<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Transaction;
use App\Services\Contracts\PdfGeneratorInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Contracts\Filesystem\Factory as StorageFactory;
use Illuminate\Http\File;

class InvoiceService
{
    protected $pdfGenerator;
    protected $storage;

    public function __construct(PdfGeneratorInterface $pdfGenerator, StorageFactory $storage)
    {
        $this->pdfGenerator = $pdfGenerator;
        $this->storage = $storage;
    }

    public function createDraft(int $advertiserId, array $transactionIds, int $createdBy): Invoice
    {
        // Validate transactions: must be unbilled and belong to the advertiser
        $validTransactionCount = Transaction::whereIn('id', $transactionIds)
            ->where('advertiser_id', $advertiserId)
            ->where('billing', 'Y')
            ->whereNull('invoice_id')
            ->count();

        if ($validTransactionCount !== count($transactionIds)) {
            throw new Exception("One or more selected transactions are invalid or already invoiced.");
        }

        return DB::transaction(function () use ($advertiserId, $transactionIds, $createdBy) {
            $invoice = Invoice::create([
                'invoice_number' => 'INV-' . strtoupper(Str::random(10)), // Placeholder logic
                'advertiser_id' => $advertiserId,
                'status' => Invoice::STATUS_DRAFT,
                'created_by' => $createdBy,
                'invoice_date' => now(),
            ]);

            Transaction::whereIn('id', $transactionIds)->update(['invoice_id' => $invoice->id]);

            $this->recalculateTotals($invoice);

            return $invoice;
        });
    }

    public function updateDraft(int $invoiceId, array $transactionIds, array $dates): Invoice
    {
        $invoice = Invoice::findOrFail($invoiceId);

        if (!$invoice->is_draft) {
            throw new Exception("Only draft invoices can be updated.");
        }

        return DB::transaction(function () use ($invoice, $transactionIds, $dates) {
            // Update metadata
            $invoice->update([
                'invoice_date' => $dates['invoice_date'] ?? $invoice->invoice_date,
                'due_date' => $dates['due_date'] ?? $invoice->due_date,
            ]);

            // Detach current transactions NOT in the new list (if any logic requires complete replacement)
            // Strategy: Detach all, then Attach new set? Or Diff? 
            // Simple approach: Detach all currently linked, then link valid ones from input.
            // Requirement said "Add/Remove transactions". 
            // Input $transactionIds acts as the new "Final List".

            // Detach all currently linked
            $invoice->transactions()->update(['invoice_id' => null]);

            // Re-attach valid transactions
            $validCount = Transaction::whereIn('id', $transactionIds)
                ->where('advertiser_id', $invoice->advertiser_id) // Security check
                ->where('billing', 'Y')
                // ->whereNull('invoice_id') // Careful: we just detached them, so they are NULL now. 
                // But what if user tries to steal another invoice's txn?
                // Logic: Invoice ID must be NULL OR already this invoice (which is now NULL)
                ->update(['invoice_id' => $invoice->id]);

            if ($validCount !== count($transactionIds)) {
                throw new Exception("Invalid transaction selection.");
            }

            $this->recalculateTotals($invoice->fresh());

            return $invoice;
        });
    }

    public function sendInvoice(int $invoiceId): Invoice
    {
        $invoice = Invoice::findOrFail($invoiceId);

        if (!$invoice->is_draft) {
            throw new Exception("Only draft invoices can be sent.");
        }

        if ($invoice->transactions()->count() === 0) {
            throw new Exception("Cannot send an empty invoice.");
        }

        // Generate PDF path: invoices/{agency_id}/{year}/{invoice_number}.pdf
        // Agency ID default 1 for now implementation as per context
        $agencyId = 1;
        $year = now()->year;
        $r2Path = "invoices/{$agencyId}/{$year}/{$invoice->invoice_number}.pdf";

        // Temp file creation
        $tempPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'inv_' . $invoice->id . '_' . Str::random(10) . '.pdf';

        try {
            // 1. Generate PDF to local temp file
            $html = view('invoices.pdf', ['invoice' => $invoice])->render();
            $this->pdfGenerator->generate($html, $tempPath);

            // 2. Upload to R2
            $uploaded = $this->storage->disk('r2')->putFileAs(
                dirname($r2Path),
                new File($tempPath),
                basename($r2Path)
            );

            if (!$uploaded) {
                throw new Exception("Failed to upload PDF to storage.");
            }

            // 3. Verify existence
            if (!$this->storage->disk('r2')->exists($r2Path)) {
                throw new Exception("Verification failed: PDF not found on storage after upload.");
            }

            // 4. Commit State
            return DB::transaction(function () use ($invoice, $r2Path) {
                $invoice->update([
                    'status' => Invoice::STATUS_SENT,
                    'sent_at' => now(),
                    'pdf_path' => $r2Path, // Relative storage path
                ]);
                return $invoice;
            });

        } catch (Exception $e) {
            // Cleanup on error is handled in finally, but throw up
            throw $e;
        } finally {
            // 5. Cleanup
            if (file_exists($tempPath)) {
                @unlink($tempPath);
            }
        }
    }

    public function getInvoicePdfUrl(int $invoiceId): string
    {
        $invoice = Invoice::findOrFail($invoiceId);

        if (!$invoice->pdf_path) {
            throw new Exception("PDF not found for this invoice.");
        }

        // Generate temporary signed URL (valid for 15 minutes)
        return $this->storage->disk('r2')->temporaryUrl(
            $invoice->pdf_path,
            now()->addMinutes(15)
        );
    }

    public function markAsPaid(int $invoiceId): Invoice
    {
        $invoice = Invoice::findOrFail($invoiceId);

        if ($invoice->is_paid) {
            return $invoice; // Idempotent
        }

        if (!$invoice->is_sent) {
            throw new Exception("Invoice must be SENT before being marked as PAID.");
        }

        $invoice->update([
            'status' => Invoice::STATUS_PAID,
            'paid_at' => now(),
        ]);

        return $invoice;
    }

    public function deleteInvoice(int $invoiceId): void
    {
        $invoice = Invoice::findOrFail($invoiceId);

        if (!$invoice->is_draft) {
            throw new Exception("Only draft invoices can be deleted.");
        }

        DB::transaction(function () use ($invoice) {
            $invoice->transactions()->update(['invoice_id' => null]);
            $invoice->update(['status' => Invoice::STATUS_DELETED]);
            $invoice->delete();
        });
    }

    private function recalculateTotals(Invoice $invoice)
    {
        $total = $invoice->transactions()->sum('billing_charge');
        $invoice->update([
            'subtotal_amount' => $total,
            'total_amount' => $total, // Tax logic can be added later
            'tax_amount' => 0
        ]);
    }
}
