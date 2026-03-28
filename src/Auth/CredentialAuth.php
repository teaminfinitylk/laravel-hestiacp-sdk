<?php

declare(strict_types=1);

namespace TeamInfinityLK\HestiaCP\Auth;

use TeamInfinityLK\HestiaCP\Contracts\AuthInterface;
use TeamInfinityLK\HestiaCP\Http\Connector;

class CredentialAuth implements AuthInterface
{
    private ?string $identifier = null;

    public function __construct(
        private readonly string $username,
        private readonly string $password
    ) {}

    public function authenticate(Connector $connector): self
    {
        $connector->setCredentials($this->username, $this->password);
        $this->identifier = $this->username;
        return $this;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }
}