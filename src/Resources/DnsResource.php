<?php

declare(strict_types=1);

namespace TeamInfinityLK\HestiaCP\Resources;

use TeamInfinityLK\HestiaCP\Contracts\ResourceInterface;
use TeamInfinityLK\HestiaCP\DTOs\DnsRecordDto;
use TeamInfinityLK\HestiaCP\Http\Connector;
use TeamInfinityLK\HestiaCP\Exceptions\ApiException;

class DnsResource implements ResourceInterface
{
    public function __construct(
        private readonly Connector $connector
    ) {}

    public function list(): array
    {
        $response = $this->connector->get('/api/v1/list/dns/');

        if (!$response->isSuccessful()) {
            throw new ApiException('Failed to list DNS zones: ' . $response->get('error', 'Unknown error'));
        }

        return $response->get('data', []);
    }

    public function get(string $id): ?array
    {
        $response = $this->connector->get('/api/v1/list/dns/' . $id);

        if (!$response->isSuccessful()) {
            return null;
        }

        return $response->get('data', []) ?? null;
    }

    public function create(array $data): array
    {
        $response = $this->connector->post('/api/v1/add/dns/', $data);

        if (!$response->isSuccessful()) {
            throw new ApiException('Failed to create DNS zone: ' . $response->get('error', 'Unknown error'));
        }

        return $response->all();
    }

    public function update(string $id, array $data): array
    {
        $response = $this->connector->post('/api/v1/edit/dns/' . $id, $data);

        if (!$response->isSuccessful()) {
            throw new ApiException('Failed to update DNS zone: ' . $response->get('error', 'Unknown error'));
        }

        return $response->all();
    }

    public function delete(string $id): bool
    {
        $response = $this->connector->post('/api/v1/delete/dns/', ['domain' => $id]);

        return $response->isSuccessful();
    }

    public function listRecords(string $domain): array
    {
        $response = $this->connector->get('/api/v1/list/dns/' . $domain);

        if (!$response->isSuccessful()) {
            throw new ApiException('Failed to list DNS records: ' . $response->get('error', 'Unknown error'));
        }

        return array_map(
            fn(array $data) => DnsRecordDto::fromArray($data),
            $response->get('data', [])
        );
    }

    public function addRecord(string $domain, array $data): array
    {
        $response = $this->connector->post('/api/v1/add/dns/' . $domain, $data);

        if (!$response->isSuccessful()) {
            throw new ApiException('Failed to add DNS record: ' . $response->get('error', 'Unknown error'));
        }

        return $response->all();
    }

    public function deleteRecord(string $domain, int $recordId): bool
    {
        $response = $this->connector->post('/api/v1/delete/dns/' . $domain, ['record_id' => $recordId]);

        return $response->isSuccessful();
    }
}