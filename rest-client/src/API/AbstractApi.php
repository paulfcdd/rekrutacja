<?php

declare(strict_types=1);

namespace Paulnovikov\RestClient\API;

use JsonException;
use Paulnovikov\RestClient\Exception\ApiException;
use Paulnovikov\RestClient\Exception\UnexpectedApiResponseException;
use Paulnovikov\RestClient\Http\HttpClientInterface;
use Psr\Http\Message\ResponseInterface;

abstract class AbstractApi
{
    public function __construct(protected readonly HttpClientInterface $httpClient) {}

    /**
     * @throws JsonException
     */
    protected function request(
        string $method,
        string $path,
        int $expectedStatus,
        array $headers = [],
        ?array $payload = null
    ): array {
        $response = $this->sendRequest($method, $path, $headers, $payload);
        $this->assertStatus($expectedStatus, $response, $method, $path);

        return $this->decodeJson($response);
    }

    /**
     * @throws JsonException
     */
    protected function sendRequest(
        string $method,
        string $path,
        array $headers = [],
        ?array $payload = null
    ): ResponseInterface {
        $body = null;

        if ($payload !== null) {
            $headers += ['Content-Type' => 'application/json'];
            $body = json_encode($payload, JSON_THROW_ON_ERROR);
        }

        return $this->httpClient->request($method, $path, $headers, $body);
    }

    protected function assertStatus(
        int $expectedStatus,
        ResponseInterface $response,
        string $method,
        string $path
    ): void {
        $statusCode = $response->getStatusCode();

        if ($statusCode !== $expectedStatus) {
            $body = $this->truncateBody((string) $response->getBody());
            $url = $this->buildUrl($path);
            $message = sprintf(
                'Unexpected HTTP status %d for %s %s',
                $statusCode,
                $method,
                $url
            );

            if ($body !== '') {
                $message .= sprintf(': %s', $body);
            }

            throw new ApiException($message, $url, $method, $statusCode, $body);
        }
    }

    /**
     * @throws JsonException
     */
    protected function decodeJson(ResponseInterface $response): array
    {
        $data = json_decode(
            (string) $response->getBody(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        if (!is_array($data)) {
            throw new UnexpectedApiResponseException('Invalid API response');
        }

        return $data;
    }

    private function truncateBody(string $body, int $limit = 2048): string
    {
        if (strlen($body) <= $limit) {
            return $body;
        }

        return substr($body, 0, $limit) . '...';
    }

    private function buildUrl(string $path): string
    {
        $baseUri = rtrim($this->httpClient->getBaseUri(), '/');
        $path = ltrim($path, '/');

        if ($baseUri === '') {
            return $path;
        }

        return $baseUri . '/' . $path;
    }
}
