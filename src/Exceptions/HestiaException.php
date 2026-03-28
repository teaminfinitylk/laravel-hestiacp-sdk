<?php

declare(strict_types=1);

namespace TeamInfinityLK\HestiaCP\Exceptions;

use Exception;

class HestiaException extends Exception
{
    public function __construct(
        string $message,
        int $code = 0,
        ?Exception $previous = null,
        public readonly ?array $responseData = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}