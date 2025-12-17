<?php

namespace App\Http\Controllers\Advertiser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Advertiser\AdvertiserReportService;

class AdvertiserReportController extends Controller
{
    public function summary(Request $request, AdvertiserReportService $service)
    {
        try {
            return response()->json($service->getSummaryReport(Auth::id()));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function invoices(Request $request, AdvertiserReportService $service)
    {
        try {
            return response()->json($service->getInvoices(Auth::id()));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}

