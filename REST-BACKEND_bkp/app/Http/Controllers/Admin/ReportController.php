<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

use DB, Auth;

use App\Models\User;

use App\Models\AdModel;
use App\Models\Device;
use App\Models\Category;

use App\Models\Transaction;

use App\Models\AffiliateUserDetail;
use App\Models\AdvertiserUserDetail;
use App\Models\OfferTracking;
use App\Http\Traits\CommonFunctions;
use App\Http\Traits\CommonOfferFunctions;



class ReportController extends Controller
{

	use CommonFunctions, CommonOfferFunctions;



	public function list_transactions(Request $request)
	{

		try {

			$data = DB::table('subscriptions')
				->join('transactions', 'subscriptions.id', '=', 'transactions.subscription_id')
				->select('*')
				->get()->toArray();

			return response()->json([
				'module_name' => "Payment Transactions",
				'data' => $data
			]);

		} catch (\Exception $e) {
			return response()->json(['error' => $e->getMessage()], 500);
		}


	}//list_transactions



	function logs()
	{


		$data = DB::table('logs')
			->select(
				'offers.id as offer_id',
				'offers.offer_name',
				'logs.module',
				'logs.description',
				'logs.created_at as logs_created_at',
				'affis.id as aff_id',
				'affis.name as affiliate_name'
			)
			->join('offers', 'logs.offer_id', '=', 'offers.id')
			->join('users as affis', 'logs.aff_id', '=', 'affis.id')
			->orderBy('logs.id', 'desc')
			->get();

		return response()->json([
			'module_name' => "Logs",
			'data' => $data
		]);

	}//logs

	function all_offers()
	{


		//DB::enableQueryLog();

		$data = DB::table('offers')
			->join('offer_trackings as ot', 'ot.offer_id', '=', 'offers.id')
			->select(
				'offers.id',
				'offers.offer_name',
				DB::raw("SUM(CASE WHEN ot.tid != '' THEN 1 ELSE 0 END) as total_clicks"),
				DB::raw("SUM(CASE WHEN ot.aff_click_id != '' THEN 1 ELSE 0 END) as total_conversions")
			)
			->groupBy('offers.id', 'offers.offer_name')
			->orderByRaw('total_clicks DESC')
			->paginate(50);

		//\Log::info(DB::getQueryLog());

		return response()->json([
			'module_name' => "Clicks Report",
			'data' => $data
		]);

	}//all_offers


	public function offer_clicks(Request $request, $offer_id, $get_conversions = "N")
	{

		//if ($request->ajax()) {
		$data = DB::table('offer_trackings')
			->select([
				'*',
				'offer_trackings.id as otid',
				'offer_trackings.created_at as link_clicked_at',
				'offer_trackings.sub1',
				'affis.id as aff_id',
				'affis.name as aff_name',
				'adverts.id as adv_id',
				'adverts.name as advert_name',
				'offer_trackings.country as ot_country',
				'offer_trackings.os as ot_os',
				'offer_trackings.browser as ot_browser'
			])
			->join('offers', 'offer_trackings.offer_id', '=', 'offers.id')
			->join('categories', 'offers.category_id', '=', 'categories.id')
			->join('devices', 'offer_trackings.device_id', '=', 'devices.id')
			->join('users as affis', 'offer_trackings.aff_id', '=', 'affis.id')
			->join('users as adverts', 'offer_trackings.adv_id', '=', 'adverts.id')
			->where('offers.id', $offer_id);

		if ($get_conversions == "Y") {
			$data = $data->where('offer_trackings.aff_click_id', '!=', '');
		}


		return DataTables::of($data)
			->addColumn('click_info', function ($row) {
				return "Click Id: {$row->tid} | Aff Click Id: {$row->aff_click_id}";
			})
			->editColumn('link_clicked_at', function ($row) {
				return date("d-m-Y H:i:s", strtotime($row->link_clicked_at));
			})
			//->rawColumns(['click_info'])
			->make(true);
		//}

	}//offer_clicks



