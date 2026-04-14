<?php

namespace App\Services\Admin;

use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Services\Reports\BaseReportService;
use App\DTOs\ReportFilterDTO;
use App\Http\Traits\CommonOfferFunctions;

class AdminReportService extends BaseReportService
{
    use CommonOfferFunctions {
        getOffersAssignedToAffiliate as traitGetOffersAssignedToAffiliate;
    }

    /**
     * List payment transactions
     */
    public function listTransactions()
    {
        $data = DB::table('subscriptions')
            ->join('transactions', 'subscriptions.id', '=', 'transactions.subscription_id')
            ->select('*')
            ->get()->toArray();

        return [
            'module_name' => "Payment Transactions",
            'data' => $data
        ];
    }

    /**
     * Get subscription payment details
    */
    public function getSubscriptionPaymentDetails($sub_id)
    {
        $data = DB::table('subscriptions')
            ->join('transactions', 'subscriptions.id', '=', 'transactions.subscription_id')
            ->select('*')
            ->where('subscriptions.id', '=', $sub_id)
            ->get()->toArray();

        return [
            'module_name' => "",
            'data' => $data
        ];
    }





    /**
     * List logs
     */
    public function listLogs()
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

        return [
            'module_name' => "Logs",
            'data' => $data
        ];
    }

    /**
     * Get Affiliate Postback Logs
     */
    public function getAffiliatePostbackLogs($request)
    {
        $query = DB::table('affiliate_postback_logs as apl')
            ->select(
                'apl.id',
                'apl.conversion_id',
                'apl.url_fired',
                'apl.http_status',
                'apl.response_body',
                'apl.created_at',
                'affis.name as affiliate_name'
            )
            ->join('users as affis', 'apl.affiliate_id', '=', 'affis.id');

        // Can easily add filters here later if needed

        $data = $query->orderBy('apl.id', 'desc')->paginate(50);

        return [
            'module_name' => "Affiliate Postback Logs",
            'data' => $data
        ];
    }

    /**
     * Get offers assigned to affiliate
     */
    public function getOffersAssignedToAffiliate($aid)
    {
        $offers = $this->traitGetOffersAssignedToAffiliate($aid);

        return [
            'module_name' => 'Offers Approved',
            'offers' => $offers
        ];
    }

    /**
     * Get all offers with click/conversion stats
     */
    public function allOffers()
    {
        // Clicks from offer_trackings, conversions from conversions table
        $data = DB::table('offers')
            ->leftJoin('offer_trackings as ot', 'ot.offer_id', '=', 'offers.id')
            ->leftJoin('conversions as conv', 'conv.offer_id', '=', 'offers.id')
            ->select(
                'offers.id',
                'offers.offer_name',
                DB::raw("COUNT(DISTINCT ot.tid) as total_clicks"),
                DB::raw("COUNT(DISTINCT conv.id) as total_conversions")
            )
            ->groupBy('offers.id', 'offers.offer_name')
            ->orderByRaw('total_clicks DESC')
            ->paginate(50);

        return [
            'module_name' => "Clicks Report",
            'data' => $data
        ];
    }

    /**
     * Get offer clicks (legacy DataTables method)
     */
    public function getOfferClicks($offer_id, $get_conversions)
    {
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
            ->make(true);
    }

    /**
     * Search offer clicks (legacy method - can be refactored)
     */
    public function searchOfferClicks($request)
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

        return [
            'module_name' => "Offer Clicks Search",
            'offer_name' => $offer_name,
            'data' => $data,
            'filters' => $request->all()
        ];
    }

    /**
     * Get click and conversion report (uses BaseReportService)
     * 
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function getClickConversionReportForAdmin($request): array
    {
        // Create filter DTO from request
        $filters = ReportFilterDTO::fromRequest($request);

        // Use base class implementation without scoping (admin sees all data)
        return parent::getClickConversionReport($filters, null, null);
    }

    /**
     * Get all payouts (from conversions table)
     */
    public function allPayouts()
    {
        $data = DB::table('conversions as C')
            ->leftJoin('offers as O', 'C.offer_id', '=', 'O.id')
            ->leftJoin('users as U', 'C.affiliate_id', '=', 'U.id')
            ->select(
                'C.id',
                'C.affiliate_id as aid',
                'U.name as affiliate_name',
                'C.offer_id as oid',
                'O.offer_name',
                'C.payout as amount',
                'C.status',
                'C.currency',
                'C.created_at'
            )
            ->orderBy('C.created_at', 'desc')
            ->get();

        return [
            'module_name' => "Payouts Report",
            'data' => $data
        ];
    }

    /**
     * Search all payouts (from conversions table)
     */
    public function searchAllPayouts($request)
    {
        $query = DB::table('conversions as C')
            ->leftJoin('offers as O', 'C.offer_id', '=', 'O.id')
            ->leftJoin('users as U', 'C.affiliate_id', '=', 'U.id')
            ->select(
                'C.id',
                'C.affiliate_id as aid',
                'U.name as affiliate_name',
                'C.offer_id as oid',
                'O.offer_name',
                'C.payout as amount',
                'C.currency',
                'C.created_at'
            );

        if ($request->from && $request->to) {
            $fromUtc = $this->getUtcStartOfDay($request->from);
            $toUtc   = $this->getUtcEndOfDay($request->to);
            $query->whereBetween('C.created_at', [$fromUtc, $toUtc]);
        }

        $data = $query->orderBy('C.created_at', 'desc')->get();

        return [
            'module_name' => "All Payouts",
            'data' => $data,
            'filters' => $request->all()
        ];
    }

    /**
     * Get offer payouts
     */
    public function offerPayouts($oid)
    {
        $data = $this->getPayoutsForOffer($oid);
        $of_name = '';

        if (count($data) > 0) {
            $dataOffer = DB::select("select offer_name from offers where id =" . $oid);
            if (strlen($dataOffer[0]->offer_name) > 0) {
                $of_name = $dataOffer[0]->offer_name;
            }
        }

        return [
            'module_name' => "Offer Payouts",
            'offer_name' => $of_name,
            'oid' => $oid,
            'data' => (count($data) > 0) ? $data : array()
        ];
    }

    /**
     * Search offer payouts
     */
    public function searchOfferPayouts($request)
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

        $data = $this->getPayoutOffersAfterSearch($oid, $str);
        $of_name = '';

        if (count($data) > 0) {
            $dataOffer = DB::select("select offer_name from offers where id =" . $oid);
            if (strlen($dataOffer[0]->offer_name) > 0) {
                $of_name = $dataOffer[0]->offer_name;
            }
        }

        return [
            'module_name' => "Offer Payouts",
            'offer_name' => $of_name,
            'data' => (count($data) > 0) ? $data : array(),
            'filters' => $request->all()
        ];
    }

    /**
     * Get affiliate payouts
     */
    public function affiliatePayouts($aid)
    {
        $data = $this->getAffiliatePayouts($aid);
        $af_name = '';

        if (count($data) > 0) {
            $dataOffer = DB::select("select name from users where id =" . $aid);
            if (strlen($dataOffer[0]->name) > 0) {
                $af_name = $dataOffer[0]->name;
            }
        }

        return [
            'module_name' => "Affiliate Payouts",
            'affiliate_name' => $af_name,
            'aid' => $aid,
            'data' => $data
        ];
    }

    /**
     * Search affiliate payouts
     */
    public function searchAffiliatePayouts($request)
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
            }
        }

        return [
            'module_name' => "Affiliate Payouts",
            'affiliate_name' => $af_name,
            'data' => (count($data) > 0) ? $data : array(),
            'filters' => $request->all()
        ];
    }

    /**
     * Get clicks report (from offer_trackings only)
     */
    public function getClicksReport($request): array
    {
        $query = DB::table('offer_trackings')
            ->select(
                'offer_trackings.*',
                'offer_trackings.id as otid',
                'offer_trackings.created_at as link_clicked_at',
                'offer_trackings.country as ot_country',
                'offer_trackings.os as ot_os',
                'offer_trackings.browser as ot_browser',
                'affis.id as aff_id',
                'affis.name as aff_name',
                'adverts.id as adv_id',
                'adverts.name as advert_name',
                'offers.id as offer_id',
                'offers.offer_name',
                'devices.device_name'
            )
            ->leftJoin('offers', 'offer_trackings.offer_id', '=', 'offers.id')
            ->leftJoin('devices', 'offer_trackings.device_id', '=', 'devices.id')
            ->leftJoin('users as affis', 'offer_trackings.aff_id', '=', 'affis.id')
            ->leftJoin('users as adverts', 'offer_trackings.adv_id', '=', 'adverts.id');

        // Apply filters
        if ($request->from && $request->to) {
            $fromUtc = $this->getUtcStartOfDay($request->from);
            $toUtc   = $this->getUtcEndOfDay($request->to);
            $query->whereBetween('offer_trackings.created_at', [$fromUtc, $toUtc]);
        }
        if ($request->offer_id) {
            $query->where('offers.id', $request->offer_id);
        }
        if ($request->affiliate_id) {
            $query->where('offer_trackings.aff_id', $request->affiliate_id);
        }
        if ($request->advertiser_id) {
            $query->where('offers.advertiser_id', $request->advertiser_id);
        }

        $limit = $request->limit ?? 50;
        $results = $query->orderBy('offer_trackings.id', 'desc')->paginate($limit);

        // Convert UTC timestamps to user's configured timezone
        $results->getCollection()->transform(function ($row) {
            if (isset($row->link_clicked_at)) $row->link_clicked_at = $this->formatUtcToLocal($row->link_clicked_at);
            if (isset($row->created_at)) $row->created_at = $this->formatUtcToLocal($row->created_at);
            return $row;
        });

        return [
            'module_name' => 'Clicks Report',
            'data' => $results,
            'filters' => $request->all()
        ];
    }

    /**
     * Get conversions report (from conversions table)
     */
    public function getConversionsReport($request): array
    {
        $query = DB::table('conversions')
            ->select(
                'conversions.*',
                'conversions.id as conv_id',
                'conversions.created_at as conversion_time',
                'conversions.country',
                'conversions.device',
                'conversions.event',
                'conversions.status',
                'conversions.revenue',
                'conversions.payout',
                'affis.id as affiliate_id',
                'affis.name as affiliate_name',
                'adverts.id as advertiser_id',
                'adverts.name as advertiser_name',
                'offers.id as offer_id',
                'offers.offer_name'
            )
            ->leftJoin('offers', 'conversions.offer_id', '=', 'offers.id')
            ->leftJoin('users as affis', 'conversions.affiliate_id', '=', 'affis.id')
            ->leftJoin('users as adverts', 'conversions.advertiser_id', '=', 'adverts.id');

        // Apply filters
        if ($request->from && $request->to) {
            $fromUtc = $this->getUtcStartOfDay($request->from);
            $toUtc   = $this->getUtcEndOfDay($request->to);
            $query->whereBetween('conversions.created_at', [$fromUtc, $toUtc]);
        }
        if ($request->offer_id) {
            $query->where('conversions.offer_id', $request->offer_id);
        }
        if ($request->affiliate_id) {
            $query->where('conversions.affiliate_id', $request->affiliate_id);
        }
        if ($request->advertiser_id) {
            $query->where('conversions.advertiser_id', $request->advertiser_id);
        }
        if ($request->status) {
            $query->where('conversions.status', $request->status);
        }

        $limit = $request->limit ?? 50;
        $results = $query->orderBy('conversions.id', 'desc')->paginate($limit);

        // Convert UTC timestamps to user's configured timezone
        $results->getCollection()->transform(function ($row) {
            if (isset($row->conversion_time)) $row->conversion_time = $this->formatUtcToLocal($row->conversion_time);
            if (isset($row->created_at)) $row->created_at = $this->formatUtcToLocal($row->created_at);
            return $row;
        });

        return [
            'module_name' => 'Conversions Report',
            'data' => $results,
            'filters' => $request->all()
        ];
    }

    /**
     * Update conversion status (confirm or decline)
     */
    public function updateConversionStatus($id, $status): array
    {
        $conversion = \App\Models\Conversion::find($id);

        if (!$conversion) {
            return ['success' => false, 'message' => 'Conversion not found'];
        }

        $conversion->status = $status;
        $conversion->save();

        if ($status === 'confirmed') {
            \App\Jobs\FireAffiliatePostbackJob::dispatch($conversion->id);
        }

        return ['success' => true, 'message' => 'Conversion status updated to ' . $status];
    }
}

