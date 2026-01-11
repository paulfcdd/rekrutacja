<?php

namespace Paulnovikov\RestClient\Model\Producer;

use InvalidArgumentException;
use Paulnovikov\RestClient\Model\ModelInterface;

final readonly class ProducerCreate implements ModelInterface
{
    public function __construct(
        private int $id,
        private string $name,
        private string $siteUrl,
        private string $logoFilename,
        private int $ordering,
        private string $sourceId
    ) {}

    public static function fromArray(array $data): self
    {
        foreach (['id', 'name', 'site_url', 'logo_filename', 'ordering', 'source_id'] as $field) {
            if (!array_key_exists($field, $data)) {
                throw new InvalidArgumentException("Missing field {$field}");
            }
        }

        return new self(
            (int) $data['id'],
            trim((string) $data['name']),
            trim((string) $data['site_url']),
            trim((string) $data['logo_filename']),
            (int) $data['ordering'],
            trim((string) $data['source_id'])
        );
    }

    public function toPayload(): array
    {
        return [
            'producer' => [
                'id' => $this->id,
                'name' => $this->name,
                'ordering' => $this->ordering,
                'logo_filename' => $this->logoFilename,
                'site_url' => $this->siteUrl,
                'source_id' => $this->sourceId,
            ]
        ];
    }
}
