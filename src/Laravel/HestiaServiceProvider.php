<?php

declare(strict_types=1);

namespace TeamInfinityLK\HestiaCP\Laravel;

use Illuminate\Support\ServiceProvider;
use TeamInfinityLK\HestiaCP\HestiaClient;

class HestiaServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(HestiaClient::class, function ($app) {
            $config = $app['config']['hestia'] ?? [];

            $baseUrl = $config['base_url'] ?? 'https://localhost:8083';
            $timeout = $config['timeout'] ?? 30;

            $client = new HestiaClient($baseUrl, $timeout);

            if (!empty($config['api_key'])) {
                $client->authenticateWithApiKey($config['api_key']);
            } elseif (!empty($config['username']) && !empty($config['password'])) {
                $client->authenticateWithCredentials($config['username'], $config['password']);
            }

            return $client;
        });
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/hestia.php' => config_path('hestia.php'),
        ], 'hestia-config');
    }
}