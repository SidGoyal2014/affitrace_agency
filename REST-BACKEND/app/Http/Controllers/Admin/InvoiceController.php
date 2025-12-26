<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class InvoiceController extends Controller
{
    protected $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    /**
     * List invoices (DataTables compatible)
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Invoice::with('advertiser')->select('invoices.*');
            return DataTables::of($data)
                ->addColumn('advertiser.company', function ($row) {
                    return $row->advertiser ? $row->advertiser->company : 'N/A';
                })
                ->editColumn('status', function ($row) {
                    $color = match ($row->status) {
                        'PAID' => 'success',
                        'SENT' => 'warning',
                        default => 'secondary',
                    };
                    return '<span class="badge badge-' . $color . '">' . $row->status . '</span>';
                })
                ->addColumn('action', function ($row) {
                    return '<a href="detail.php?id=' . $row->id . '" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return response()->json(['message' => 'Use AJAX request']);
    }

    /**
     * Store new invoice (manual input)
     */
    public function store(Request $request)
    {
        $request->validate([
            'advertiser_id' => 'required|exists:users,id',
            'title' => 'nullable|string|max:255',
            'total_amount' => 'required|numeric|min:0.01',
            'invoice_date' => 'nullable|date',
            'due_date' => 'nullable|date',
        ]);

        try {
            $invoice = $this->invoiceService->createDraft(
                $request->only(['advertiser_id', 'title', 'total_amount', 'invoice_date', 'due_date']),
                auth()->id() ?? 1
            );
            return response()->json(['success' => true, 'invoice_id' => $invoice->id]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * Show single invoice (JSON)
     */
    public function show($id)
    {
        $invoice = Invoice::with('advertiser')->findOrFail($id);
        return response()->json($invoice);
    }

    /**
     * Update draft invoice
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'total_amount' => 'nullable|numeric|min:0.01',
            'invoice_date' => 'nullable|date',
            'due_date' => 'nullable|date',
        ]);

        try {
            $invoice = $this->invoiceService->updateDraft(
                $id,
                $request->only(['title', 'total_amount', 'invoice_date', 'due_date'])
            );
            return response()->json(['success' => true, 'invoice' => $invoice]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * Send invoice (generate PDF, change status)
     */
    public function send($id)
    {
        try {
            $this->invoiceService->sendInvoice($id);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * Mark invoice as paid
     */
    public function pay($id)
    {
        try {
            $this->invoiceService->markAsPaid($id);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * Delete draft invoice
     */
    public function destroy($id)
    {
        try {
            $this->invoiceService->deleteInvoice($id);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * Download PDF (redirect to signed URL)
     */
    public function download($id)
    {
        try {
            $url = $this->invoiceService->getInvoicePdfUrl($id);
            return redirect()->away($url);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    /**
     * Dashboard statistics
     */
    public function stats()
    {
        try {
            $stats = $this->invoiceService->getDashboardStats();
            return response()->json($stats);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
