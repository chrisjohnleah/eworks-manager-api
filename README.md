# eWorks Manager API

Framework-agnostic PHP SDK for the eWorks Manager API, built on Saloon 4.

The public API contract uses an `Authorization: Apikey {username}:{api key}` header, a current Central-time `Timestamp` header, JSON content negotiation, and the `https://api.eworkorders.com/v1` base URL.

```php
use ChrisJohnLeah\EworksManager\EworksClient;

$client = EworksClient::fromArray([
    'username' => $username,
    'api_key' => $apiKey,
]);

$customers = $client->get('customers');
foreach ($customers->records() as $customer) {
    // Provider data only: map it in the consuming application.
}

$workorders = $client->workorders();
$notes = $client->workorderLogs('WO-100');
$purchaseOrders = $client->purchaseOrders();
$purchaseOrderLines = $client->purchaseOrderLineItems('PO-100');
```

The SDK owns provider transport, headers, response normalization, and transient retries. It does not decide tenant plans, migrations, local models, or archive retention.

## Testing

Run the package contract tests with `composer install` followed by
`vendor/bin/pest`.
