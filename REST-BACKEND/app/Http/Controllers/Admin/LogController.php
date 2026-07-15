<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\Admin\AdminLogService;
use Illuminate\Support\Facades\Log;

class LogController extends Controller
{
	protected $logService;

	public function __construct(AdminLogService $logService)
	{
		$this->logService = $logService;
	}

	function system_logs()
	{
		return response()->json($this->logService->listSystemLogs());
	}

	public function postback_logs(Request $request)
	{
		return response()->json($this->logService->getAffiliatePostbackLogs($request));
	}

	public function impersonation_logs(Request $request)
	{
		return response()->json($this->logService->getImpersonationLogs($request));
	}

	public function audit_logs(Request $request)
	{
		return response()->json($this->logService->getAuditLogs($request));
	}

}
