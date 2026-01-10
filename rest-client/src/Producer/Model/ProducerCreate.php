<?php

namespace Paulnovikov\RestClient\Producer\Model;

final readonly class ProducerCreate
{
    public function __construct(
        private int $id,
        private string $name,
        private string $siteUrl,
        private string $logoFilename,
        private int $ordering,
        private string $sourceId
    ) {}

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