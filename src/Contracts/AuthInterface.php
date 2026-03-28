<?php

declare(strict_types=1);

namespace TeamInfinityLK\HestiaCP\Contracts;

use TeamInfinityLK\HestiaCP\Http\Connector;

interface AuthInterface
{
    public function authenticate(Connector $connector): self;

    public function getIdentifier(): ?string;
}