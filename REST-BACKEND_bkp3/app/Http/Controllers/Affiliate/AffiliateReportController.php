<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

use DB, Auth;

use App\Models\User;
use App\Models\AdModel;
use App\Models\Device;
use App\Models\Category;
use App\Models\AffiliateUserDetail;
use App\Models\AdvertiserUserDetail;
use App\Models\OfferTracking;
use App\Models\AffiliatePostbackColumn;

use App\Http\Traits\CommonFunctions;
use App\Http\Traits\CommonOfferFunctions;

class AffiliateReportController extends Controller
{

	use CommonFunctions, CommonOfferFunctions;

	function summary()
	{

		$sql_1 = "
			SELECT 
                offers.id, 
				offers.offer_name,
				COUNT(DISTINCT(ot.tid)) as total_clicks
			FROM 
				offers 
			LEFT JOIN 
				offer_trackings as ot 
			ON 
				ot.offer_id = offers.id 
			INNER JOIN
				offer_affiliates
			ON
				offer_affiliates.offer_id  = offers.id 
			WHERE
				ot.aff_id = " . Auth::id() . " 
			GROUP BY
				offers.id
			Order By 
				total_clicks DESC
		";

		$sql_2 = "

			SELECT 
				offer_id,
				COUNT(aff_click_id) as total_conversions
			FROM
				offer_trackings
			WHERE
				aff_click_id != '' 
					AND
				aff_id = " . Auth::id() . " 

			group by
				offer_id;
		";

		$data_1 = DB::select($sql_1);
		$data_2 = DB::select($sql_2);

		$tempArr = array();

		foreach ($data_2 as $obj) {
			$tempArr[$obj->offer_id] = $obj->total_conversions;
		}//foreach

		$data = [];

		foreach ($data_1 as $item1) {

			$conversions = (isset($tempArr[$item1->id]) && $tempArr[$item1->id] > 0) ? $tempArr[$item1->id] : 0;

			$combined = (object) [
				'id' => $item1->id,
				'offer_name' => $item1->offer_name,
				'total_clicks' => $item1->total_clicks,
				'total_conversions' => $conversions
			];

			$data[] = $combined;

		}//foreach

		return response()->json([
			'module_name' => "Reports Summary",
			'data' => $data
		]);

	}//summary


	function offer_clicks(Request $request, $offer_id, $get_conversions = "")
	{
		// Always return DataTables or JSON
		$data = DB::table('offer_trackings')
			->select(
				'*',
				'offer_trackings.id as otid',
				'offer_trackings.created_at as link_clicked_at',
				'affis.id as aff_id',
				'affis.name as aff_name',
				'adverts.id as adv_id',
				'adverts.name as advert_name',
				'offer_trackings.country as ot_country',
				'offer_trackings.os as ot_os',
				'offer_trackings.browser as ot_browser'
			)
			->join('offers', 'offer_trackings.offer_id', '=', 'offers.id')
			->join('categories', 'offers.category_id', '=', 'categories.id')
			->join('devices', 'offer_trackings.device_id', '=', 'devices.id')
			->join('users as affis', 'offer_trackings.aff_id', '=', 'affis.id')
			->join('users as adverts', 'offer_trackings.adv_id', '=', 'adverts.id')
			->where('offers.id', $offer_id)
			->where('offer_trackings.created_at', date('Y-m-d'))
			->where('offer_trackings.aff_id', Auth::id());

		if ($get_conversions == "Y") {
			$data = $data->where('offer_trackings.aff_click_id', '!=', '');
		}//if

		// Return DataTable response directly which is JSON
		return DataTables::of($data)
			->addColumn('click_info', function ($row) {
				return "Click Id: {$row->tid} | Aff Click Id: {$row->aff_click_id}";
			})
			->editColumn('link_clicked_at', function ($row) {
				return date("d-m-Y H:i:s", strtotime($row->link_clicked_at));
			})
			//->rawColumns(['click_info'])
			->make(true);

	}//offer_clicks


