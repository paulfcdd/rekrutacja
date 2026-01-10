<?php

declare(strict_types=1);

namespace Paulnovikov\RestClient\Exception;

use RuntimeException;
use Throwable;

class HttpRequestException extends RuntimeException
{
    public function __construct(
        string $message = 'HTTP request failed',
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}