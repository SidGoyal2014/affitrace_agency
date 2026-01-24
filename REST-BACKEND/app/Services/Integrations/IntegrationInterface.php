<?php

namespace App\Services\Integrations;

interface IntegrationInterface
{
    /**
     * Test the API connection.
     * @return bool
     */
    public function testConnection(): bool;

    /**
     * Fetch all offers (or active ones) from the provider.
     * @return array
     */
    public function getOffers(): array;

    /**
     * Fetch all affiliates from the provider.
     * @return array
     */
    public function getAffiliates(): array;

    /**
     * Fetch all advertisers from the provider.
     * @return array
     */
    public function getAdvertisers(): array;

    /**
     * Set configuration (API keys, etc.)
     * @param array $config
     * @return void
     */
    public function setConfig(array $config): void;
}
