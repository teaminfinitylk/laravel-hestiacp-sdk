<?php

declare(strict_types=1);

namespace TeamInfinityLK\HestiaCP\Auth;

use TeamInfinityLK\HestiaCP\Contracts\AuthInterface;
use TeamInfinityLK\HestiaCP\Http\Connector;

class ApiKeyAuth implements AuthInterface
{
    private ?string $identifier = null;

    public function __construct(
        private readonly string $apiKey
    ) {}

    public function authenticate(Connector $connector): self
    {
        $connector->setApiKey($this->apiKey);
        $this->identifier = $this->apiKey;
        return $this;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }
}