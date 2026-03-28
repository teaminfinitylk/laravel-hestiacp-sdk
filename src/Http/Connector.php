<?php

declare(strict_types=1);

namespace TeamInfinityLK\HestiaCP\Http;

use TeamInfinityLK\HestiaCP\Exceptions\ConnectionException;

class Connector
{
    private string $baseUrl;

    // Modern access key auth (recommended)
    private ?string $accessKeyId     = null;
    private ?string $secretAccessKey = null;

    // Legacy auth (deprecated but still supported)
    private ?string $apiKey   = null;
    private ?string $username = null;
    private ?string $password = null;

    private int $timeout    = 30;
    private bool $sslVerify = true;

    // HestiaCP uses a SINGLE endpoint for ALL commands
    private const API_ENDPOINT = '/api/index.php';

    public function __construct(string $baseUrl)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    /**
     * Modern auth: ACCESS_KEY_ID:SECRET_ACCESS_KEY
     * Pass combined string or set separately
     */
    public function setApiKey(string $apiKey): self
    {
        // Support both "ACCESSKEY:SECRETKEY" combined format and plain legacy key
        if (str_contains($apiKey, ':')) {
            [$this->accessKeyId, $this->secretAccessKey] = explode(':', $apiKey, 2);
        } else {
            // Legacy single key
            $this->apiKey = $apiKey;
        }
        return $this;
    }

    public function setAccessKey(string $accessKeyId, string $secretAccessKey): self
    {
        $this->accessKeyId     = $accessKeyId;
        $this->secretAccessKey = $secretAccessKey;
        return $this;
    }

    public function setCredentials(string $username, string $password): self
    {
        $this->username = $username;
        $this->password = $password;
        return $this;
    }

    public function setTimeout(int $timeout): self
    {
        $this->timeout = $timeout;
        return $this;
    }

    public function setSslVerify(bool $verify): self
    {
        $this->sslVerify = $verify;
        return $this;
    }

    /**
     * Execute a HestiaCP CLI command via the API.
     *
     * Per official docs, POST to /api/index.php with:
     *   hash       = "ACCESS_KEY_ID:SECRET_ACCESS_KEY"  (modern)
     *   user       = username (legacy)
     *   password   = password (legacy)
     *   returncode = "yes"  (for action commands that return a status code)
     *   cmd        = "v-list-users" (the CLI command)
     *   arg1..argN = command arguments
     *
     * IMPORTANT: Do NOT pass returncode=yes for list/get commands.
     * When returncode=yes is set, HestiaCP returns only the numeric
     * exit code ("0") and suppresses the actual JSON output.
     * Use $returnCode=false for any command that returns data (v-list-*, v-get-*).
     * Use $returnCode=true  for action commands (v-add-*, v-delete-*, v-change-*).
     *
     * @param string $cmd        e.g. 'v-list-users', 'v-add-web-domain'
     * @param array  $args       positional arguments for the command
     * @param bool   $returnCode set false for list/get commands to receive JSON output
     */
    public function execute(string $cmd, array $args = [], bool $returnCode = true): Response
    {
        $postFields = ['cmd' => $cmd];

        // Only add returncode=yes for action commands (add/delete/change/suspend etc.)
        // List/get commands must NOT have this flag or HestiaCP returns "0" instead of JSON
        if ($returnCode) {
            $postFields['returncode'] = 'yes';
        }

        // Auth — priority: modern access key > legacy api key > username/password
        if ($this->accessKeyId !== null && $this->secretAccessKey !== null) {
            // Modern: hash = "ACCESS_KEY_ID:SECRET_ACCESS_KEY"
            $postFields['hash'] = $this->accessKeyId . ':' . $this->secretAccessKey;
        } elseif ($this->apiKey !== null) {
            // Legacy single hash key
            $postFields['hash'] = $this->apiKey;
        } elseif ($this->username !== null && $this->password !== null) {
            // Legacy username + password
            $postFields['user']     = $this->username;
            $postFields['password'] = $this->password;
        }

        // Map args to arg1, arg2, arg3...
        $i = 1;
        foreach ($args as $value) {
            $postFields['arg' . $i] = $value;
            $i++;
        }

        return $this->post(self::API_ENDPOINT, $postFields);
    }

    public function post(string $endpoint, array $data = []): Response
    {
        $url = $this->baseUrl . $endpoint;

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => $this->timeout,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS      => 3,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($data),
            CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
            CURLOPT_SSL_VERIFYPEER => $this->sslVerify,
            CURLOPT_SSL_VERIFYHOST => $this->sslVerify ? 2 : 0,
        ]);

        if (!$this->sslVerify) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYSTATUS, false);
        }

        $body   = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error  = curl_error($ch);
        $errno  = curl_errno($ch);
        curl_close($ch);

        if ($errno !== 0) {
            throw new ConnectionException(sprintf('cURL error [%d]: %s', $errno, $error));
        }

        return new Response(
            statusCode: $status,
            statusMessage: '',
            body: is_string($body) ? $body : null,
        );
    }

    // Keep for backwards compatibility
    public function get(string $endpoint, array $data = []): Response
    {
        return $this->post(self::API_ENDPOINT, $data);
    }
}