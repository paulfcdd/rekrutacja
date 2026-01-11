<?php

declare(strict_types=1);

namespace Paulnovikov\RestClient\Tests;

use Paulnovikov\RestClient\Model\Producer\ProducerCreate;
use PHPUnit\Framework\TestCase;

final class ProducerCreateTest extends TestCase
{
    public function testFromArrayRequiresAllFields(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        ProducerCreate::fromArray([
            'id' => 1,
            'name' => 'Acme',
        ]);
    }

    public function testFromArrayTrimsAndCasts(): void
    {
        $producer = ProducerCreate::fromArray([
            'id' => '10',
            'name' => ' Acme ',
            'site_url' => ' https://example.com ',
            'logo_filename' => ' logo.png ',
            'ordering' => '2',
            'source_id' => ' src-10 ',
        ]);

        $payload = $producer->toPayload();

        $this->assertSame(10, $payload['producer']['id']);
        $this->assertSame('Acme', $payload['producer']['name']);
        $this->assertSame('https://example.com', $payload['producer']['site_url']);
        $this->assertSame('logo.png', $payload['producer']['logo_filename']);
        $this->assertSame(2, $payload['producer']['ordering']);
        $this->assertSame('src-10', $payload['producer']['source_id']);
    }
}
