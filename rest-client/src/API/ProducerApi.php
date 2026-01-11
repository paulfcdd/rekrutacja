<?php

declare(strict_types=1);

namespace Paulnovikov\RestClient\API;

use JsonException;
use Paulnovikov\RestClient\Exception\UnexpectedApiResponseException;
use Paulnovikov\RestClient\Http\HttpClientInterface;
use Paulnovikov\RestClient\Mapper\ProducerMapper;
use Paulnovikov\RestClient\Model\Producer\ProducerData;
use Paulnovikov\RestClient\Model\Producer\ProducerCreate;
use Psr\Log\LoggerInterface;

class ProducerApi extends AbstractApi
{
    public function __construct(
        HttpClientInterface $httpClient,
        private readonly LoggerInterface $logger
    ) {
        parent::__construct($httpClient);
    }

    /**
     * @return ProducerData[]
     *
     * @throws JsonException
     */
    public function getAll(): array
    {
        $data = $this->request('GET', 'shop_api/v1/producers', 200);
        $producers = [];
        $mapper = new ProducerMapper();

        foreach ($data as $item) {
            try {
                if (!is_array($item)) {
                    throw new UnexpectedApiResponseException('Invalid producer response');
                }

                $producers[] = $mapper->transformFromApiResponse($item);
            } catch (UnexpectedApiResponseException $e) {
                $this->logger->warning('Invalid producer record', [
                    'error' => $e->getMessage(),
                    'item' => $item,
                ]);
            }
        }

        return $producers;
    }

    /**
     * @throws JsonException
     */
    public function create(ProducerCreate $producer): ProducerData
    {
        $data = $this->request(
            'POST',
            'shop_api/v1/producers',
            201,
            [],
            $producer->toPayload()
        );

        return (new ProducerMapper())->transformFromApiResponse($data);
    }
}
