<?php

declare(strict_types=1);

namespace Paulnovikov\RestClient\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Paulnovikov\RestClient\Exception\ApiException;
use Psr\Http\Message\ResponseInterface;

final readonly class GuzzleHttpClient implements HttpClientInterface
{
    public function __construct(
        private Client $client,
        private string $baseUri,
        private ?string $hostHeader = null
    ) {}

    public function request(
        string $method,
        string $url,
        array $headers = [],
        ?string $body = null
    ): ResponseInterface {
        $resolvedUrl = $this->resolveUrl($url);

        try {
            return $this->client->request($method, $resolvedUrl, [
                'headers' => $this->buildHeaders($headers),
                'body' => $body,
            ]);
        } catch (RequestException $e) {
            $response = $e->getResponse();
            $statusCode = $response?->getStatusCode();
            $bodyContents = $response ? (string) $response->getBody() : '';
            $message = sprintf(
                'HTTP request failed for %s %s',
                $method,
                $resolvedUrl
            );

            if ($statusCode !== null) {
                $message .= sprintf(' with status %d', $statusCode);
            }

            if ($bodyContents !== '') {
                $message .= sprintf(': %s', $bodyContents);
            }

            throw new ApiException(
                $message,
                $resolvedUrl,
                $statusCode,
                $bodyContents,
                $e
            );
        } catch (GuzzleException $e) {
            throw new ApiException(
                sprintf('HTTP request failed for %s %s', $method, $resolvedUrl),
                $resolvedUrl,
                null,
                null,
                $e
            );
        }
    }

    private function resolveUrl(string $url): string
    {
        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return $url;
        }

        $baseUri = rtrim($this->baseUri, '/');
        $path = ltrim($url, '/');

        if ($baseUri === '') {
            return $path;
        }

        return $baseUri . '/' . $path;
    }

    private function buildHeaders(array $headers): array
    {
        if ($this->hostHeader === null || $this->hostHeader === '') {
            return $headers;
        }

        $headers['Host'] = $this->hostHeader;

        return $headers;
    }
}
