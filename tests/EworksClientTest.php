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

it('posts a documented work order with the provider headers and JSON body', function () {
    $mock = new MockClient([
        MockResponse::make([
            'status' => 201,
            'status_message' => 'Work Order created successfully.',
        ], 201),
    ]);

    $payload = [
        'title' => 'Automatic door inspection',
        'type_num' => 2,
        'type_desc' => 'Preventive Maintenance',
        'type_short' => 'PM',
        'location1' => 'Manchester depot',
        'procedure' => 'Inspect and report defects.',
    ];

    $response = (new EworksClient(EworksCredentials::fromArray([
        'username' => 'user',
        'api_key' => 'key',
    ])))->withMockClient($mock)->postWorkorder($payload);

    $request = $mock->getLastPendingRequest();

    expect($response->status)->toBe(201)
        ->and($response->json('status_message'))->toBe('Work Order created successfully.')
        ->and($request?->getUrl())->toBe('https://api.eworkorders.com/v1/workorders')
        ->and($request?->getRequest()->body()->all())->toBe($payload)
        ->and($request?->headers()->get('Authorization'))->toBe('Apikey user:key')
        ->and($request?->headers()->get('Content-Type'))->toBe('application/json')
        ->and($request?->headers()->get('Timestamp'))->not->toBeNull();
});

it('exposes the documented historical collection and detail helpers', function () {
    $helpers = [
        ['meters', [], 'https://api.eworkorders.com/v1/meters'],
        ['meterReadings', [['from' => '2024-01-01']], 'https://api.eworkorders.com/v1/meters/readings?from=2024-01-01'],
        ['assetFields', [], 'https://api.eworkorders.com/v1/assets/fields'],
        ['assetFieldValues', ['ASSET/1'], 'https://api.eworkorders.com/v1/assets/fields/values/ASSET%2F1'],
        ['stockrooms', [], 'https://api.eworkorders.com/v1/stockrooms'],
        ['transactions', [], 'https://api.eworkorders.com/v1/transactions'],
        ['spotBuys', [], 'https://api.eworkorders.com/v1/spotbuys'],
        ['purchaseReturns', [], 'https://api.eworkorders.com/v1/purchasereturns'],
        ['purchaseReturn', ['PR/1'], 'https://api.eworkorders.com/v1/purchasereturns/PR%2F1'],
        ['purchaseReturnLineItems', ['PR/1'], 'https://api.eworkorders.com/v1/purchasereturns/PR%2F1/lineitems'],
        ['inventoryOrders', [], 'https://api.eworkorders.com/v1/inventoryorders'],
        ['inventoryOrder', ['IO/1'], 'https://api.eworkorders.com/v1/inventoryorders/IO%2F1'],
        ['inventoryOrderLineItems', ['IO/1'], 'https://api.eworkorders.com/v1/inventoryorders/IO%2F1/lineitems'],
        ['inventoryReturns', [], 'https://api.eworkorders.com/v1/inventoryreturns'],
        ['inventoryReturn', ['IR/1'], 'https://api.eworkorders.com/v1/inventoryreturns/IR%2F1'],
        ['inventoryReturnLineItems', ['IR/1'], 'https://api.eworkorders.com/v1/inventoryreturns/IR%2F1/lineitems'],
        ['transferOrders', [], 'https://api.eworkorders.com/v1/transferorders'],
        ['transferOrder', ['TO/1'], 'https://api.eworkorders.com/v1/transferorders/TO%2F1'],
        ['transferOrderLineItems', ['TO/1'], 'https://api.eworkorders.com/v1/transferorders/TO%2F1/lineitems'],
        ['vendors', [], 'https://api.eworkorders.com/v1/vendors'],
    ];

    foreach ($helpers as [$method, $arguments, $expectedUrl]) {
        $mock = new MockClient([MockResponse::make([])]);
        $client = (new EworksClient(EworksCredentials::fromArray([
            'username' => 'user',
            'api_key' => 'key',
        ])))->withMockClient($mock);

        $client->{$method}(...$arguments);

        expect((string) $mock->getLastPendingRequest()?->getUri())->toBe($expectedUrl);
    }
});
