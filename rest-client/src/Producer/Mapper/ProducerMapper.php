<?php

declare(strict_types=1);

namespace Paulnovikov\RestClient\Producer\Mapper;

use Paulnovikov\RestClient\Exception\UnexpectedApiResponseException;
use Paulnovikov\RestClient\Producer\Model\Producer;

class ProducerMapper
{
    public static function transformFromApiResponse(
        array $data,
    ): Producer
    {
        self::assertInt($data['id'], 'id');
        self::assertNonEmptyString($data['name'], 'name');
        self::assertString($data['site_url'], 'site_url');
        self::assertString($data['logo_filename'], 'logo_filename');
        self::assertInt($data['ordering'], 'ordering');
        self::assertString($data['source_id'], 'source_id');

        return new Producer(
            $data['id'],
            $data['name'],
            $data['site_url'],
            $data['logo_filename'],
            $data['ordering'],
            $data['source_id']
        );
    }

    private static function assertInt(mixed $value, string $field): void
    {
        if (!is_int($value)) {
            throw new UnexpectedApiResponseException("Invalid producer {$field}");
        }
    }

    private static function assertString(mixed $value, string $field): void
    {
        if (!is_string($value)) {
            throw new UnexpectedApiResponseException("Invalid producer {$field}");
        }
    }

    private static function assertNonEmptyString(mixed $value, string $field): void
    {
        if (!is_string($value) || $value === '') {
            throw new UnexpectedApiResponseException("Invalid producer {$field}");
        }
    }
}