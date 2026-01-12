# Rest Client SDK

REST client for the recruitment API.

## Requirements

- PHP 7.4+ or 8.x

## Usage

```php
use Paulnovikov\RestClient\ApiClient;

$client = ApiClient::create(
    'http://rekrutacja.localhost:8091/shop_api/v1',
    'user',
    'password'
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

When calling `getAll()`, invalid records are skipped and logged (PSR-3), so the result
may contain fewer items than the API response.

## Tests

```bash
composer test
```
