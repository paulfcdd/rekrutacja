<?php

declare(strict_types=1);

namespace Paulnovikov\RestClient\Mapper;

use Paulnovikov\RestClient\Exception\UnexpectedApiResponseException;
use Paulnovikov\RestClient\Model\ModelInterface;

abstract class AbstractMapper
{
    public abstract function transformFromApiResponse(array $data): ModelInterface;

    protected static function assertKeyExists(array $data, string $field): void
    {
        if (!array_key_exists($field, $data)) {
            throw new UnexpectedApiResponseException("Missing field {$field}");
        }
    }

    protected static function assertInt(mixed $value, string $field): void
    {
        if (!is_int($value)) {
            throw new UnexpectedApiResponseException("Invalid field {$field}");
        }
    }

    protected static function assertString(mixed $value, string $field): void
    {
        if (!is_string($value)) {
            throw new UnexpectedApiResponseException("Invalid field {$field}");
        }
    }

    protected static function assertNonEmptyString(mixed $value, string $field): void
    {
        if (!is_string($value) || $value === '') {
            throw new UnexpectedApiResponseException("Invalid field {$field}");
        }
    }
}
