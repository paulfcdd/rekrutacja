<?php
declare(strict_types=1);

namespace Paulnovikov\RestClient;

use Paulnovikov\RestClient\Http\HttpClientInterface;
use Paulnovikov\RestClient\Producer\ProducerApi;

final readonly class ApiClient
{
    public function __construct(private HttpClientInterface $httpClient) {}

    public function producers(): ProducerApi
    {
        return new ProducerApi($this->httpClient);
    }
}