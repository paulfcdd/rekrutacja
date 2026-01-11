<?php

namespace Paulnovikov\RestClient\Model\Producer;

use Paulnovikov\RestClient\Model\ModelInterface;

final readonly class Producer implements ModelInterface
{
    public function __construct(
        private int $id,
        private string $name,
        private string $siteUrl,
        private string $logoFilename,
        private int $ordering,
        private string $sourceId
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSiteUrl(): string
    {
        return $this->siteUrl;
    }

    public function getLogoFilename(): string
    {
        return $this->logoFilename;
    }

    public function getOrdering(): int
    {
        return $this->ordering;
    }

    public function getSourceId(): string
    {
        return $this->sourceId;
    }
}
