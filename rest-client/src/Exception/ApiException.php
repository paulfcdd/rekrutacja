<?php

declare(strict_types=1);

namespace Paulnovikov\RestClient\Exception;

use RuntimeException;
use Throwable;

final class ApiException extends RuntimeException
{
    public function __construct(
        string $message,
        private string $url,
        private ?int $statusCode = null,
        private ?string $responseBody = null,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, 0, $previous);
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getStatusCode(): ?int
    {
        return $this->statusCode;
    }

    public function getResponseBody(): ?string
    {
        return $this->responseBody;
    }
}
