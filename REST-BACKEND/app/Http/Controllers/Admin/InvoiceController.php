<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Advertiser;
use App\Models\Invoice;
use App\Models\Transaction;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class InvoiceController extends Controller
{
    protected $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Invoice::with('advertiser')->select('invoices.*');
            return DataTables::of($data)
                ->editColumn('status', function ($row) {
                    return '<span class="badge badge-' . ($row->status == 'PAID' ? 'success' : ($row->status == 'SENT' ? 'info' : 'secondary')) . '">' . $row->status . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route('admin.invoices.show', $row->id) . '" class="btn btn-sm btn-primary">View</a>';
                    return $btn;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
        return view('admin.invoices.index');
    }

    public function create()
    {
        $advertisers = Advertiser::all();
        return view('admin.invoices.create', compact('advertisers'));
    }

    public function getBillableTransactions(Request $request, $advertiserId)
    {
        $transactions = Transaction::where('advertiser_id', $advertiserId)
            ->where('billing', 'Y')
            ->whereNull('invoice_id')
            ->select('id', 'billing_charge', 'billing_reason', 'created_at')
            ->get();

        return response()->json($transactions);
    }

    public function store(Request $request)
    {
        $request->validate([
            'advertiser_id' => 'required|exists:advertisers,id',
            'transactions' => 'required|array|min:1',
            'transactions.*' => 'exists:transactions,id'
        ]);

        try {
            $invoice = $this->invoiceService->createDraft(
                $request->advertiser_id,
                $request->transactions,
                auth()->id() ?? 1 // Fallback for dev/test if no auth
            );
            return response()->json(['success' => true, 'invoice_id' => $invoice->id]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function show($id)
    {
        $invoice = Invoice::with(['advertiser', 'transactions'])->findOrFail($id);
        return view('admin.invoices.show', compact('invoice'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'transaction_ids' => 'required|array|min:1',
            'invoice_date' => 'nullable|date',
            'due_date' => 'nullable|date',
        ]);

        try {
            $this->invoiceService->updateDraft($id, $request->transaction_ids, $request->only('invoice_date', 'due_date'));
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function send($id)
    {
        try {
            $this->invoiceService->sendInvoice($id);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function pay($id)
    {
        try {
            $this->invoiceService->markAsPaid($id);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function destroy($id)
    {
        try {
            $this->invoiceService->deleteInvoice($id);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function download($id)
    {
        try {
            $url = $this->invoiceService->getInvoicePdfUrl($id);
            return redirect()->away($url);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }
}
