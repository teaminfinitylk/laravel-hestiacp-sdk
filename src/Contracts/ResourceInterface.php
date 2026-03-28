<?php

declare(strict_types=1);

namespace TeamInfinityLK\HestiaCP\Contracts;

use TeamInfinityLK\HestiaCP\Http\Connector;

/**
 * Marker interface for HestiaCP resource managers.
 *
 * Note: HestiaCP commands are user-scoped (most require USER as the first arg),
 * so it is not possible to enforce a uniform list/get/create/update/delete
 * signature here. Each resource class defines its own user-aware methods.
 */
interface ResourceInterface
{
    public function __construct(Connector $connector);
}