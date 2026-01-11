<?php

declare(strict_types=1);

namespace Paulnovikov\RestClient\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Paulnovikov\RestClient\Exception\ApiException;
use Paulnovikov\RestClient\Http\GuzzleHttpClient;
use PHPUnit\Framework\TestCase;

final class GuzzleHttpClientTest extends TestCase
{
    public function testReturnsResponse(): void
    {
        $handler = new MockHandler([
            new Response(200, [], 'ok'),
        ]);

        $client = $this->buildClient($handler);
        $http = new GuzzleHttpClient($client, 'http://example.test');

        $response = $http->request('GET', 'ping');

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('ok', (string) $response->getBody());
    }

    public function testThrowsApiExceptionWithStatusAndBody(): void
    {
        $handler = new MockHandler([
            new Response(500, [], 'fail'),
        ]);

        $client = $this->buildClient($handler);
        $http = new GuzzleHttpClient($client, 'http://example.test');

        $this->expectException(ApiException::class);

        try {
            $http->request('GET', 'ping');
        } catch (ApiException $e) {
            $this->assertSame('http://example.test/ping', $e->getUrl());
            $this->assertSame('GET', $e->getMethod());
            $this->assertSame(500, $e->getStatusCode());
            $this->assertSame('fail', $e->getResponseBody());
            throw $e;
        }
    }

    public function testThrowsApiExceptionOnRequestException(): void
    {
        $request = new Request('GET', 'http://example.test/ping');
        $handler = new MockHandler([
            new RequestException('boom', $request),
        ]);

        $client = $this->buildClient($handler);
        $http = new GuzzleHttpClient($client, 'http://example.test');

        $this->expectException(ApiException::class);

        try {
            $http->request('GET', 'ping');
        } catch (ApiException $e) {
            $this->assertSame('http://example.test/ping', $e->getUrl());
            $this->assertSame('GET', $e->getMethod());
            $this->assertNull($e->getStatusCode());
            $this->assertSame('', $e->getResponseBody());
            throw $e;
        }
    }

    private function buildClient(MockHandler $handler): Client
    {
        $stack = HandlerStack::create($handler);

        return new Client(['handler' => $stack]);
    }
}
