<?php

return [
    /*
    |--------------------------------------------------------------------------
    | HestiaCP Server URL
    |--------------------------------------------------------------------------
    |
    | The base URL of your HestiaCP server.
    |
    */
    'base_url' => env('HESTIA_URL', 'https://localhost:8083'),

    /*
    |--------------------------------------------------------------------------
    | API Key Authentication
    |--------------------------------------------------------------------------
    |
    | Use API key for authentication. Get this from your HestiaCP panel.
    |
    */
    'api_key' => env('HESTIA_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Username / Password Authentication
    |--------------------------------------------------------------------------
    |
    | Alternative authentication method using username and password.
    |
    */
    'username' => env('HESTIA_USERNAME'),
    'password' => env('HESTIA_PASSWORD'),

    /*
    |--------------------------------------------------------------------------
    | Timeout
    |--------------------------------------------------------------------------
    |
    | Request timeout in seconds.
    |
    */
    'timeout' => env('HESTIA_TIMEOUT', 30),
];