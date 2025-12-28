<?php

namespace App\Services;

use App\Models\Invoice;
use App\Services\Contracts\PdfGeneratorInterface;
use Illuminate\Support\Facades\DB;
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

    /**
     * Create a draft invoice with manual input
     */
    public function createDraft(array $data, int $createdBy): Invoice
    {
        return Invoice::create([
            'invoice_number' => 'INV-' . strtoupper(Str::random(10)),
            'advertiser_id' => $data['advertiser_id'],
            'title' => $data['title'] ?? null,
            'total_amount' => $data['total_amount'],
            'subtotal_amount' => $data['total_amount'],
            'tax_amount' => 0,
            'status' => \App\Enums\InvoiceStatus::Draft->value,
            'invoice_date' => $data['invoice_date'] ?? now(),
            'due_date' => $data['due_date'] ?? null,
            'created_by' => $createdBy,
        ]);
    }

    /**
     * Update a draft invoice
     */
    public function updateDraft(int $invoiceId, array $data): Invoice
    {
        $invoice = Invoice::findOrFail($invoiceId);

        if (!$invoice->is_draft) {
            throw new Exception("Only draft invoices can be updated.");
        }

        $invoice->update([
            'title' => $data['title'] ?? $invoice->title,
            'total_amount' => $data['total_amount'] ?? $invoice->total_amount,
            'subtotal_amount' => $data['total_amount'] ?? $invoice->subtotal_amount,
            'invoice_date' => $data['invoice_date'] ?? $invoice->invoice_date,
            'due_date' => $data['due_date'] ?? $invoice->due_date,
        ]);

        return $invoice->fresh();
    }

    /**
     * Send invoice - generates PDF and uploads to R2
     */
    public function sendInvoice(int $invoiceId): Invoice
    {
        $invoice = Invoice::with('advertiser')->findOrFail($invoiceId);

        if (!$invoice->is_draft) {
            throw new Exception("Only draft invoices can be sent.");
        }

        if (!$invoice->total_amount || $invoice->total_amount <= 0) {
            throw new Exception("Cannot send an invoice with zero or negative amount.");
        }

        // Generate PDF path
        $agencyId = 1;
        $year = now()->year;
        $r2Path = "invoices/{$agencyId}/{$year}/{$invoice->invoice_number}.pdf";

        $tempPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'inv_' . $invoice->id . '_' . Str::random(10) . '.pdf';

        try {
            // 1. Generate PDF from HTML template
            $html = $this->renderPdfHtml($invoice);
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
                    'status' => \App\Enums\InvoiceStatus::Sent->value,
                    'sent_at' => now(),
                    'pdf_path' => $r2Path,
                ]);
                return $invoice;
            });

        } finally {
            if (file_exists($tempPath)) {
                @unlink($tempPath);
            }
        }
    }

    /**
     * Render PDF HTML from template
     */
    private function renderPdfHtml(Invoice $invoice): string
    {
        $templatePath = resource_path('views/pdf/invoice.html');

        if (!file_exists($templatePath)) {
            throw new Exception("PDF template not found.");
        }

        $html = file_get_contents($templatePath);

        // Replace placeholders
        $replacements = [
            '{{invoice_number}}' => $invoice->invoice_number,
            '{{advertiser_name}}' => $invoice->advertiser->company ?? 'N/A',
            '{{title}}' => $invoice->title ?? 'Invoice',
            '{{invoice_date}}' => $invoice->invoice_date->format('Y-m-d'),
            '{{due_date}}' => $invoice->due_date ? $invoice->due_date->format('Y-m-d') : 'N/A',
            '{{total_amount}}' => number_format($invoice->total_amount, 2),
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $html);
    }

    /**
     * Get signed URL for PDF download
     */
    public function getInvoicePdfUrl(int $invoiceId): string
    {
        $invoice = Invoice::findOrFail($invoiceId);

        if (!$invoice->pdf_path) {
            throw new Exception("PDF not found for this invoice.");
        }

        return $this->storage->disk('r2')->temporaryUrl(
            $invoice->pdf_path,
            now()->addMinutes(15)
        );
    }

    /**
     * Mark invoice as paid
     */
    public function markAsPaid(int $invoiceId): Invoice
    {
        $invoice = Invoice::findOrFail($invoiceId);

        if ($invoice->is_paid) {
            return $invoice;
        }

        if (!$invoice->is_sent) {
            throw new Exception("Invoice must be SENT before being marked as PAID.");
        }

        $invoice->update([
            'status' => \App\Enums\InvoiceStatus::Paid->value,
            'paid_at' => now(),
        ]);

        return $invoice;
    }

    /**
     * Delete draft invoice
     */
    public function deleteInvoice(int $invoiceId): void
    {
        $invoice = Invoice::findOrFail($invoiceId);

        if (!$invoice->is_draft) {
            throw new Exception("Only draft invoices can be deleted.");
        }

        DB::transaction(function () use ($invoice) {
            $invoice->update(['status' => \App\Enums\InvoiceStatus::Deleted->value]);
            $invoice->delete();
        });
    }

    /**
     * Get dashboard statistics
     */
    public function getDashboardStats(): array
    {
        return [
            'received_amount' => Invoice::where('status', \App\Enums\InvoiceStatus::Paid->value)->sum('total_amount'),
            'pending_amount' => Invoice::where('status', \App\Enums\InvoiceStatus::Sent->value)->sum('total_amount'),
            'draft_count' => Invoice::where('status', \App\Enums\InvoiceStatus::Draft->value)->count(),
        ];
    }
}
