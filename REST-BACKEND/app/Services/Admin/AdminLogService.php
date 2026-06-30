<?php

namespace App\Services\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Services\Reports\BaseReportService;
use Illuminate\Support\Facades\Log;
use App\Models\ImpersonationLog;
class AdminLogService extends BaseReportService
{

    /**
     * List logs
     */
    public function listSystemLogs()
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
            'module_name' => "System Logs",
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
     * Get Affiliate Postback Logs
     */
    public function getAuditLogs($request)
    {
        $query = DB::table('audit_logs as al')
            ->select(
                'al.id',
                'al.actor_id',
                'al.action',
                'al.details',
                'al.ip_address',
                'al.created_at',
                'users.name as username'
            )
            ->join('users', 'al.actor_id', '=', 'users.id');

        // Can easily add filters here later if needed

        $data = $query->orderBy('al.id', 'desc')->paginate(50);

        return [
            'module_name' => "Audit Logs",
            'data' => $data
        ];
    }


    /**
     * List impersonation logs.
     */
    /**
     * List impersonation logs.
     */
    public function getImpersonationLogs(Request $request)
    {
        // Define filters and pagination limit from the request
        $filters = $request->only(['impersonator_id', 'impersonated_id']);
        $perPage = $request->input('per_page', 25);

        $query = ImpersonationLog::with(['impersonator:id,name,email', 'impersonated:id,name,email,user_type'])
            ->orderByDesc('started_at');

        if (!empty($filters['impersonator_id'])) {
            $query->where('impersonator_id', $filters['impersonator_id']);
        }

        if (!empty($filters['impersonated_id'])) {
            $query->where('impersonated_id', $filters['impersonated_id']);
        }

        return $query->paginate($perPage);
    }





}

