<?php

declare(strict_types=1);

namespace TeamInfinityLK\HestiaCP\Exceptions;

class ConnectionException extends HestiaException
{
    public function __construct(
        string $message,
        int $code = 0,
        ?\Exception $previous = null,
        ?array $responseData = null
    ) {
        parent::__construct($message, $code, $previous, $responseData);
    }
}