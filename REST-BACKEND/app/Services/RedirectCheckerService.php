<?php

namespace App\Services;

use App\Models\RedirectCheck;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RedirectCheckerService
{
    const USER_AGENTS = [
        'desktop_windows' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
        'desktop_mac'     => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 14_4) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.4 Safari/605.1.15',
        'mobile_ios'      => 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.4 Mobile/15E148 Safari/604.1',
        'mobile_android'  => 'Mozilla/5.0 (Linux; Android 14; Pixel 8) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.6367.82 Mobile Safari/537.36',
        'tablet_ios'      => 'Mozilla/5.0 (iPad; CPU OS 17_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.4 Mobile/15E148 Safari/604.1',
        'tablet_android'  => 'Mozilla/5.0 (Linux; Android 14; SM-X710) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.6367.82 Safari/537.36',
        'bot_googlebot'   => 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)',
    ];

    const MAX_HOPS = 20;

    public function trace(string $url, ?string $proxy, string $device): RedirectCheck
    {
        $record = RedirectCheck::create([
            'user_id'   => Auth::id(),
            'input_url' => $url,
            'proxy'     => $proxy ?: null,
            'device'    => $device,
            'status'    => RedirectCheck::STATUS_PENDING,
        ]);

        try {
            [$hops, $finalUrl] = $this->followRedirects($url, $proxy, $device);

            $record->update([
                'final_url' => $finalUrl,
                'hops'      => $hops,
                'hop_count' => count($hops),
                'status'    => RedirectCheck::STATUS_COMPLETED,
            ]);
        } catch (\Throwable $e) {
            Log::warning('RedirectCheckerService: trace failed', [
                'url'   => $url,
                'error' => $e->getMessage(),
            ]);
            $record->update([
                'status'        => RedirectCheck::STATUS_FAILED,
                'error_message' => $e->getMessage(),
            ]);
        }

        return $record->fresh();
    }

    private function followRedirects(string $startUrl, ?string $proxy, string $device): array
    {
        $userAgent = self::USER_AGENTS[$device] ?? self::USER_AGENTS['desktop_windows'];

        $clientOptions = [
            'allow_redirects' => false,
            'timeout'         => 15,
            'connect_timeout' => 10,
            'verify'          => false,
            'headers'         => [
                'User-Agent'      => $userAgent,
                'Accept'          => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language' => 'en-US,en;q=0.5',
            ],
        ];

        if ($proxy) {
            $clientOptions['proxy'] = $this->parseProxyString($proxy);
        }

        $client     = new Client($clientOptions);
        $hops       = [];
        $currentUrl = $startUrl;
        $hopNumber  = 1;

        while ($hopNumber <= self::MAX_HOPS) {
            $hopStart = microtime(true); // ← start timer for this hop

            try {
                $response   = $client->get($currentUrl);
                $statusCode = $response->getStatusCode();
                $location   = null;

                if ($statusCode >= 300 && $statusCode < 400) {
                    $location = $response->getHeaderLine('Location');
                    if ($location && !preg_match('#^https?://#i', $location)) {
                        $location = $this->resolveRelativeUrl($currentUrl, $location);
                    }
                }

                $hops[] = [
                    'hop'         => $hopNumber,
                    'url'         => $currentUrl,
                    'status_code' => $statusCode,
                    'redirect_to' => $location,
                    'time_ms'     => round((microtime(true) - $hopStart) * 1000, 2),
                ];

                if (!$location) {
                    break;
                }

                $currentUrl = $location;
                $hopNumber++;

            } catch (ConnectException $e) {
                $hops[] = [
                    'hop'         => $hopNumber,
                    'url'         => $currentUrl,
                    'status_code' => 0,
                    'redirect_to' => null,
                    'time_ms'     => round((microtime(true) - $hopStart) * 1000, 2),
                    'error'       => 'Connection failed: ' . $e->getMessage(),
                ];
                break;
            } catch (RequestException $e) {
                $statusCode = $e->hasResponse() ? $e->getResponse()->getStatusCode() : 0;
                $hops[] = [
                    'hop'         => $hopNumber,
                    'url'         => $currentUrl,
                    'status_code' => $statusCode,
                    'redirect_to' => null,
                    'time_ms'     => round((microtime(true) - $hopStart) * 1000, 2),
                    'error'       => $e->getMessage(),
                ];
                break;
            }
        }

        if ($hopNumber > self::MAX_HOPS) {
            $hops[] = [
                'hop'         => $hopNumber,
                'url'         => $currentUrl,
                'status_code' => 0,
                'redirect_to' => null,
                'time_ms'     => 0,
                'error'       => 'Max redirect limit (' . self::MAX_HOPS . ') reached',
            ];
        }

        $finalUrl = $hops[count($hops) - 1]['url'] ?? $startUrl;

        return [$hops, $finalUrl];
    }

    private function resolveRelativeUrl(string $base, string $relative): string
    {
        $parsed = parse_url($base);
        $scheme = $parsed['scheme'] ?? 'https';
        $host   = $parsed['host']   ?? '';
        $port   = isset($parsed['port']) ? ':' . $parsed['port'] : '';

        if (str_starts_with($relative, '//')) {
            return $scheme . ':' . $relative;
        }

        if (str_starts_with($relative, '/')) {
            return $scheme . '://' . $host . $port . $relative;
        }

        $basePath = isset($parsed['path']) ? dirname($parsed['path']) : '/';
        return $scheme . '://' . $host . $port . rtrim($basePath, '/') . '/' . ltrim($relative, '/');
    }

    public function testProxy(string $proxy): array
    {
        $proxyUrl = $this->parseProxyString($proxy);

        $client = new Client([
            'proxy'           => $proxyUrl,
            'timeout'         => 15,
            'connect_timeout' => 10,
            'verify'          => false,
        ]);

        $response = $client->get('http://ip-api.com/json?fields=status,message,country,countryCode,city,isp,query');
        $data     = json_decode($response->getBody()->getContents(), true);

        if (($data['status'] ?? '') !== 'success') {
            throw new \Exception($data['message'] ?? 'ip-api.com returned a non-success status.');
        }

        return [
            'ip'           => $data['query']       ?? null,
            'country'      => $data['country']     ?? null,
            'country_code' => $data['countryCode'] ?? null,
            'city'         => $data['city']        ?? null,
            'isp'          => $data['isp']         ?? null,
        ];
    }


    private function parseProxyString(string $proxy): string
    {
        $proxy = trim($proxy);

        if (preg_match('#^(https?|socks5)://#i', $proxy)) {
            return $proxy;
        }

        $parts = explode(':', $proxy, 4);

        if (count($parts) === 4) {
            [$host, $port, $user, $pass] = $parts;
            return 'http://' . rawurlencode($user) . ':' . rawurlencode($pass) . '@' . $host . ':' . $port;
        }

        if (count($parts) === 2) {
            [$host, $port] = $parts;
            return 'http://' . $host . ':' . $port;
        }

        return $proxy;
    }

}