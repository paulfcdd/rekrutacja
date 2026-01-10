<?php

namespace Paulnovikov\RestClient\Http;

use Psr\Http\Message\ResponseInterface;

interface HttpClientInterface
{
    public function request(
        string $method,
        string $url,
        array $headers = [],
        ?string $body = null
    ): ResponseInterface;
}