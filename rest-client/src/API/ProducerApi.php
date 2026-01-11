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
    private const PRODUCERS_PATH = 'producers';

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
        $data = $this->request('GET', self::PRODUCERS_PATH, 200);
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
    public function create(array $producerData): ProducerData
    {
        $producer = ProducerCreate::fromArray($producerData);

        $data = $this->request(
            'POST',
            self::PRODUCERS_PATH,
            201,
            [],
            $producer->toPayload()
        );

        return (new ProducerMapper())->transformFromApiResponse($data);
    }
}