	function search_offer_clicks(Request $request)
	{

		$data = DB::table('offer_trackings')
			->select(
				'*',
				'offer_trackings.id as otid',
				'offer_trackings.created_at as link_clicked_at',
				'affis.id as aff_id',
				'affis.name as aff_name',
				'adverts.id as adv_id',
				'adverts.name as advert_name',
				'offer_trackings.country as ot_country',
				'offer_trackings.os as ot_os',
				'offer_trackings.browser as ot_browser'
			)
			->join('offers', 'offer_trackings.offer_id', '=', 'offers.id')
			->join('categories', 'offers.category_id', '=', 'categories.id')
			->join('devices', 'offer_trackings.device_id', '=', 'devices.id')
			->join('users as affis', 'offer_trackings.aff_id', '=', 'affis.id')
			->join('users as adverts', 'offer_trackings.adv_id', '=', 'adverts.id')
			->where('offers.id', $request->offer_id)
			->where('offer_trackings.aff_id', Auth::id());

		if ($request->from != "" && $request->to != "" && $request->from == $request->to) {
			$data = $data->where('offer_trackings.created_at', $request->from);

		} elseif ($request->from != "" && $request->to != "") {
			$data = $data->whereBetween('offer_trackings.created_at', [$request->from, $request->to]);

		}//if-else


		$arrC = $this->getCountry();

		if (isset($request->country) && $request->country != "") {
			$data = $data->where('offer_trackings.country', $arrC[$request->country]);
		}

		if ($request->os != "") {
			$data = $data->where('offer_trackings.os', 'LIKE', "%{$request->os}%");
		}


		if ($request->browser != "") {
			$data = $data->where('offer_trackings.browser', $request->browser);
		}


		if ($request->device != "") {
			$data = $data->where('offer_trackings.device_id', $request->device);
		}


		if ($request->category != "") {
			$data = $data->where('offers.category_id', $request->category);
		}

		$data = $data->orderBy('offer_trackings.id', 'desc')->get(); // Get collection

		// For search, we might just want to return the data list, 
		// or we can wrap it in DataTables manually if frontend expects it.
		// Returning JSON array for now as typical API usage.

		$offer_data = DB::table('offers')->where('id', $request->offer_id)->first();

		return response()->json([
			'offer_name' => $offer_data->offer_name ?? 'Unknown',
			'data' => $data,
			'filters' => [
				'from' => $request->from,
				'to' => $request->to,
				'country' => $request->country,
				'os' => $request->os,
				'browser' => $request->browser,
				'device' => $request->device
			]
		]);

	}//search_offer_clicks


	public function offer_payouts($oid)
	{//, $aid

		$data = $this->getPayoutsForOffer($oid, Auth::id());

		$offer_name = '';
		if (count($data) > 0) {
			$dataOffer = DB::select("select offer_name from offers where id =" . $oid);
			if (strlen($dataOffer[0]->offer_name ?? '') > 0) {
				$offer_name = $dataOffer[0]->offer_name;
			}
		}

		return response()->json([
			'module_name' => "Offer Payouts",
			'offer_name' => $offer_name,
			'oid' => $oid,
			'data' => (count($data) > 0) ? $data : array()
		]);

	}//offer_payouts


	public function search_offer_payouts(Request $request)
	{

		$oid = $request->oid;
		$aid = Auth::id();

		$str = "";
		if ($request->from != "" && $request->to != "") {
			$str = " AND OT.updated_at BETWEEN '" . $request->from . "' AND '" . $request->to . "'";
		}

		$arrC = $this->getCountry();

		if (isset($request->country) && $request->country != "") {
			$str .= " AND OT.country = '" . $arrC[$request->country] . "'";
		}

		if (isset($request->browser) && $request->browser != "") {
			$str .= " AND OT.browser = '" . $request->browser . "'";
		}

		if (isset($request->os) && $request->os != "") {
			$str .= " AND OT.os = '" . $request->os . "'";
		}

		if (isset($request->device) && $request->device != "") {
			$str .= " AND OT.device_id = " . $request->device;
		}

		$data = $this->getPayoutOffersAfterSearch($oid, $str, $aid);

		$offer_name = '';
		if (count($data) > 0) {
			$dataOffer = DB::select("select offer_name from offers where id =" . $oid);
			if (strlen($dataOffer[0]->offer_name ?? '') > 0) {
				$offer_name = $dataOffer[0]->offer_name;
			}
		}

		return response()->json([
			'module_name' => "Offer Payouts",
			'offer_name' => $offer_name,
			'data' => (count($data) > 0) ? $data : array(),
			'filters' => $request->all()
		]);

	}//search_offer_payouts

	public function payouts()
	{//, $oid

		$aid = Auth::id();
		$data = $this->getAffiliatePayouts($aid);

		return response()->json([
			'module_name' => "Affiliate Payouts",
			'data' => $data
		]);

	}//affiliate_payouts


	public function search_affiliate_payouts(Request $request)
	{

		$aid = Auth::id();

		$str = "";
		if ($request->from != "" && $request->to != "") {
			$str = " AND OT.updated_at BETWEEN '" . $request->from . "' AND '" . $request->to . "'";
		}

		$arrC = $this->getCountry();

		if (isset($request->country) && $request->country != "") {
			$str .= " AND OT.country = '" . $arrC[$request->country] . "'";
		}

		if (isset($request->browser) && $request->browser != "") {
			$str .= " AND OT.browser = '" . $request->browser . "'";
		}

		if (isset($request->os) && $request->os != "") {
			$str .= " AND OT.os = '" . $request->os . "'";
		}

		if (isset($request->device) && $request->device != "") {
			$str .= " AND OT.device_id = " . $request->device;
		}

		$data = $this->getAffiliatePayoutsAfterSearch($aid, $str);

		return response()->json([
			'module_name' => "Affiliate Payouts",
			'data' => $data,
			'filters' => $request->all()
		]);

	}//search_affiliate_payouts

}//AffiliateReportController
