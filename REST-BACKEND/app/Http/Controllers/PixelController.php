<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TrackingService;

/**
 * @group Tracking (Pixel)
 *
 * Pixel-based conversion tracking endpoints.
 * These endpoints do not require authentication.
 */
class PixelController extends Controller
{
    protected $trackingService;

    public function __construct(TrackingService $trackingService)
    {
        $this->trackingService = $trackingService;
    }

    /**
     * Fire Conversion Pixel
     *
     * Record a conversion via pixel tracking. Returns a 1x1 transparent GIF.
     *
     * @unauthenticated
     * @queryParam tid string The transaction/click ID. Example: abc123xyz
     * @queryParam aff_click_id string Alternative parameter for transaction ID. Example: abc123xyz
     * @response 200 1x1 transparent GIF image
     * @response 400 {"error": "Missing Transaction ID"}
     */
    public function track(Request $request)
    {
        $tid = $request->query('tid') ?? $request->query('aff_click_id');

        if (!$tid) {
            return response()->json(['error' => 'Missing Transaction ID'], 400);
        }

        // Delegate to service to record conversion
        // We assume processConversion returns details about the affiliate pixel to fire
        $result = $this->trackingService->processPixelConversion($request);

        if ($result['success'] == 'Y') {
            $pixelType = $result['pixel_type'] ?? 'image';
            $pixelCode = $result['pixel_code'] ?? '';

            if ($pixelType === 'iframe') {
                return response($pixelCode)->header('Content-Type', 'text/html');
            } elseif ($pixelType === 'script') {
                return response('<script>' . $pixelCode . '</script>')->header('Content-Type', 'text/html');
            } else {
                // Default to 1x1 GIF for image or generic success
                return response(base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'))
                    ->header('Content-Type', 'image/gif');
            }
        }

        // Return empty 1x1 GIF even on failure to avoid broken image icon
        return response(base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'))
            ->header('Content-Type', 'image/gif');
    }
}
