<?php

declare(strict_types=1);

namespace Paulnovikov\RestClient\Producer;

use Paulnovikov\RestClient\Exception\HttpRequestException;
use Paulnovikov\RestClient\Exception\UnexpectedApiResponseException;
use Paulnovikov\RestClient\Http\HttpClientInterface;
use JsonException;
use Paulnovikov\RestClient\Producer\Mapper\ProducerMapper;
use Paulnovikov\RestClient\Producer\Model\Producer;
use Paulnovikov\RestClient\Producer\Model\ProducerCreate;

final readonly class ProducerApi
{
    public function __construct(private HttpClientInterface $httpClient) {}

    /**
     * @return Producer[]
     *
     * @throws JsonException
     */
    public function getAll(): array
    {
        $response = $this->httpClient->request(
            'GET',
            '/shop_api/v1/producers'
        );

        if ($response->getStatusCode() !== 200) {
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
            throw new UnexpectedApiResponseException('Invalid producers response');
        }

        $producers = [];

        foreach ($data as $item) {
            try {
                $producers[] = ProducerMapper::transformFromApiResponse($item);
            } catch (UnexpectedApiResponseException) {
                //TODO: add some logger
            }
        }

        return $producers;
    }

    /**
     * @throws JsonException
     */
    public function create(ProducerCreate $producer): Producer
    {
        $response = $this->httpClient->request(
            'POST',
            '/shop_api/v1/producers',
            ['Content-Type' => 'application/json'],
            json_encode($producer->toPayload(), JSON_THROW_ON_ERROR)
        );

        if ($response->getStatusCode() !== 201) {
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

        return ProducerMapper::transformFromApiResponse($data);
    }
}