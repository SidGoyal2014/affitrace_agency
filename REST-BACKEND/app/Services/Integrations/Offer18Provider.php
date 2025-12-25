<?php

namespace App\Services\Integrations;

use Illuminate\Support\Facades\Http;
use Exception;

class Offer18Provider implements IntegrationInterface
{
    protected $config;
    protected $baseUrl = 'https://api.offer18.com/api';

    public function setConfig(array $config): void
    {
        $this->config = $config;
        if (!empty($config['api_domain'])) {
            $this->baseUrl = rtrim($config['api_domain'], '/');
        }
    }

    public function testConnection(): bool
    {
        try {
            // Try fetching a single offer or checking account details
            $response = $this->makeRequest('GET', '/v1/advertisers'); // Assuming standard endpoint, verify documentation
            return isset($response['status']) && $response['status'] == true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getOffers(): array
    {
        // Example Endpoint: /api/v1/offers
        // Documentation specific implementation needed.
        // Assuming /api/v1/offers/list or similar
        // For Network API: /api/v1/offers
        $response = $this->makeRequest('GET', '/v1/offers', ['limit' => 500, 'status' => 'active']);

        return $response['data'] ?? [];
    }

    public function getAffiliates(): array
    {
        $response = $this->makeRequest('GET', '/v1/affiliates', ['limit' => 500]);
        return $response['data'] ?? [];
    }

    public function getAdvertisers(): array
    {
        $response = $this->makeRequest('GET', '/v1/advertisers', ['limit' => 500]);
        return $response['data'] ?? [];
    }

    protected function makeRequest($method, $endpoint, $params = [])
    {
        // Headers for Network API usually involve mid, api-key, secret-key
        // OR standard Basic/Bearer auth depending on version.

        $headers = [
            'Accept' => 'application/json',
        ];

        // Assuming Network API params structure based on general practices (needs exact doc confirmation)
        // If query params are used for auth:
        $authParams = [
            'mid' => $this->config['account_id'],
            'api_key' => $this->config['api_key'],
            // 'secret_key' => $this->config['api_secret'] ?? '', // Some endpoints might need this
        ];

        $url = $this->baseUrl . $endpoint;

        $response = Http::withHeaders($headers)
            ->withOptions(['verify' => false]) // Helpful for local dev if SSL issues
            ->get($url, array_merge($authParams, $params));

        if ($response->successful()) {
            return $response->json();
        }

        throw new Exception("API Request Failed: " . $response->body());
    }
}
