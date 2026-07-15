<?php

namespace App\Services\Affiliate;

use App\Models\OfferTracking;
use App\Models\AffiliatePostbackColumn;
use App\Services\Affiliate\AffiliateDashboardService;
use App\Services\Reports\BaseReportService;
use App\DTOs\ReportFilterDTO;
use App\Http\Traits\CommonOfferFunctions;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AffiliateReportService extends BaseReportService
{
    use CommonOfferFunctions, \App\Http\Traits\TimezoneHelper;

    /**
     * Get summary data for affiliate
     * 
     * @param int $aid Affiliate ID
     * @return array
     */
    public function getSummaryData($aid)
    {
        $sql_1 = "
			SELECT 
                offers.id, 
				offers.offer_name,
				COUNT(DISTINCT(ot.tid)) as total_clicks
			FROM 
				offers 
			INNER JOIN
				offer_affiliates
			ON
				offer_affiliates.offer_id  = offers.id 
			LEFT JOIN 
				offer_trackings as ot 
			ON 
				ot.offer_id = offers.id AND ot.aff_id = " . $aid . "
			WHERE
				offer_affiliates.affiliate_id = " . $aid . " 
			GROUP BY
				offers.id, offers.offer_name
			Order By 
				total_clicks DESC
		";

        $sql_2 = "
			SELECT 
				offer_id,
				COUNT(*) as total_conversions
			FROM
				conversions
			WHERE
				affiliate_id = " . $aid . " 
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
            $data[] = (object)[
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

    /**
     * Get click and conversion report for affiliate (wrapper method)
     * 
     * @param Request $request
     * @param int $aid Affiliate ID
     * @return array
     */
    public function getClickConversionReportForAffiliate($request, $aid): array
    {
        // Create filter DTO from request
        $filters = ReportFilterDTO::fromRequest($request);

        // Use base class implementation with affiliate scoping
        return parent::getClickConversionReport($filters, $aid, 'affiliate');
    }

    /**
     * Get offer clicks (legacy method for DataTables)
     */
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
            ->whereBetween('offer_trackings.created_at', [
                $this->getUtcStartOfDay(\Carbon\Carbon::now($this->getUserTimezone())->format('Y-m-d')),
                $this->getUtcEndOfDay(\Carbon\Carbon::now($this->getUserTimezone())->format('Y-m-d'))
            ])
            ->where('offer_trackings.aff_id', $aid);

        if ($get_conversions == "Y") {
            $data = $data->where('offer_trackings.aff_click_id', '!=', '');
        }

        return DataTables::of($data)
            ->addColumn('click_info', function ($row) {
            return "Click Id: {$row->tid} | Aff Click Id: {$row->aff_click_id}";
        })
            ->editColumn('link_clicked_at', function ($row) {
            return $this->formatUtcToLocal($row->link_clicked_at);
        })
            ->make(true);
    }

    /**
     * Search offer clicks (legacy method - can be refactored to use BaseReportService)
     */
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
            $data = $data->whereBetween('offer_trackings.created_at', [$this->getUtcStartOfDay($request->from), $this->getUtcEndOfDay($request->to)]);
        }
        elseif ($request->from != "" && $request->to != "") {
            $data = $data->whereBetween('offer_trackings.created_at', [$this->getUtcStartOfDay($request->from), $this->getUtcEndOfDay($request->to)]);
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
        foreach($data as $row) {
            $row->link_clicked_at = $this->formatUtcToLocal($row->link_clicked_at);
        }
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

    /**
     * Get offer payouts
     */
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

    /**
     * Search offer payouts
     */
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

    /**
     * Get affiliate payouts
     */
    public function getAffiliatePayouts($aid)
    {
        return [
            'module_name' => "Affiliate Payouts",
            'data' => $this->getPayoutsForAffiliate($aid)
        ];
    }

    /**
     * Get payouts for affiliate (helper method)
     */
    public function getPayoutsForAffiliate($aid)
    {
        $sql = "SELECT 
					C.id as conv_id,
					O.offer_name, 
					M.name as model_name, 
					C.payout as price,
					C.currency as ot_currency,
					C.browser as ot_browser,
					C.created_at as date_updated,
					C.*
				FROM 
					offers O 
						LEFT JOIN ad_models M ON CAST(NULLIF(O.ad_model, '') AS BIGINT) = M.id
						INNER JOIN conversions C ON O.id = C.offer_id
				WHERE 
				 	C.affiliate_id = " . $aid . "
                ORDER BY
                    C.id DESC";

        $results = DB::select($sql);
        foreach ($results as $row) {
            $row->date_updated = $this->formatUtcToLocal($row->date_updated);
            $row->created_at = $this->formatUtcToLocal($row->created_at);
            $row->updated_at = $this->formatUtcToLocal($row->updated_at);
        }
        return $results;
    }

    /**
     * Search affiliate payouts
     */
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

    /**
     * Get clicks report for affiliate (from offer_trackings)
     */
    public function getClicksReport($request, $aid): array
    {
        $query = DB::table('offer_trackings')
            ->select(
            'offer_trackings.*',
            'offer_trackings.id as otid',
            'offer_trackings.created_at as link_clicked_at',
            'offer_trackings.country as ot_country',
            'offer_trackings.os as ot_os',
            'offer_trackings.browser as ot_browser',
            'offers.id as offer_id',
            'offers.offer_name',
            'devices.device_name'
        )
            ->leftJoin('offers', 'offer_trackings.offer_id', '=', 'offers.id')
            ->leftJoin('devices', 'offer_trackings.device_id', '=', 'devices.id')
            ->where('offer_trackings.aff_id', $aid);

        if ($request->from && $request->to) {
            $query->whereBetween('offer_trackings.created_at', [
                $this->getUtcStartOfDay($request->from), 
                $this->getUtcEndOfDay($request->to)
            ]);
        }
        if ($request->offer_id) {
            $query->where('offers.id', $request->offer_id);
        }

        $limit = $request->limit ?? 50;
        $results = $query->orderBy('offer_trackings.id', 'desc')->paginate($limit);
        
        $results->getCollection()->transform(function ($row) {
            return $this->formatClickRow($row, Auth::user());
        });

        return [
            'module_name' => 'Clicks Report',
            'data' => $results,
            'filters' => $request->all()
        ];
    }

    /**
     * Get conversions report for affiliate (from conversions table)
     */
    public function getConversionsReport($request, $aid): array
    {
        $query = DB::table('conversions')
            ->select(
                'conversions.*',
                'conversions.id as conv_id',
                'conversions.created_at as conversion_time',
                'conversions.country',
                'conversions.device',
                'conversions.event',
                'conversions.payout',
                'offers.id as offer_id',
                'offers.offer_name',
                'ot.created_at as ot_click_time',
                'ot.ip as ot_click_ip'
            )
            ->leftJoin('offers', 'conversions.offer_id', '=', 'offers.id')
            ->leftJoin('offer_trackings as ot', 'conversions.tid', '=', 'ot.tid')
            ->where('conversions.affiliate_id', $aid);

        if ($request->from && $request->to) {
            $query->whereBetween('conversions.created_at', [
                $this->getUtcStartOfDay($request->from), 
                $this->getUtcEndOfDay($request->to)
            ]);
        }
        if ($request->offer_id) {
            $query->where('conversions.offer_id', $request->offer_id);
        }

        $limit = $request->limit ?? 50;
        $results = $query->orderBy('conversions.id', 'desc')->paginate($limit);

        $results->getCollection()->transform(function ($row) {
            return $this->formatConversionRow($row, Auth::user());
        });

        return [
            'module_name' => 'Conversions Report',
            'data' => $results,
            'filters' => $request->all()
        ];
    }
}
