<?php

declare(strict_types=1);

namespace Paulnovikov\RestClient\Tests\Support;

use Paulnovikov\RestClient\Http\HttpClientInterface;
use Psr\Http\Message\ResponseInterface;

final class RecordingHttpClient implements HttpClientInterface
{
    public array $requests = [];

    public function __construct(private ResponseInterface $response) {}

    public function request(
        string $method,
        string $url,
        array $headers = [],
        ?string $body = null
    ): ResponseInterface {
        $this->requests[] = [
            'method' => $method,
            'url' => $url,
            'headers' => $headers,
            'body' => $body,
        ];

        return $this->response;
    }
}
