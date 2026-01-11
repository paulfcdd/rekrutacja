<?php

declare(strict_types=1);

namespace Paulnovikov\RestClient\API;

use JsonException;
use Paulnovikov\RestClient\Exception\HttpRequestException;
use Paulnovikov\RestClient\Exception\UnexpectedApiResponseException;
use Paulnovikov\RestClient\Http\HttpClientInterface;

abstract class AbstractApi
{
    public function __construct(protected HttpClientInterface $httpClient) {}

    /**
     * @throws JsonException
     */
    protected function requestJson(
        string $method,
        string $path,
        int $expectedStatus,
        array $headers = [],
        ?array $payload = null
    ): array {
        $body = null;

        if ($payload !== null) {
            $headers += ['Content-Type' => 'application/json'];
            $body = json_encode($payload, JSON_THROW_ON_ERROR);
        }

        $response = $this->httpClient->request($method, $path, $headers, $body);

        if ($response->getStatusCode() !== $expectedStatus) {
            throw new HttpRequestException(
                sprintf('Unexpected HTTP status %d', $response->getStatusCode())
            );
        }

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
}
