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
     * List invoices (DataTables compatible with filters)
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Invoice::with('advertiser')->select('invoices.*');

            // Filter by advertiser_id
            if ($request->filled('advertiser_id')) {
                $query->where('advertiser_id', $request->advertiser_id);
            }

            // Filter by company_name (search across advertisers, affiliates, custom)
            if ($request->filled('company_name')) {
                $query->where('company_name', 'ILIKE', '%' . $request->company_name . '%');
            }

            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Filter by amount range
            if ($request->filled('amount_min')) {
                $query->where('total_amount', '>=', $request->amount_min);
            }
            if ($request->filled('amount_max')) {
                $query->where('total_amount', '<=', $request->amount_max);
            }

            // Filter by invoice date range
            if ($request->filled('date_from')) {
                $query->whereDate('invoice_date', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('invoice_date', '<=', $request->date_to);
            }

            // Filter by due date range
            if ($request->filled('due_date_from')) {
                $query->whereDate('due_date', '>=', $request->due_date_from);
            }
            if ($request->filled('due_date_to')) {
                $query->whereDate('due_date', '<=', $request->due_date_to);
            }

            return DataTables::of($query)
                ->addColumn('display_company', function ($row) {
                    // Use company_name if set, otherwise fall back to advertiser
                    if ($row->company_name) {
                        return $row->company_name;
                    }
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
                    $btns = '<a href="detail.php?id=' . $row->id . '" class="btn btn-sm btn-primary mr-1" title="View"><i class="fas fa-eye"></i></a>';
                    $btns .= '<a href="edit.php?id=' . $row->id . '" class="btn btn-sm btn-info" title="Edit"><i class="fas fa-edit"></i></a>';
                    return $btns;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return response()->json(['message' => 'Use AJAX request']);
    }

    /**
     * Store new invoice
     */
    public function store(Request $request)
    {
        $request->validate([
            'company_type' => 'required|in:advertiser,affiliate,custom',
            'company_name' => 'required|string|max:255',
            'title' => 'nullable|string|max:255',
            'invoice_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            // From
            'from_company' => 'nullable|string|max:255',
            'from_address' => 'nullable|string',
            'from_email' => 'nullable|email',
            'from_vat_id' => 'nullable|string|max:100',
            // To
            'to_address' => 'nullable|string',
            'to_email' => 'nullable|email',
            'to_vat_id' => 'nullable|string|max:100',
            // Offer
            'offer_id' => 'nullable|integer',
            // Amounts
            'total_amount' => 'nullable|numeric|min:0',
            'tax_percent' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            // Line items
            'line_items' => 'nullable|array',
            'line_items.*.description' => 'nullable|string',
            'line_items.*.quantity' => 'nullable|numeric|min:0',
            'line_items.*.price' => 'nullable|numeric|min:0',
            // Payment & extras
            'payment_method' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'terms' => 'nullable|string',
        ]);

        try {
            $invoice = $this->invoiceService->createDraft(
                $request->all(),
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
        $invoice = Invoice::with(['advertiser', 'lineItems'])->findOrFail($id);
        return response()->json($invoice);
    }

    /**
     * Update draft invoice
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'invoice_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'company_type' => 'nullable|in:advertiser,affiliate,custom',
            'company_name' => 'nullable|string|max:255',
            'from_company' => 'nullable|string|max:255',
            'from_address' => 'nullable|string',
            'from_email' => 'nullable|email',
            'from_vat_id' => 'nullable|string|max:100',
            'to_address' => 'nullable|string',
            'to_email' => 'nullable|email',
            'to_vat_id' => 'nullable|string|max:100',
            'offer_id' => 'nullable|integer',
            'total_amount' => 'nullable|numeric|min:0',
            'tax_percent' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'line_items' => 'nullable|array',
            'line_items.*.description' => 'nullable|string',
            'line_items.*.quantity' => 'nullable|numeric|min:0',
            'line_items.*.price' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'terms' => 'nullable|string',
        ]);

        try {
            $invoice = $this->invoiceService->updateDraft(
                $id,
                $request->all()
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
     * Download PDF - returns signed URL as JSON
     */
    public function download($id)
    {
        try {
            $url = $this->invoiceService->getInvoicePdfUrl($id);
            return response()->json(['success' => true, 'url' => $url]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    /**
     * Email invoice PDF to advertiser
     */
    public function email($id)
    {
        try {
            $this->invoiceService->emailInvoice($id);
            return response()->json(['success' => true, 'message' => 'Invoice emailed successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * Revert SENT invoice back to DRAFT
     */
    public function revertToDraft($id)
    {
        try {
            $this->invoiceService->revertToDraft($id);
            return response()->json(['success' => true, 'message' => 'Invoice reverted to draft.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
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
