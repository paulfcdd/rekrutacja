<?php

declare(strict_types=1);

namespace Paulnovikov\RestClient\Tests;

use GuzzleHttp\Psr7\Response;
use Paulnovikov\RestClient\API\ProducerApi;
use Paulnovikov\RestClient\Exception\ApiException;
use Paulnovikov\RestClient\Exception\UnexpectedApiResponseException;
use Paulnovikov\RestClient\Tests\Support\RecordingHttpClient;
use Paulnovikov\RestClient\Tests\Support\TestLogger;
use PHPUnit\Framework\TestCase;

final class ProducerApiTest extends TestCase
{
    public function testGetAllSkipsInvalidRecordsAndLogs(): void
    {
        $payload = [
            [
                'id' => 1,
                'name' => 'Acme',
                'site_url' => 'https://example.com',
                'logo_filename' => 'logo.png',
                'ordering' => 10,
                'source_id' => 'src-1',
            ],
            [
                'id' => 2,
                'name' => 'Broken',
            ],
        ];

        $response = new Response(200, [], json_encode($payload, JSON_THROW_ON_ERROR));
        $client = new RecordingHttpClient($response);
        $logger = new TestLogger();
        $api = new ProducerApi($client, $logger);

        $result = $api->getAll();

        $this->assertCount(1, $result);
        $this->assertCount(1, $logger->records);
        $this->assertSame('warning', $logger->records[0]['level']);
        $this->assertSame('Invalid producer record', $logger->records[0]['message']);
        $this->assertArrayHasKey('error', $logger->records[0]['context']);
    }

    public function testGetAllThrowsOnInvalidTopLevelResponse(): void
    {
        $response = new Response(200, [], json_encode('nope', JSON_THROW_ON_ERROR));
        $client = new RecordingHttpClient($response);
        $api = new ProducerApi($client, new TestLogger());

        $this->expectException(UnexpectedApiResponseException::class);

        $api->getAll();
    }

    public function testCreateAcceptsArrayPayload(): void
    {
        $responseData = [
            'id' => 10,
            'name' => 'Acme',
            'site_url' => 'https://example.com',
            'logo_filename' => 'logo.png',
            'ordering' => 1,
            'source_id' => 'src-10',
        ];

        $response = new Response(201, [], json_encode($responseData, JSON_THROW_ON_ERROR));
        $client = new RecordingHttpClient($response);
        $api = new ProducerApi($client, new TestLogger());

        $api->create([
            'id' => 10,
            'name' => 'Acme',
            'site_url' => 'https://example.com',
            'logo_filename' => 'logo.png',
            'ordering' => 1,
            'source_id' => 'src-10',
        ]);

        $this->assertCount(1, $client->requests);
        $body = json_decode($client->requests[0]['body'], true, 512, JSON_THROW_ON_ERROR);
        $this->assertEquals(['producer' => $responseData], $body);
    }

    public function testCreateThrowsOnMissingField(): void
    {
        $response = new Response(201, [], json_encode([], JSON_THROW_ON_ERROR));
        $client = new RecordingHttpClient($response);
        $api = new ProducerApi($client, new TestLogger());

        $this->expectException(\InvalidArgumentException::class);

        $api->create([
            'id' => 1,
            'name' => 'Acme',
            'site_url' => 'https://example.com',
            'logo_filename' => 'logo.png',
            'ordering' => 1,
        ]);
    }

    public function testCreateThrowsOnUnexpectedStatus(): void
    {
        $response = new Response(500, [], json_encode(['error' => 'fail'], JSON_THROW_ON_ERROR));
        $client = new RecordingHttpClient($response);
        $api = new ProducerApi($client, new TestLogger());

        $this->expectException(ApiException::class);

        try {
            $api->create([
                'id' => 1,
                'name' => 'Acme',
                'site_url' => 'https://example.com',
                'logo_filename' => 'logo.png',
                'ordering' => 1,
                'source_id' => 'src-1',
            ]);
        } catch (ApiException $e) {
            $this->assertSame('http://example.test/shop_api/v1/producers', $e->getUrl());
            $this->assertSame('POST', $e->getMethod());
            throw $e;
        }
    }
}
