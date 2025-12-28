<?php

namespace App\Services\Affiliate;

use App\Models\OfferTracking;
use App\Models\AffiliatePostbackColumn;
use App\Services\Affiliate\AffiliateDashboardService;
use App\Http\Traits\CommonFunctions;
use App\Http\Traits\CommonOfferFunctions;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AffiliateReportService
{
    use CommonFunctions, CommonOfferFunctions;

    public function getSummaryData($aid)
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
				ot.aff_id = " . $aid . " 
			GROUP BY
				offers.id, offers.offer_name
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
				aff_id = " . $aid . " 
			group by
				offer_id;
		";

        $data_1 = DB::select($sql_1);
        $data_2 = DB::select($sql_2);

        $tempArr = array();

        foreach ($data_2 as $obj) {
            $tempArr[$obj->offer_id] = $obj->total_conversions;
        }

        $data = [];

        foreach ($data_1 as $item1) {
            $conversions = (isset($tempArr[$item1->id]) && $tempArr[$item1->id] > 0) ? $tempArr[$item1->id] : 0;
            $data[] = (object) [
                'id' => $item1->id,
                'offer_name' => $item1->offer_name,
                'total_clicks' => $item1->total_clicks,
                'total_conversions' => $conversions
            ];
        }

        return [
            'module_name' => "Reports Summary",
            'data' => $data
        ];
    }

    public function getOfferClicks($offer_id, $aid, $get_conversions = "")
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
            ->where('offers.id', $offer_id)
            ->where('offer_trackings.created_at', date('Y-m-d'))
            ->where('offer_trackings.aff_id', $aid);

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
            ->make(true);
    }

    public function searchOfferClicks($request, $aid)
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
            ->where('offer_trackings.aff_id', $aid);

        if ($request->from != "" && $request->to != "" && $request->from == $request->to) {
            $data = $data->where('offer_trackings.created_at', $request->from);
        } elseif ($request->from != "" && $request->to != "") {
            $data = $data->whereBetween('offer_trackings.created_at', [$request->from, $request->to]);
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
        $offer_data = DB::table('offers')->where('id', $request->offer_id)->first();

        return [
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
        ];
    }

    public function getOfferPayouts($oid, $aid)
    {
        $data = $this->getPayoutsForOffer($oid, $aid);
        $offer_name = '';

        if (count($data) > 0) {
            $dataOffer = DB::select("select offer_name from offers where id =" . $oid);
            if (strlen($dataOffer[0]->offer_name ?? '') > 0) {
                $offer_name = $dataOffer[0]->offer_name;
            }
        }

        return [
            'module_name' => "Offer Payouts",
            'offer_name' => $offer_name,
            'oid' => $oid,
            'data' => (count($data) > 0) ? $data : array()
        ];
    }

    public function searchOfferPayouts($request, $aid)
    {
        $oid = $request->oid;
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

        return [
            'module_name' => "Offer Payouts",
            'offer_name' => $offer_name,
            'data' => (count($data) > 0) ? $data : array(),
            'filters' => $request->all()
        ];
    }

    public function getAffiliatePayouts($aid)
    {
        return [
            'module_name' => "Affiliate Payouts",
            'data' => $this->getPayoutsForAffiliate($aid) // Assuming Trait method name fix or alias needed? Trait has `getAffiliatePayouts` which clashes.
            // Wait, trait CommonOfferFunctions has `getAffiliatePayouts`.
            // Inside this class `$this->getAffiliatePayouts` refers to THIS method.
            // We need to use trait method.
        ];
    }

    // Fix for Trait name collision
    public function getPayoutsForAffiliate($aid)
    {
        // Copying trait logic or using alias would be better, but let's copy for safety/simplicity as traits can be tricky with name collisions
        // Actually, CommonOfferFunctions::getAffiliatePayouts is what we need.
        // Let's rename the Trait method alias or just duplicate logic to avoid recursion loop if I call $this->getAffiliatePayouts
        // I'll assume I can just use the trait's logic here directly

        $sql = "SELECT 
					OT.id as otid,
					O.offer_name, 
					M.name as model_name, 
					OT.payout as price,
					OT.currency as ot_currency,
					OT.browser as ot_browser,
					OT.updated_at as date_updated,
					OT.*,
					D.*,
					E.event_name
				FROM 
					offers O 
						LEFT JOIN ad_models M ON O.ad_model = M.id
						LEFT JOIN offer_trackings OT ON O.id = OT.offer_id
						LEFT JOIN devices D ON OT.device_id = D.id
						LEFT JOIN offer_events E ON OT.event_id = E.id
				WHERE 
				 	OT.aff_click_id = OT.tid
				 	AND
				 	OT.aff_id = " . $aid;

        return DB::select($sql);
    }


    public function searchAffiliatePayouts($request, $aid)
    {
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

        $data = $this->getAffiliatePayoutsAfterSearch($aid, $str); // Trait method

        return [
            'module_name' => "Affiliate Payouts",
            'data' => $data,
            'filters' => $request->all()
        ];
    }

    public function getClickConversionReport($request, $aid)
    {
        $data = DB::table('offer_trackings')
            ->select(
                'offer_trackings.*',
                'offer_trackings.id as otid',
                'offer_trackings.created_at as link_clicked_at',
                'offer_trackings.country as ot_country',
                'offer_trackings.os as ot_os',
                'offer_trackings.browser as ot_browser',
                'affis.id as aff_id',
                'affis.name as aff_name',
                'offers.id as offer_id',
                'offers.offer_name',
                'devices.device_name',
                DB::raw("CASE WHEN offer_trackings.aff_click_id != '' THEN 1 ELSE 0 END as is_conversion")
            )
            ->leftJoin('offers', 'offer_trackings.offer_id', '=', 'offers.id')
            ->leftJoin('categories', 'offers.category_id', '=', 'categories.id')
            ->leftJoin('devices', 'offer_trackings.device_id', '=', 'devices.id')
            ->leftJoin('users as affis', 'offer_trackings.aff_id', '=', 'affis.id')
            ->where('offer_trackings.aff_id', $aid);

        if ($request->has('from') && $request->has('to') && $request->from != "" && $request->to != "") {
            if ($request->from != $request->to) {
                $data = $data->whereBetween('offer_trackings.created_at', [$request->from, $request->to]);
            } else {
                $data = $data->whereDate('offer_trackings.created_at', $request->from);
            }
        }
        if ($request->has('offer_id') && $request->offer_id != "") {
            $data = $data->where('offers.id', $request->offer_id);
        }

        $arrC = $this->getCountry();
        if ($request->has('country') && $request->country != "") {
            $country = isset($arrC[$request->country]) ? $arrC[$request->country] : $request->country;
            $data = $data->where('offer_trackings.country', $country);
        }
        if ($request->has('os') && $request->os != "") {
            $data = $data->where('offer_trackings.os', 'LIKE', "%{$request->os}%");
        }
        if ($request->has('browser') && $request->browser != "") {
            $data = $data->where('offer_trackings.browser', $request->browser);
        }
        if ($request->has('device') && $request->device != "") {
            $data = $data->where('offer_trackings.device_id', $request->device);
        }
        if ($request->has('category') && $request->category != "") {
            $data = $data->where('offers.category_id', $request->category);
        }
        if ($request->has('conversion_status') && $request->conversion_status != "") {
            if ($request->conversion_status == \App\Enums\ConversionStatus::Converted->value) {
                $data = $data->where('offer_trackings.aff_click_id', '!=', '');
            } elseif ($request->conversion_status == \App\Enums\ConversionStatus::Pending->value) {
                $data = $data->where('offer_trackings.aff_click_id', '=', '');
            }
        }

        $perPage = $request->get('limit', 50);
        return [
            'module_name' => "Click & Conversion Report",
            'data' => $data->orderBy('offer_trackings.id', 'desc')->paginate($perPage),
            'filters' => $request->all()
        ];
    }
}