	function search_offer_clicks(Request $request)
	{



		$data = DB::table('offer_trackings')
			->select(
				'*',
				'offer_trackings.id as otid',
				'offer_trackings.created_at as link_clicked_at',
				'offer_trackings.sub1',
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
			->where('offers.id', $request->offer_id);

		if ($request->from != "" && $request->to != "" && $request->from != $request->to) {
			$data = $data->whereBetween('offer_trackings.created_at', [$request->from, $request->to]);

		} elseif ($request->from != "" && $request->to != "" && $request->from == $request->to) {
			$data = $data->where('offer_trackings.created_at', $request->from);

		}


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

		$data = $data->orderBy('offer_trackings.id', 'desc')->get();

		$offer_name = '';
		$offer_data = DB::table('offers')->where('id', $request->offer_id)->first();
		if ($offer_data) {
			$offer_name = $offer_data->offer_name;
		}

		return response()->json([
			'module_name' => "Offer Clicks Search",
			'offer_name' => $offer_name,
			'data' => $data,
			'filters' => $request->all()
		]);

	}//search_offer_clicks


	public function all_payouts()
	{


		$data = DB::select('select OT.aff_id, U.name as affiliate_name, O.id as offer_id, O.offer_name, count(*) as total_conversions, OT.sub1, OT.revenue as revenue, OT.payout as payout, OT.currency as ot_currency, SUM(OT.revenue) as total_revenue, SUM(OT.payout) as total_payout, OE.event_name from offers O LEFT JOIN offer_trackings OT ON O.id = OT.offer_id LEFT JOIN users U ON OT.aff_id = U.id LEFT JOIN payout_rules PR ON OT.event_id = PR.event_id LEFT JOIN offer_events OE ON PR.event_id = OE.id where OT.aff_click_id = OT.tid group by OT.offer_id, OT.aff_id, OT.event_id, OT.sub1, OT.revenue, OT.payout, OT.currency');


		return response()->json([
			'module_name' => "Payouts Report",
			'data' => $data
		]);


	}//all_payouts


	public function search_all_payouts(Request $request)
	{


		$str = "";
		if ($request->from != "" && $request->to != "") {
			$str = " AND OT.updated_at BETWEEN '" . $request->from . "' AND '" . $request->to . "'";
		}

		//and OT.updated_at BETWEEN '".$request->from."' AND '".$request->to."'

		$data = DB::select("select OT.aff_id, U.name as affiliate_name, O.id as offer_id, O.offer_name, count(*) as total_conversions, OT.sub1, OT.revenue as revenue, OT.payout as payout, OT.currency as ot_currency, SUM(OT.revenue) as total_revenue, SUM(OT.payout) as total_payout, OE.event_name from offers O LEFT JOIN offer_trackings OT ON O.id = OT.offer_id LEFT JOIN users U ON OT.aff_id = U.id LEFT JOIN payout_rules PR ON OT.event_id = PR.event_id LEFT JOIN offer_events OE ON PR.event_id = OE.id where OT.aff_click_id = OT.tid " . $str . " group by OT.offer_id, OT.aff_id, OT.event_id");


		return response()->json([
			'module_name' => "All Payouts",
			'data' => $data,
			'filters' => $request->all()
		]);


	}//search_all_payouts


	public function offer_payouts($oid)
	{//, $aid	

		$data = $this->getPayoutsForOffer($oid);

		$of_name = '';

		if (count($data) > 0) {

			$dataOffer = DB::select("select offer_name from offers where id =" . $oid);

			if (strlen($dataOffer[0]->offer_name) > 0) {
				$of_name = $dataOffer[0]->offer_name;
			}//if

		}//if

		return response()->json([
			'module_name' => "Offer Payouts",
			'offer_name' => $of_name,
			'oid' => $oid,
			'data' => (count($data) > 0) ? $data : array()
		]);

	}//offer_payouts

	public function search_offer_payouts(Request $request)
	{

		$oid = $request->oid;
		//$aid = $request->aid;

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



		$data = $this->getPayoutOffersAfterSearch($oid, $str);


		$of_name = '';

		if (count($data) > 0) {

			$dataOffer = DB::select("select offer_name from offers where id =" . $oid);

			if (strlen($dataOffer[0]->offer_name) > 0) {
				$of_name = $dataOffer[0]->offer_name;
			}//if

		}//if


		return response()->json([
			'module_name' => "Offer Payouts",
			'offer_name' => $of_name,
			'data' => (count($data) > 0) ? $data : array(),
			'filters' => $request->all()
		]);


	}//search_offer_payouts

	public function affiliate_payouts($aid)
	{//, $oid


		$data = $this->getAffiliatePayouts($aid);


		$af_name = '';

		if (count($data) > 0) {

			$dataOffer = DB::select("select name from users where id =" . $aid);

			if (strlen($dataOffer[0]->name) > 0) {
				$af_name = $dataOffer[0]->name;
			}//if

		}//if


		return response()->json([
			'module_name' => "Affiliate Payouts",
			'affiliate_name' => $af_name,
			'aid' => $aid,
			'data' => $data
		]);


	}//affiliate_payouts


	public function search_affiliate_payouts(Request $request)
	{

		$aid = $request->aid;

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


		$af_name = '';

		if (count($data) > 0) {

			$dataOffer = DB::select("select name from users where id =" . $aid);

			if (strlen($dataOffer[0]->name) > 0) {
				$af_name = $dataOffer[0]->name;
			}//if

		}//if



		return response()->json([
			'module_name' => "Affiliate Payouts",
			'affiliate_name' => $af_name,
			'data' => (count($data) > 0) ? $data : array(),
			'filters' => $request->all()
		]);

	}//search_affiliate_payouts


	public function get_offers_assigned_to_affiliate($aid)
	{

		try {

			$offers = $this->getOffersAssignedToAffiliate($aid);

			return response()->json([
				'module_name' => 'Offers Approved',
				'offers' => $offers
			]);


		} catch (\Exception $e) {

			return response()->json(['error' => $e->getMessage()], 500);

		}

	}//getOffersAssignedToAffiliate


}//ReportController
