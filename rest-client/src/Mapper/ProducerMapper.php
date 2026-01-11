<?php

declare(strict_types=1);

namespace Paulnovikov\RestClient\Mapper;

use Paulnovikov\RestClient\Model\Producer\ProducerData;

class ProducerMapper extends AbstractMapper
{
    public function transformFromApiResponse(array $data): ProducerData
    {
        self::assertKeyExists($data, 'id');
        self::assertKeyExists($data, 'name');
        self::assertKeyExists($data, 'site_url');
        self::assertKeyExists($data, 'logo_filename');
        self::assertKeyExists($data, 'ordering');
        self::assertKeyExists($data, 'source_id');

        self::assertInt($data['id'], 'id');
        self::assertNonEmptyString($data['name'], 'name');
        self::assertString($data['site_url'], 'site_url');
        self::assertString($data['logo_filename'], 'logo_filename');
        self::assertInt($data['ordering'], 'ordering');
        self::assertString($data['source_id'], 'source_id');

        return new ProducerData(
            $data['id'],
            $data['name'],
            $data['site_url'],
            $data['logo_filename'],
            $data['ordering'],
            $data['source_id']
        );
    }
}
