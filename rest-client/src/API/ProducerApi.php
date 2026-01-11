<?php

declare(strict_types=1);

namespace Paulnovikov\RestClient\API;

use JsonException;
use Paulnovikov\RestClient\Exception\UnexpectedApiResponseException;
use Paulnovikov\RestClient\Mapper\ProducerMapper;
use Paulnovikov\RestClient\Model\Producer\Producer;
use Paulnovikov\RestClient\Model\Producer\ProducerCreate;

class ProducerApi extends AbstractApi
{
    /**
     * @return Producer[]
     *
     * @throws JsonException
     */
    public function getAll(): array
    {
        $data = $this->requestJson('GET', 'shop_api/v1/producers', 200);
        $producers = [];
        $mapper = new ProducerMapper();

        foreach ($data as $item) {
            if (!is_array($item)) {
                throw new UnexpectedApiResponseException('Invalid producer response');
            }

            $producers[] = $mapper->transformFromApiResponse($item);
        }

        return $producers;
    }

    /**
     * @throws JsonException
     */
    public function create(ProducerCreate $producer): Producer
    {
        $data = $this->requestJson(
            'POST',
            'shop_api/v1/producers',
            201,
            [],
            $producer->toPayload()
        );

        return (new ProducerMapper())->transformFromApiResponse($data);
    }
}
