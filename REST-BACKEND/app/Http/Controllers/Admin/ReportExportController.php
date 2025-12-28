<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateReportExport;
use App\Models\ReportExport;
use App\Services\ReportExportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Log;

class ReportExportController extends Controller
{
    protected ReportExportService $service;

    public function __construct(ReportExportService $service)
    {
        $this->service = $service;
    }

    /**
     * Initiate an async report export.
     * 
     * POST /api/admin/reports/export
     */
    public function export(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'report_type' => 'required|string|in:clicks,conversions,all_trackings',
            'format' => 'required|string|in:csv,xlsx',
            'filters' => 'nullable|array',
            'columns' => 'nullable|array',
        ]);

        $ttlMinutes = config('reports.export_ttl_minutes', 10);

        // Create export record with metadata only
        $export = ReportExport::create([
            'user_id' => Auth::id(),
            'report_type' => $validated['report_type'],
            'format' => $validated['format'],
            'filters' => $validated['filters'] ?? [],
            'columns' => $validated['columns'] ?? null,
            'status' => ReportExport::STATUS_QUEUED,
            'expires_at' => now()->addMinutes($ttlMinutes),
        ]);

        // Dispatch async job to dedicated reports queue
        GenerateReportExport::dispatch($export->id);

        return response()->json([
            'success' => true,
            'message' => 'Export queued successfully',
            'data' => [
                'export_id' => $export->id,
                'status' => $export->status,
                'expires_at' => $export->expires_at->toIso8601String(),
            ],
        ], 202);
    }

    /**
     * Check export status.
     * 
     * GET /api/admin/reports/export/{id}/status
     */
    public function status(int $id): JsonResponse
    {
        $export = ReportExport::find($id);

        if (!$export) {
            return response()->json([
                'success' => false,
                'message' => 'Export not found',
            ], 404);
        }

        // Authorization: only owner can check status
        if ($export->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'export_id' => $export->id,
                'status' => $export->status,
                'report_type' => $export->report_type,
                'format' => $export->format,
                'expires_at' => $export->expires_at->toIso8601String(),
                'error' => $export->error,
                'is_ready' => $export->isReady(),
                'is_expired' => $export->isExpired(),
            ],
        ]);
    }

    /**
     * Download the generated report.
     * 
     * GET /api/admin/reports/export/{id}/download
     * 
     * Returns:
     * - 200: Streams file and deletes after
     * - 403: Not authorized
     * - 404: Export not found
     * - 409: Export not ready yet
     * - 410: Export expired
     */
    public function download(int $id): StreamedResponse|JsonResponse
    {
        $export = ReportExport::find($id);

        \Log::info('Export ID: ' . $id . ' Export Format: ' . $export->format);

        if (!$export) {
            return response()->json([
                'success' => false,
                'message' => 'Export not found',
            ], 404);
        }

        // Authorization: only owner can download
        if ($export->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        // Check if expired
        if ($export->isExpired()) {
            // Mark as expired if not already
            if ($export->status !== ReportExport::STATUS_EXPIRED) {
                $export->update(['status' => ReportExport::STATUS_EXPIRED]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Export has expired',
            ], 410);
        }

        // Check if ready
        if (!$export->isReady()) {
            return response()->json([
                'success' => false,
                'message' => 'Export is not ready yet',
                'status' => $export->status,
            ], 409);
        }

        // Get temp file path
        $filePath = $this->service->getExistingTempFile($export);

        \Log::info("filePath : " . $filePath);

        if (!$filePath || !file_exists($filePath)) {
            return response()->json([
                'success' => false,
                'message' => 'Export file not found',
            ], 404);
        }

        $filename = "report_{$export->report_type}_{$export->id}.{$export->format}";
        $mimeType = $export->format === 'csv'
            ? 'text/csv'
            : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';

        // Stream the file and delete after
        return response()->streamDownload(function () use ($filePath, $export) {
            $handle = fopen($filePath, 'rb');
            while (!feof($handle)) {
                echo fread($handle, 8192);
                flush();
            }
            fclose($handle);

            // Delete temp file after streaming
            $this->service->deleteTempFile($export);

            // Mark as expired since file is now deleted
            $export->update(['status' => ReportExport::STATUS_EXPIRED]);
        }, $filename, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * List user's exports.
     * 
     * GET /api/admin/reports/exports
     */
    public function index(): JsonResponse
    {
        $exports = ReportExport::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get(['id', 'report_type', 'format', 'status', 'expires_at', 'created_at']);

        return response()->json([
            'success' => true,
            'data' => $exports,
        ]);
    }
}
