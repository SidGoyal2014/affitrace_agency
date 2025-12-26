<?php

namespace App\Http\Controllers\Advertiser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Advertiser\AdvertiserReportService;
use App\Services\InvoiceService;
use App\Models\Invoice;

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

    public function downloadInvoice($id, InvoiceService $invoiceService)
    {
        try {
            // Verify the invoice belongs to this advertiser
            $invoice = Invoice::where('id', $id)
                ->where('advertiser_id', Auth::id())
                ->whereIn('status', [Invoice::STATUS_SENT, Invoice::STATUS_PAID])
                ->firstOrFail();

            $url = $invoiceService->getInvoicePdfUrl($id);
            return response()->json(['success' => true, 'url' => $url]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }
}

