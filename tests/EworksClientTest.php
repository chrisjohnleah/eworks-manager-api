<?php

use ChrisJohnLeah\EworksManager\Data\EworksCredentials;
use ChrisJohnLeah\EworksManager\EworksClient;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

it('sends the documented eWorks API key header and normalizes records', function () {
    $mock = new MockClient([
        MockResponse::make(['data' => [['customer_num' => 'C-100']]]),
    ]);

    $response = (new EworksClient(EworksCredentials::fromArray([
        'username' => 'user',
        'api_key' => 'key',
    ])))->withMockClient($mock)->customers();

    $request = $mock->getLastPendingRequest();

    expect($response->records())->toBe([['customer_num' => 'C-100']])
        ->and($request?->headers()->get('Authorization'))->toBe('Apikey user:key')
        ->and($request?->headers()->get('Content-Type'))->toBe('application/json')
        ->and($request?->headers()->get('Timestamp'))->not->toBeNull();
});

it('encodes detail resource identifiers safely', function () {
    $mock = new MockClient([MockResponse::make([])]);

    (new EworksClient(EworksCredentials::fromArray([
        'username' => 'user',
        'api_key' => 'key',
    ])))->withMockClient($mock)->workorderLogs('WO/100');

    expect((string) $mock->getLastPendingRequest()?->getUrl())
        ->toBe('https://api.eworkorders.com/v1/workorders/WO%2F100/logs');
});
