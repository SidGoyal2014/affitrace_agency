<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceLineItem;
use App\Mail\InvoiceMail;
use App\Services\Contracts\PdfGeneratorInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
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
     * Create a draft invoice with all fields + line items
     */
    public function createDraft(array $data, int $createdBy): Invoice
    {
        return DB::transaction(function () use ($data, $createdBy) {
            // Calculate totals from line items
            $lineItems = $data['line_items'] ?? [];
            $subtotal = 0;
            foreach ($lineItems as $item) {
                $qty = floatval($item['quantity'] ?? 1);
                $price = floatval($item['price'] ?? 0);
                $subtotal += $qty * $price;
            }

            // If no line items, use legacy total_amount
            if (empty($lineItems) && isset($data['total_amount'])) {
                $subtotal = floatval($data['total_amount']);
            }

            $taxPercent = floatval($data['tax_percent'] ?? 0);
            $taxAmount = $subtotal * ($taxPercent / 100);
            $discountAmount = floatval($data['discount_amount'] ?? 0);
            $netTotal = $subtotal + $taxAmount - $discountAmount;
            $totalAmount = $netTotal;

            $invoice = Invoice::create([
                'invoice_number' => 'INV-' . strtoupper(Str::random(10)),
                'advertiser_id' => $data['advertiser_id'] ?? null,
                'title' => $data['title'] ?? null,
                // From
                'from_company' => $data['from_company'] ?? null,
                'from_address' => $data['from_address'] ?? null,
                'from_email' => $data['from_email'] ?? null,
                'from_vat_id' => $data['from_vat_id'] ?? null,
                // To
                'company_type' => $data['company_type'] ?? 'advertiser',
                'company_name' => $data['company_name'] ?? null,
                'to_address' => $data['to_address'] ?? null,
                'to_email' => $data['to_email'] ?? null,
                'to_vat_id' => $data['to_vat_id'] ?? null,
                // Offer
                'offer_id' => $data['offer_id'] ?? null,
                // Amounts
                'subtotal_amount' => $subtotal,
                'tax_percent' => $taxPercent,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'net_total' => $netTotal,
                'total_amount' => $totalAmount,
                // Payment & extras
                'payment_method' => $data['payment_method'] ?? null,
                'notes' => $data['notes'] ?? null,
                'terms' => $data['terms'] ?? null,
                // Status & dates
                'status' => \App\Enums\InvoiceStatus::Draft->value,
                'invoice_date' => $data['invoice_date'] ?? now(),
                'due_date' => $data['due_date'] ?? null,
                'created_by' => $createdBy,
            ]);

            // Create line items
            foreach ($lineItems as $item) {
                $qty = floatval($item['quantity'] ?? 1);
                $price = floatval($item['price'] ?? 0);
                $invoice->lineItems()->create([
                    'description' => $item['description'] ?? '',
                    'quantity' => $qty,
                    'price' => $price,
                    'subtotal' => $qty * $price,
                ]);
            }

            return $invoice->load('lineItems');
        });
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

        return DB::transaction(function () use ($invoice, $data) {
            // Recalculate totals from line items if provided
            $lineItems = $data['line_items'] ?? null;
            $subtotal = $invoice->subtotal_amount;

            if ($lineItems !== null) {
                $subtotal = 0;
                foreach ($lineItems as $item) {
                    $qty = floatval($item['quantity'] ?? 1);
                    $price = floatval($item['price'] ?? 0);
                    $subtotal += $qty * $price;
                }
            } elseif (isset($data['total_amount'])) {
                $subtotal = floatval($data['total_amount']);
            }

            $taxPercent = floatval($data['tax_percent'] ?? $invoice->tax_percent);
            $taxAmount = $subtotal * ($taxPercent / 100);
            $discountAmount = floatval($data['discount_amount'] ?? $invoice->discount_amount);
            $netTotal = $subtotal + $taxAmount - $discountAmount;

            $invoice->update([
                'title' => $data['title'] ?? $invoice->title,
                'advertiser_id' => $data['advertiser_id'] ?? $invoice->advertiser_id,
                // From
                'from_company' => $data['from_company'] ?? $invoice->from_company,
                'from_address' => $data['from_address'] ?? $invoice->from_address,
                'from_email' => $data['from_email'] ?? $invoice->from_email,
                'from_vat_id' => $data['from_vat_id'] ?? $invoice->from_vat_id,
                // To
                'company_type' => $data['company_type'] ?? $invoice->company_type,
                'company_name' => $data['company_name'] ?? $invoice->company_name,
                'to_address' => $data['to_address'] ?? $invoice->to_address,
                'to_email' => $data['to_email'] ?? $invoice->to_email,
                'to_vat_id' => $data['to_vat_id'] ?? $invoice->to_vat_id,
                // Offer
                'offer_id' => array_key_exists('offer_id', $data) ? $data['offer_id'] : $invoice->offer_id,
                // Amounts
                'subtotal_amount' => $subtotal,
                'tax_percent' => $taxPercent,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'net_total' => $netTotal,
                'total_amount' => $netTotal,
                // Payment & extras
                'payment_method' => $data['payment_method'] ?? $invoice->payment_method,
                'notes' => $data['notes'] ?? $invoice->notes,
                'terms' => $data['terms'] ?? $invoice->terms,
                // Dates
                'invoice_date' => $data['invoice_date'] ?? $invoice->invoice_date,
                'due_date' => $data['due_date'] ?? $invoice->due_date,
            ]);

            // Replace line items if provided
            if ($lineItems !== null) {
                $invoice->lineItems()->delete();
                foreach ($lineItems as $item) {
                    $qty = floatval($item['quantity'] ?? 1);
                    $price = floatval($item['price'] ?? 0);
                    $invoice->lineItems()->create([
                        'description' => $item['description'] ?? '',
                        'quantity' => $qty,
                        'price' => $price,
                        'subtotal' => $qty * $price,
                    ]);
                }
            }

            return $invoice->fresh()->load('lineItems');
        });
    }

    /**
     * Send invoice - generates PDF and uploads to R2
     */
    public function sendInvoice(int $invoiceId): Invoice
    {
        $invoice = Invoice::with(['advertiser', 'lineItems'])->findOrFail($invoiceId);

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

        // Build line items HTML
        $lineItemsHtml = '';
        if ($invoice->lineItems && $invoice->lineItems->count() > 0) {
            foreach ($invoice->lineItems as $item) {
                $lineItemsHtml .= '<tr>';
                $lineItemsHtml .= '<td>' . e($item->description) . '</td>';
                $lineItemsHtml .= '<td style="text-align:center;">' . number_format($item->quantity, 2) . '</td>';
                $lineItemsHtml .= '<td style="text-align:right;">$' . number_format($item->price, 2) . '</td>';
                $lineItemsHtml .= '<td style="text-align:right;">$' . number_format($item->subtotal, 2) . '</td>';
                $lineItemsHtml .= '</tr>';
            }
        } else {
            $lineItemsHtml = '<tr><td colspan="4" style="text-align:center; color:#888;">No line items</td></tr>';
        }

        // Replace placeholders
        $replacements = [
            '{{invoice_number}}' => $invoice->invoice_number,
            '{{from_company}}' => $invoice->from_company ?? 'N/A',
            '{{from_address}}' => nl2br(e($invoice->from_address ?? '')),
            '{{from_email}}' => $invoice->from_email ?? '',
            '{{from_vat_id}}' => $invoice->from_vat_id ?? '',
            '{{company_name}}' => $invoice->company_name ?? ($invoice->advertiser ? $invoice->advertiser->company : 'N/A'),
            '{{to_address}}' => nl2br(e($invoice->to_address ?? '')),
            '{{to_email}}' => $invoice->to_email ?? '',
            '{{to_vat_id}}' => $invoice->to_vat_id ?? '',
            '{{advertiser_name}}' => $invoice->company_name ?? ($invoice->advertiser ? $invoice->advertiser->company : 'N/A'),
            '{{title}}' => $invoice->title ?? 'Invoice',
            '{{invoice_date}}' => $invoice->invoice_date ? $invoice->invoice_date->format('Y-m-d') : 'N/A',
            '{{due_date}}' => $invoice->due_date ? $invoice->due_date->format('Y-m-d') : 'N/A',
            '{{line_items}}' => $lineItemsHtml,
            '{{subtotal_amount}}' => number_format($invoice->subtotal_amount, 2),
            '{{tax_percent}}' => number_format($invoice->tax_percent, 2),
            '{{tax_amount}}' => number_format($invoice->tax_amount, 2),
            '{{discount_amount}}' => number_format($invoice->discount_amount, 2),
            '{{net_total}}' => number_format($invoice->net_total, 2),
            '{{total_amount}}' => number_format($invoice->total_amount, 2),
            '{{payment_method}}' => $invoice->payment_method ?? 'N/A',
            '{{notes}}' => nl2br(e($invoice->notes ?? '')),
            '{{terms}}' => nl2br(e($invoice->terms ?? '')),
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
     * Email invoice PDF to advertiser
     */
    public function emailInvoice(int $invoiceId): void
    {
        $invoice = Invoice::with('advertiser')->findOrFail($invoiceId);

        if (!$invoice->pdf_path) {
            throw new Exception("Invoice PDF has not been generated yet. Please send the invoice first.");
        }

        // Determine recipient email: use to_email first, then advertiser email
        $recipientEmail = $invoice->to_email;
        if (!$recipientEmail && $invoice->advertiser) {
            $recipientEmail = $invoice->advertiser->email;
        }

        if (!$recipientEmail) {
            throw new Exception("Recipient email address not found.");
        }

        $tempPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'email_inv_' . $invoice->id . '_' . Str::random(8) . '.pdf';

        try {
            // Download PDF from R2 to temp file
            $pdfContent = $this->storage->disk('r2')->get($invoice->pdf_path);

            if (!$pdfContent) {
                throw new Exception("Failed to retrieve invoice PDF from storage.");
            }

            file_put_contents($tempPath, $pdfContent);

            // Prepare email data
            $invoiceData = [
                'subject' => 'Invoice ' . $invoice->invoice_number . ' from ' . ($invoice->from_company ?? config('app.name', 'Affitrace')),
                'invoice_number' => $invoice->invoice_number,
                'title' => $invoice->title ?? 'Invoice',
                'advertiser_name' => $invoice->company_name ?? ($invoice->advertiser ? ($invoice->advertiser->company ?? $invoice->advertiser->name) : 'Valued Customer'),
                'invoice_date' => $invoice->invoice_date ? $invoice->invoice_date->format('Y-m-d') : 'N/A',
                'due_date' => $invoice->due_date ? $invoice->due_date->format('Y-m-d') : 'N/A',
                'total_amount' => number_format($invoice->total_amount, 2),
                'pdf_path' => $tempPath,
                'pdf_filename' => $invoice->invoice_number . '.pdf',
            ];

            Mail::to($recipientEmail)->send(new InvoiceMail($invoiceData));

        } finally {
            if (file_exists($tempPath)) {
                @unlink($tempPath);
            }
        }
    }

    /**
     * Revert a SENT invoice back to DRAFT
     */
    public function revertToDraft(int $invoiceId): Invoice
    {
        $invoice = Invoice::findOrFail($invoiceId);

        if (!$invoice->is_sent) {
            throw new Exception("Only SENT invoices can be reverted to draft.");
        }

        $invoice->update([
            'status' => \App\Enums\InvoiceStatus::Draft->value,
            'sent_at' => null,
        ]);

        return $invoice->fresh();
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
