<?php

declare(strict_types=1);

namespace Paulnovikov\RestClient\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Paulnovikov\RestClient\Exception\HttpRequestException;
use Psr\Http\Message\ResponseInterface;

final readonly class GuzzleHttpClient implements HttpClientInterface
{
    public function __construct(private Client $client) {}

    public function request(
        string $method,
        string $url,
        array $headers = [],
        ?string $body = null
    ): ResponseInterface {
        try {
            return $this->client->request($method, $url, [
                'headers' => $headers,
                'body' => $body,
            ]);
        } catch (GuzzleException $e) {
            throw new HttpRequestException(
                'HTTP request failed',
                0,
                $e
            );
        }
    }
}