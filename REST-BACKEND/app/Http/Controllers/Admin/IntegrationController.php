<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Integration;
use App\Services\IntegrationService;

class IntegrationController extends Controller
{
    protected $integrationService;

    public function __construct(IntegrationService $integrationService)
    {
        $this->integrationService = $integrationService;
    }

    public function get_config()
    {
        // Assuming single integration per provider for now, or just returning the first active one
        // or list all. 
        $integrations = Integration::all();
        return response()->json(['status' => 1, 'data' => $integrations]);
    }

    public function save_config(Request $request)
    {
        $request->validate([
            'provider' => 'required|in:offer18,trackier',
            'api_key' => 'required',
            // 'account_id' is optional for some providers but typically needed
        ]);

        $integration = Integration::updateOrCreate(
            ['provider' => $request->provider],
            [
                'account_id' => $request->account_id,
                'api_key' => $request->api_key,
                'api_secret' => $request->api_secret,
                'api_domain' => $request->api_domain,
                'status' => $request->status ?? 'active',
                'config' => $request->config
            ]
        );

        return response()->json(['status' => 1, 'message' => 'Configuration saved successfully', 'data' => $integration]);
    }

    public function sync(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:integrations,id'
        ]);

        try {
            $result = $this->integrationService->syncData($request->id);
            return response()->json(['status' => 1, 'message' => $result['message']]);
        } catch (\Exception $e) {
            return response()->json(['status' => 0, 'error' => $e->getMessage()], 500);
        }
    }

    public function upload_csv(Request $request, \App\Services\CsvImportService $csvService)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
            'type' => 'required|in:advertiser,affiliate,clicks,conversions'
        ]);

        try {
            $file = $request->file('csv_file');
            $result = $csvService->import($file, $request->type);

            return response()->json([
                'status' => 1,
                'message' => "Import processed: {$result['processed']} success, {$result['failed']} failed.",
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 0, 'error' => $e->getMessage()], 500);
        }
    }
}
