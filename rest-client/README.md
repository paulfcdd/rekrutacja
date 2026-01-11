# Rest Client SDK

REST client for the recruitment API.

## Requirements

- PHP 7.4+ or 8.x

## Installation

```bash
composer require paulnovikov/rest-client
```

## Usage

```php
use Paulnovikov\RestClient\ApiClient;

$client = ApiClient::create(
    'http://rekrutacja.localhost:8091/shop_api/v1',
    'rest',
    'vKTUeyrt1!'
);

$producers = $client->producers()->getAll();

$created = $client->producers()->create([
    'id' => 1001,
    'name' => 'Acme Corp',
    'site_url' => 'https://example.com',
    'logo_filename' => 'logo.png',
    'ordering' => 22,
    'source_id' => '123',
]);
```

## Error handling

All HTTP and API errors are wrapped in `ApiException`, which includes URL, HTTP method,
status code, and response body (if available).

## Tests

```bash
composer test
```
