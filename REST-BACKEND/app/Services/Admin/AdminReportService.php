<?php

namespace App\Services\Admin;

use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Services\Reports\BaseReportService;
use App\DTOs\ReportFilterDTO;
use App\Http\Traits\CommonOfferFunctions;
use Illuminate\Support\Facades\Log;

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
        /*
        $data = DB::table('subscriptions')
            ->join('transactions', 'subscriptions.id', '=', 'transactions.subscription_id')
            ->select('*')
            ->get()->toArray();
        */

        $data = DB::table('transactions')
            ->select('*')
            ->where('subscription_id', '=', 0)
            ->get()->toArray();

        return [
            'module_name' => "Transactions",
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
        $user = \Illuminate\Support\Facades\Auth::user();
        $query = DB::table('offers');

        if ($user && $user->isAdvertiserManager()) {
            $assignedAdvIds = \App\Models\User::where('user_type', 'advertiser')->where('employee_id', (string) $user->id)->pluck('id')->toArray();
            $query->whereIn('offers.advertiser_id', count($assignedAdvIds) ? $assignedAdvIds : [-1]);
        }

        $query->leftJoin('offer_trackings as ot', function($join) use ($user) {
            $join->on('ot.offer_id', '=', 'offers.id');
            if ($user && $user->isAffiliateManager()) {
                $assignedAffIds = \App\Models\User::where('user_type', 'affiliate')->where('employee_id', (string) $user->id)->pluck('id')->toArray();
                $join->whereIn('ot.aff_id', count($assignedAffIds) ? $assignedAffIds : [-1]);
            }
        })
        ->leftJoin('conversions as conv', function($join) use ($user) {
            $join->on('conv.offer_id', '=', 'offers.id');
            if ($user && $user->isAffiliateManager()) {
                $assignedAffIds = \App\Models\User::where('user_type', 'affiliate')->where('employee_id', (string) $user->id)->pluck('id')->toArray();
                $join->whereIn('conv.affiliate_id', count($assignedAffIds) ? $assignedAffIds : [-1]);
            }
        })
        ->select(
            'offers.id',
            'offers.offer_name',
            DB::raw("COUNT(DISTINCT ot.tid) as total_clicks"),
            DB::raw("COUNT(DISTINCT conv.id) as total_conversions")
        )
        ->groupBy('offers.id', 'offers.offer_name')
        ->orderByRaw('total_clicks DESC');

        $data = $query->paginate(50);

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
        $user = \Illuminate\Support\Facades\Auth::user();
        if ($user && $user->isAdvertiserManager()) {
            $offer = Offer::find($offer_id);
            if (!$offer) {
                return DataTables::of(DB::table('offer_trackings')->whereRaw('0 = 1'))->make(true);
            }
            $assignedAdvIds = \App\Models\User::where('user_type', 'advertiser')->where('employee_id', (string) $user->id)->pluck('id')->toArray();
            if (!in_array($offer->advertiser_id, $assignedAdvIds)) {
                return DataTables::of(DB::table('offer_trackings')->whereRaw('0 = 1'))->make(true);
            }
        }

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

        if ($user && $user->isAffiliateManager()) {
            $assignedAffIds = \App\Models\User::where('user_type', 'affiliate')->where('employee_id', (string) $user->id)->pluck('id')->toArray();
            $data->whereIn('offer_trackings.aff_id', count($assignedAffIds) ? $assignedAffIds : [-1]);
        }

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
            ->editColumn('revenue_usd', function ($row) use ($user) {
                return ($user && $user->isObserver()) ? 0 : ($row->revenue_usd ?? 0);
            })
            ->editColumn('payout_usd', function ($row) use ($user) {
                return ($user && $user->isObserver()) ? 0 : ($row->payout_usd ?? 0);
            })
            ->make(true);
    }

    /**
     * Search offer clicks (legacy method - can be refactored)
     */
    public function searchOfferClicks($request)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        if ($user && $user->isAdvertiserManager()) {
            $offer = Offer::find($request->offer_id);
            $assignedAdvIds = \App\Models\User::where('user_type', 'advertiser')->where('employee_id', (string) $user->id)->pluck('id')->toArray();
            if (!$offer || !in_array($offer->advertiser_id, $assignedAdvIds)) {
                return [
                    'module_name' => "Offer Clicks Search",
                    'offer_name' => '',
                    'data' => [],
                    'filters' => $request->all()
                ];
            }
        }

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

        if ($user && $user->isAffiliateManager()) {
            $assignedAffIds = \App\Models\User::where('user_type', 'affiliate')->where('employee_id', (string) $user->id)->pluck('id')->toArray();
            $data->whereIn('offer_trackings.aff_id', count($assignedAffIds) ? $assignedAffIds : [-1]);
        }

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

        if ($user && $user->isObserver()) {
            foreach ($data as $row) {
                if (isset($row->revenue_usd)) $row->revenue_usd = 0;
                if (isset($row->payout_usd)) $row->payout_usd = 0;
                if (isset($row->revenue)) $row->revenue = 0;
                if (isset($row->payout)) $row->payout = 0;
            }
        }

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

    public function allPayouts($request = null)
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
                'C.status',
                'C.currency',
                'C.payout_usd',
                'C.created_at'
            );

        $user = \Illuminate\Support\Facades\Auth::user();
        if ($user) {
            if ($user->isAffiliateManager()) {
                $assignedAffIds = \App\Models\User::where('user_type', 'affiliate')->where('employee_id', (string) $user->id)->pluck('id')->toArray();
                $query->whereIn('C.affiliate_id', count($assignedAffIds) ? $assignedAffIds : [-1]);
            } elseif ($user->isAdvertiserManager()) {
                $assignedAdvIds = \App\Models\User::where('user_type', 'advertiser')->where('employee_id', (string) $user->id)->pluck('id')->toArray();
                $query->whereIn('O.advertiser_id', count($assignedAdvIds) ? $assignedAdvIds : [-1]);
            }
        }

        if ($request && $request->month) {
            $fromUtc = $this->getUtcStartOfDay(
                \Carbon\Carbon::createFromFormat('Y-m', $request->month)->startOfMonth()->toDateString()
            );
            $toUtc = $this->getUtcEndOfDay(
                \Carbon\Carbon::createFromFormat('Y-m', $request->month)->endOfMonth()->toDateString()
            );
            $query->whereBetween('C.created_at', [$fromUtc, $toUtc]);
        }

        $data = $query->orderBy('C.created_at', 'desc')->get();

        if ($user && $user->isObserver()) {
            foreach ($data as $row) {
                if (isset($row->amount)) $row->amount = 0;
                if (isset($row->payout_usd)) $row->payout_usd = 0;
            }
        }

        return [
            'module_name' => "Payouts Report",
            'data' => $data
        ];
    }

    /*public function allPayouts_OLD()
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
                'C.payout_usd',
                'C.created_at'
            )
            ->orderBy('C.created_at', 'desc')
            ->get();

        return [
            'module_name' => "Payouts Report",
            'data' => $data
        ];
    }*/

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
                'C.payout_usd',
                'C.status',
                'C.created_at'
            );

        $user = \Illuminate\Support\Facades\Auth::user();
        if ($user) {
            if ($user->isAffiliateManager()) {
                $assignedAffIds = \App\Models\User::where('user_type', 'affiliate')->where('employee_id', (string) $user->id)->pluck('id')->toArray();
                $query->whereIn('C.affiliate_id', count($assignedAffIds) ? $assignedAffIds : [-1]);
            } elseif ($user->isAdvertiserManager()) {
                $assignedAdvIds = \App\Models\User::where('user_type', 'advertiser')->where('employee_id', (string) $user->id)->pluck('id')->toArray();
                $query->whereIn('O.advertiser_id', count($assignedAdvIds) ? $assignedAdvIds : [-1]);
            }
        }

        if ($request->from && $request->to) {
            $fromUtc = $this->getUtcStartOfDay($request->from);
            $toUtc = $this->getUtcEndOfDay($request->to);
            $query->whereBetween('C.created_at', [$fromUtc, $toUtc]);
        }

        $data = $query->orderBy('C.created_at', 'desc')->get();

        if ($user && $user->isObserver()) {
            foreach ($data as $row) {
                if (isset($row->amount)) $row->amount = 0;
                if (isset($row->payout_usd)) $row->payout_usd = 0;
            }
        }

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
        $user = \Illuminate\Support\Facades\Auth::user();
        if ($user && $user->isAdvertiserManager()) {
            $offer = Offer::find($oid);
            $assignedAdvIds = \App\Models\User::where('user_type', 'advertiser')->where('employee_id', (string) $user->id)->pluck('id')->toArray();
            if (!$offer || !in_array($offer->advertiser_id, $assignedAdvIds)) {
                return ['module_name' => "Offer Payouts", 'offer_name' => '', 'oid' => $oid, 'data' => []];
            }
        }

        $data = $this->getPayoutsForOffer($oid);
        $of_name = '';

        if (count($data) > 0) {
            $dataOffer = DB::select("select offer_name from offers where id =" . $oid);
            if (strlen($dataOffer[0]->offer_name) > 0) {
                $of_name = $dataOffer[0]->offer_name;
            }
        }

        if ($user && $user->isObserver()) {
            foreach ($data as $row) {
                if (isset($row->payout)) $row->payout = 0;
                if (isset($row->payout_usd)) $row->payout_usd = 0;
                if (isset($row->amount)) $row->amount = 0;
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
        $user = \Illuminate\Support\Facades\Auth::user();
        if ($user && $user->isAdvertiserManager()) {
            $offer = Offer::find($oid);
            $assignedAdvIds = \App\Models\User::where('user_type', 'advertiser')->where('employee_id', (string) $user->id)->pluck('id')->toArray();
            if (!$offer || !in_array($offer->advertiser_id, $assignedAdvIds)) {
                return [
                    'module_name' => "Offer Payouts",
                    'offer_name' => '',
                    'data' => [],
                    'filters' => $request->all()
                ];
            }
        }

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

        if ($user && $user->isObserver()) {
            foreach ($data as $row) {
                if (isset($row->payout)) $row->payout = 0;
                if (isset($row->payout_usd)) $row->payout_usd = 0;
                if (isset($row->amount)) $row->amount = 0;
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
        $user = \Illuminate\Support\Facades\Auth::user();
        if ($user && $user->isAffiliateManager()) {
            $assignedAffIds = \App\Models\User::where('user_type', 'affiliate')->where('employee_id', (string) $user->id)->pluck('id')->toArray();
            if (!in_array($aid, $assignedAffIds)) {
                return ['module_name' => "Affiliate Payouts", 'affiliate_name' => '', 'aid' => $aid, 'data' => []];
            }
        }

        $data = $this->getAffiliatePayouts($aid);
        $af_name = '';

        if (count($data) > 0) {
            $dataOffer = DB::select("select name from users where id =" . $aid);
            if (strlen($dataOffer[0]->name) > 0) {
                $af_name = $dataOffer[0]->name;
            }
        }

        if ($user && $user->isObserver()) {
            foreach ($data as $row) {
                if (isset($row->payout)) $row->payout = 0;
                if (isset($row->payout_usd)) $row->payout_usd = 0;
                if (isset($row->amount)) $row->amount = 0;
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
        $user = \Illuminate\Support\Facades\Auth::user();
        if ($user && $user->isAffiliateManager()) {
            $assignedAffIds = \App\Models\User::where('user_type', 'affiliate')->where('employee_id', (string) $user->id)->pluck('id')->toArray();
            if (!in_array($aid, $assignedAffIds)) {
                return [
                    'module_name' => "Affiliate Payouts",
                    'affiliate_name' => '',
                    'data' => [],
                    'filters' => $request->all()
                ];
            }
        }

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

        if ($user && $user->isObserver()) {
            foreach ($data as $row) {
                if (isset($row->payout)) $row->payout = 0;
                if (isset($row->payout_usd)) $row->payout_usd = 0;
                if (isset($row->amount)) $row->amount = 0;
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
        $user = \Illuminate\Support\Facades\Auth::user();
        $query = DB::table('offer_trackings')
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
            ->leftJoin('devices', 'offer_trackings.device_id', '=', 'devices.id')
            ->join('users as affis', 'offer_trackings.aff_id', '=', 'affis.id')
            ->join('users as adverts', 'offer_trackings.adv_id', '=', 'adverts.id');

        if ($user) {
            if ($user->isAffiliateManager()) {
                $assignedAffIds = \App\Models\User::where('user_type', 'affiliate')->where('employee_id', (string) $user->id)->pluck('id')->toArray();
                $query->whereIn('offer_trackings.aff_id', count($assignedAffIds) ? $assignedAffIds : [-1]);
            } elseif ($user->isAdvertiserManager()) {
                $assignedAdvIds = \App\Models\User::where('user_type', 'advertiser')->where('employee_id', (string) $user->id)->pluck('id')->toArray();
                $query->whereIn('offers.advertiser_id', count($assignedAdvIds) ? $assignedAdvIds : [-1]);
            }
        }

        // Apply filters
        if ($request->from != "" && $request->to != "" && $request->from != $request->to) {
            $query = $query->whereBetween('offer_trackings.created_at', [$request->from, $request->to]);

        } elseif ($request->from != "" && $request->to != "" && $request->from == $request->to) {
            $query = $query->where('offer_trackings.created_at', $request->from);

        }

        if ($request->device) {
            $query = $query->where('offer_trackings.device_id', $request->device);
        }
        if ($request->browser) {

            $query = $query->where('offer_trackings.browser', $request->browser);
        }

        if ($request->os != "") {
            $query = $query->where('offer_trackings.os', 'LIKE', "%{$request->os}%");

        }

        $arrC = $this->getCountry();
        if (isset($request->country) && $request->country != "") {
            $query = $query->where('offer_trackings.country', $arrC[$request->country]);

        }

        $results = $query->orderBy('offer_trackings.id', 'desc')
            ->get();

        // Convert UTC timestamps to user's configured timezone and apply role masking
        $results->transform(function ($row) use ($user) {
            return $this->formatClickRow($row, $user);
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
        $user = \Illuminate\Support\Facades\Auth::user();
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
                'offers.offer_name',
                'ot.created_at as ot_click_time',
                'ot.ip as ot_click_ip'
            )
            ->leftJoin('offers', 'conversions.offer_id', '=', 'offers.id')
            ->leftJoin('users as affis', 'conversions.affiliate_id', '=', 'affis.id')
            ->leftJoin('users as adverts', 'conversions.advertiser_id', '=', 'adverts.id')
            ->leftJoin('offer_trackings as ot', 'conversions.tid', '=', 'ot.tid');

        if ($user) {
            if ($user->isAffiliateManager()) {
                $assignedAffIds = \App\Models\User::where('user_type', 'affiliate')->where('employee_id', (string) $user->id)->pluck('id')->toArray();
                $query->whereIn('conversions.affiliate_id', count($assignedAffIds) ? $assignedAffIds : [-1]);
            } elseif ($user->isAdvertiserManager()) {
                $assignedAdvIds = \App\Models\User::where('user_type', 'advertiser')->where('employee_id', (string) $user->id)->pluck('id')->toArray();
                $query->whereIn('offers.advertiser_id', count($assignedAdvIds) ? $assignedAdvIds : [-1]);
            }
        }

        // Apply filters
        if ($request->from && $request->to) {
            $fromUtc = $this->getUtcStartOfDay($request->from);
            $toUtc = $this->getUtcEndOfDay($request->to);
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

        $results = $query->orderBy('conversions.id', 'desc')->get();

        // Convert UTC timestamps and compute fallbacks / masking
        $results->transform(function ($row) use ($user) {
            return $this->formatConversionRow($row, $user);
        });

        return [
            'module_name' => 'Conversions Report',
            'data' => $results,
            'filters' => $request->all()
        ];
    }

    /**
     * Get dropdown data for filters: statuses, offers, affiliates, advertisers
     */
    public function getDropdownData(): array
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        // Distinct statuses from conversions
        $statuses = DB::table('conversions')
            ->distinct()
            ->pluck('status')
            ->toArray();

        // Offers list
        $offersQuery = DB::table('offers');
        if ($user && $user->isAdvertiserManager()) {
            $assignedAdvIds = \App\Models\User::where('user_type', 'advertiser')->where('employee_id', (string) $user->id)->pluck('id')->toArray();
            $offersQuery->whereIn('advertiser_id', count($assignedAdvIds) ? $assignedAdvIds : [-1]);
        } elseif ($user && $user->isAffiliateManager()) {
            $assignedAffIds = \App\Models\User::where('user_type', 'affiliate')->where('employee_id', (string) $user->id)->pluck('id')->toArray();
            $offersQuery->whereIn('id', function($q) use ($assignedAffIds) {
                $q->select('offer_id')->from('offer_affiliates')->whereIn('affiliate_id', count($assignedAffIds) ? $assignedAffIds : [-1]);
            });
        }
        $offers = $offersQuery->select('id', 'offer_name')->orderBy('offer_name')->get();

        // Affiliates list (users with role affiliate)
        $affiliatesQuery = DB::table('users')->where('user_type', 'affiliate');
        if ($user && $user->isAffiliateManager()) {
            $affiliatesQuery->where('employee_id', (string) $user->id);
        } elseif ($user && $user->isAdvertiserManager()) {
            $assignedAdvIds = \App\Models\User::where('user_type', 'advertiser')->where('employee_id', (string) $user->id)->pluck('id')->toArray();
            $affiliatesQuery->whereIn('id', function($q) use ($assignedAdvIds) {
                $q->select('affiliate_id')->from('offer_affiliates')
                  ->join('offers', 'offer_affiliates.offer_id', '=', 'offers.id')
                  ->whereIn('offers.advertiser_id', count($assignedAdvIds) ? $assignedAdvIds : [-1]);
            });
        }
        $affiliates = $affiliatesQuery->select('id', 'name')->orderBy('name')->get();

        // Advertisers list (users with role advertiser)
        $advertisersQuery = DB::table('users')->where('user_type', 'advertiser');
        if ($user && $user->isAdvertiserManager()) {
            $assignedAdvIds = \App\Models\User::where('user_type', 'advertiser')->where('employee_id', (string) $user->id)->pluck('id')->toArray();
            $advertisersQuery->whereIn('id', count($assignedAdvIds) ? $assignedAdvIds : [-1]);
        } elseif ($user && $user->isAffiliateManager()) {
            $advertisersQuery->whereRaw('0 = 1');
        }
        $advertisers = $advertisersQuery->select('id', 'name')->orderBy('name')->get();

        return [
            'statuses' => $statuses,
            'offers' => $offers,
            'affiliates' => $affiliates,
            'advertisers' => $advertisers,
        ];
    }

    /**
     * Get detailed conversion information by ID
     */
    public function getConversionDetail($id): array
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $query = DB::table('conversions')
            ->select(
                'conversions.*',
                'offers.offer_name',
                'affis.name as affiliate_name',
                'adverts.name as advertiser_name'
            )
            ->leftJoin('offers', 'conversions.offer_id', '=', 'offers.id')
            ->leftJoin('users as affis', 'conversions.affiliate_id', '=', 'affis.id')
            ->leftJoin('users as adverts', 'conversions.advertiser_id', '=', 'adverts.id')
            ->where('conversions.id', $id);

        if ($user) {
            if ($user->isAffiliateManager()) {
                $assignedAffIds = \App\Models\User::where('user_type', 'affiliate')->where('employee_id', (string) $user->id)->pluck('id')->toArray();
                $query->whereIn('conversions.affiliate_id', count($assignedAffIds) ? $assignedAffIds : [-1]);
            } elseif ($user->isAdvertiserManager()) {
                $assignedAdvIds = \App\Models\User::where('user_type', 'advertiser')->where('employee_id', (string) $user->id)->pluck('id')->toArray();
                $query->whereIn('offers.advertiser_id', count($assignedAdvIds) ? $assignedAdvIds : [-1]);
            }
        }

        $conversion = $query->first();

        if (!$conversion) {
            return ['success' => false, 'message' => 'Conversion not found'];
        }

        if ($user && $user->isObserver()) {
            if (isset($conversion->revenue)) $conversion->revenue = 0;
            if (isset($conversion->payout)) $conversion->payout = 0;
            if (isset($conversion->revenue_usd)) $conversion->revenue_usd = 0;
            if (isset($conversion->payout_usd)) $conversion->payout_usd = 0;
        }

        // Convert timestamps to local timezone if present
        if (isset($conversion->conversion_time)) {
            $conversion->conversion_time = $this->formatUtcToLocal($conversion->conversion_time);
        }
        if (isset($conversion->created_at)) {
            $conversion->created_at = $this->formatUtcToLocal($conversion->created_at);
        }

        return ['success' => true, 'data' => $conversion];
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

        $user = \Illuminate\Support\Facades\Auth::user();
        if ($user) {
            if ($user->isAffiliateManager()) {
                $assignedAffIds = \App\Models\User::where('user_type', 'affiliate')->where('employee_id', (string) $user->id)->pluck('id')->toArray();
                if (!in_array($conversion->affiliate_id, $assignedAffIds)) {
                    return ['success' => false, 'message' => 'Unauthorized'];
                }
            } elseif ($user->isAdvertiserManager()) {
                $assignedAdvIds = \App\Models\User::where('user_type', 'advertiser')->where('employee_id', (string) $user->id)->pluck('id')->toArray();
                if (!in_array($conversion->advertiser_id, $assignedAdvIds)) {
                    return ['success' => false, 'message' => 'Unauthorized'];
                }
            }
        }

        $conversion->status = $status;
        $conversion->save();

        if ($status === 'confirmed') {
            \App\Jobs\FireAffiliatePostbackJob::dispatch($conversion->id);
        }

        return ['success' => true, 'message' => 'Conversion status updated to ' . $status];
    }


    /**
     * Update conversion status (confirm or decline)
     */
    public function updateConversionStatusUsingTid($tid, $status): array
    {
        $conversion = \App\Models\Conversion::where('tid', $tid)->first();

        if (!$conversion) {
            return ['success' => false, 'message' => 'Conversion not found'];
        }

        $user = \Illuminate\Support\Facades\Auth::user();
        if ($user) {
            if ($user->isAffiliateManager()) {
                $assignedAffIds = \App\Models\User::where('user_type', 'affiliate')->where('employee_id', (string) $user->id)->pluck('id')->toArray();
                if (!in_array($conversion->affiliate_id, $assignedAffIds)) {
                    return ['success' => false, 'message' => 'Unauthorized'];
                }
            } elseif ($user->isAdvertiserManager()) {
                $assignedAdvIds = \App\Models\User::where('user_type', 'advertiser')->where('employee_id', (string) $user->id)->pluck('id')->toArray();
                if (!in_array($conversion->advertiser_id, $assignedAdvIds)) {
                    return ['success' => false, 'message' => 'Unauthorized'];
                }
            }
        }

        $conversion->status = $status;
        $conversion->save();

        if ($status === 'confirmed') {
            \App\Jobs\FireAffiliatePostbackJob::dispatch($conversion->id);
        }

        return ['success' => true, 'message' => 'Conversion status updated to ' . $status];
    }

    /**
     * Bulk update conversion statuses using parsed rows from a CSV/XLSX file.
     *
     * Each row accepts:
     * - conversion_id (or id)
     * - status (optional if $forcedStatus is provided)
     */
    public function bulkUpdateConversionStatuses(array $rows, ?string $forcedStatus = null): array
    {
        $normalizeStatus = function (?string $status): ?string {
            if ($status === null) {
                return null;
            }

            $status = strtolower(trim($status));
            $map = [
                'approve' => 'confirmed',
                'approved' => 'confirmed',
                'confirm' => 'confirmed',
                'confirmed' => 'confirmed',
                'reject' => 'declined',
                'rejected' => 'declined',
                'decline' => 'declined',
                'declined' => 'declined',
            ];

            return $map[$status] ?? null;
        };

        $normalizedForcedStatus = $normalizeStatus($forcedStatus);
        if ($forcedStatus !== null && $normalizedForcedStatus === null) {
            return [
                'success' => false,
                'message' => 'Invalid forced status. Use confirmed/declined (or approve/reject aliases).',
                'summary' => [
                    'total_rows' => count($rows),
                    'processed' => 0,
                    'updated' => 0,
                    'failed' => count($rows),
                ],
                'rows' => [],
            ];
        }

        $results = [];
        $updated = 0;
        $failed = 0;

        foreach ($rows as $row) {
            $rowNumber = (int) ($row['row_number'] ?? 0);
            $conversionIdRaw = trim((string) ($row['conversion_id'] ?? ''));
            $rowStatusRaw = $row['status'] ?? null;

            Log::info("RowNumber : " . $rowNumber);
            Log::info("conversion Id : " . $conversionIdRaw);
            Log::info("Row Status : " . $rowStatusRaw);

            $requestedStatus = $normalizedForcedStatus ?? $normalizeStatus(is_string($rowStatusRaw) ? $rowStatusRaw : null);

            if ($conversionIdRaw === '') {
                $failed++;
                $results[] = [
                    'row' => $rowNumber,
                    'conversion_id' => $conversionIdRaw,
                    'requested_status' => $requestedStatus,
                    'success' => false,
                    'message' => 'Invalid conversion_id.',
                ];
                continue;
            }

            if (!$requestedStatus) {
                $failed++;
                $results[] = [
                    'row' => $rowNumber,
                    'conversion_id' => (int) $conversionIdRaw,
                    'requested_status' => $rowStatusRaw,
                    'success' => false,
                    'message' => 'Missing or invalid status. Provide status column or send request status parameter.',
                ];
                continue;
            }

            Log::info("conversion Id : " . $conversionIdRaw);
            Log::info("Row Status : " . $rowStatusRaw);

            $response = $this->updateConversionStatusUsingTid($conversionIdRaw, $requestedStatus);
            if (!($response['success'] ?? false)) {
                $failed++;
            } else {
                $updated++;
            }

            $results[] = [
                'row' => $rowNumber,
                'conversion_id' => $conversionIdRaw,
                'requested_status' => $requestedStatus,
                'success' => (bool) ($response['success'] ?? false),
                'message' => $response['message'] ?? 'Processed',
            ];
        }

        $processed = count($results);

        return [
            'success' => $processed > 0 && $failed === 0,
            'message' => $processed === 0
                ? 'No data rows found in uploaded file.'
                : "Processed {$processed} rows.",
            'summary' => [
                'total_rows' => count($rows),
                'processed' => $processed,
                'updated' => $updated,
                'failed' => $failed,
            ],
            'rows' => $results,
        ];
    }
}

