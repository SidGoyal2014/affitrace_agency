<?php

namespace App\Services\Integrations;

use Illuminate\Support\Facades\Http;
use Exception;

class TrackierProvider implements IntegrationInterface
{
    protected $config;
    // Trackier Base URL often depends on the client's domain, e.g., https://api.trackier.com or https://client.trackier.com
    protected $baseUrl = 'https://api.trackier.com';

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
            $response = $this->makeRequest('GET', '/network/advertisers');
            return isset($response['success']) && $response['success'] == true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getOffers(): array
    {
        $response = $this->makeRequest('GET', '/network/campaigns', ['limit' => 100]); // Trackier calls offers 'campaigns'
        return $response['campaigns'] ?? [];
    }

    public function getAffiliates(): array
    {
        $response = $this->makeRequest('GET', '/network/affiliates');
        return $response['affiliates'] ?? [];
    }

    public function getAdvertisers(): array
    {
        $response = $this->makeRequest('GET', '/network/advertisers');
        return $response['advertisers'] ?? [];
    }

    protected function makeRequest($method, $endpoint, $params = [])
    {
        $headers = [
            'x-api-key' => $this->config['api_key'], // Trackier usually uses x-api-key header
            'Accept' => 'application/json',
        ];

        $url = $this->baseUrl . $endpoint;

        $response = Http::withHeaders($headers)
            ->withOptions(['verify' => false])
            ->get($url, $params);

        if ($response->successful()) {
            return $response->json();
        }

        throw new Exception("API Request Failed: " . $response->body());
    }
}
