<?php

declare(strict_types=1);

namespace TeamInfinityLK\HestiaCP\Exceptions;

class ApiException extends HestiaException
{
    public function __construct(
        string $message,
        int $code = 500,
        ?\Exception $previous = null,
        ?array $responseData = null
    ) {
        parent::__construct($message, $code, $previous, $responseData);
    }
}