<?php

declare(strict_types=1);

namespace Paulnovikov\RestClient;

use GuzzleHttp\Client;
use Paulnovikov\RestClient\API\ProducerApi;
use Paulnovikov\RestClient\Http\GuzzleHttpClient;
use Paulnovikov\RestClient\Http\HttpClientInterface;
use Psr\Log\LoggerInterface;

final readonly class ApiClient
{
    public function __construct(private HttpClientInterface $httpClient) {}

    public static function create(
        string $baseUrl,
        ?string $username = null,
        ?string $password = null,
        array $options = [],
        ?string $hostHeader = null
    ): self {
        $config = $options;

        if ($username !== null && $password !== null) {
            $config['auth'] = [$username, $password];
        }

        $guzzle = new Client($config);
        $httpClient = new GuzzleHttpClient($guzzle, $baseUrl, $hostHeader);

        return new self($httpClient);
    }

    public function producers(?LoggerInterface $logger = null): ProducerApi
    {
        return new ProducerApi($this->httpClient, $logger);
    }
}
