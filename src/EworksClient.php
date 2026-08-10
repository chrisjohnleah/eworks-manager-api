<?php

declare(strict_types=1);

namespace ChrisJohnLeah\EworksManager;

use ChrisJohnLeah\EworksManager\Data\EworksCredentials;
use ChrisJohnLeah\EworksManager\Data\EworksResponse;
use ChrisJohnLeah\EworksManager\Requests\GetRequest;
use ChrisJohnLeah\EworksManager\Requests\PostRequest;
use Saloon\Contracts\Authenticator;
use Saloon\Http\Connector;
use Saloon\Http\Faking\MockClient;

final class EworksClient extends Connector
{
    public function __construct(public readonly EworksCredentials $credentials) {}

    public static function fromArray(array $values, ?MockClient $mockClient = null): self
    {
        $client = new self(EworksCredentials::fromArray($values));

        return $mockClient === null ? $client : $client->withMockClient($mockClient);
    }

    public function resolveBaseUrl(): string
    {
        return $this->credentials->apiBaseUrl;
    }

    protected function defaultAuth(): ?Authenticator
    {
        return null;
    }

    /**
     * eWorks documents GET collection/detail endpoints. Keeping the request
     * generic means new provider resources do not require a DoorOps release.
     *
     * @param  array<string, mixed>  $query
     */
    public function get(string $path, array $query = []): EworksResponse
    {
        return EworksResponse::fromSaloon($this->send(new GetRequest($path, $query, $this->credentials)));
    }

    /**
     * Execute a provider POST request and return the raw provider response.
     *
     * Keeping this primitive generic lets the SDK expose documented write
     * endpoints without coupling the framework-agnostic package to DoorOps
     * workflows or local models.
     *
     * @param  array<string, mixed>  $payload
     */
    public function post(string $path, array $payload = []): EworksResponse
    {
        return EworksResponse::fromSaloon($this->send(new PostRequest($path, $payload, $this->credentials)));
    }

    public function customers(array $query = []): EworksResponse
    {
        return $this->get('customers', $query);
    }

    public function customer(string|int $customerNumber): EworksResponse
    {
        return $this->get('customers/'.$this->pathSegment($customerNumber));
    }

    public function assets(array $query = []): EworksResponse
    {
        return $this->get('assets', $query);
    }

    public function assetFields(array $query = []): EworksResponse
    {
        return $this->get('assets/fields', $query);
    }

    public function assetFieldValues(string|int $assetNumber): EworksResponse
    {
        return $this->get('assets/fields/values/'.$this->pathSegment($assetNumber));
    }

    public function meters(array $query = []): EworksResponse
    {
        return $this->get('meters', $query);
    }

    public function meterReadings(array $query = []): EworksResponse
    {
        return $this->get('meters/readings', $query);
    }

    public function users(array $query = []): EworksResponse
    {
        return $this->get('users', $query);
    }

    public function workorders(array $query = []): EworksResponse
    {
        return $this->get('workorders', $query);
    }

    /**
     * Create a work order using eWorks' documented POST /workorders contract.
     * The provider requires title, type_num, type_desc, and type_short; the
     * SDK deliberately leaves validation and tenant-specific defaults to the
     * consuming application.
     *
     * @param  array<string, mixed>  $payload
     */
    public function postWorkorder(array $payload): EworksResponse
    {
        return $this->post('workorders', $payload);
    }

    public function workorderAssignments(array $query = []): EworksResponse
    {
        return $this->get('workorders/assignments', $query);
    }

    public function workorderLogs(string|int $workorderNumber): EworksResponse
    {
        return $this->get('workorders/'.$this->pathSegment($workorderNumber).'/logs');
    }

    public function workorderFieldValues(string|int $workorderNumber): EworksResponse
    {
        return $this->get('workorders/fields/values/'.$this->pathSegment($workorderNumber));
    }

    public function inventory(array $query = []): EworksResponse
    {
        return $this->get('inventory', $query);
    }

    public function stockrooms(array $query = []): EworksResponse
    {
        return $this->get('stockrooms', $query);
    }

    public function transactions(array $query = []): EworksResponse
    {
        return $this->get('transactions', $query);
    }

    public function purchaseOrders(array $query = []): EworksResponse
    {
        return $this->get('purchaseorders', $query);
    }

    public function purchaseOrderLineItems(string|int $purchaseOrderNumber): EworksResponse
    {
        return $this->get('purchaseorders/'.$this->pathSegment($purchaseOrderNumber).'/lineitems');
    }

    public function spotBuyLineItems(array $query = []): EworksResponse
    {
        return $this->get('spotbuys/lineitems', $query);
    }

    public function spotBuys(array $query = []): EworksResponse
    {
        return $this->get('spotbuys', $query);
    }

    public function purchaseReturns(array $query = []): EworksResponse
    {
        return $this->get('purchasereturns', $query);
    }

    public function purchaseReturn(string|int $returnNumber): EworksResponse
    {
        return $this->get('purchasereturns/'.$this->pathSegment($returnNumber));
    }

    public function purchaseReturnLineItems(string|int $returnNumber): EworksResponse
    {
        return $this->get('purchasereturns/'.$this->pathSegment($returnNumber).'/lineitems');
    }

    public function inventoryOrders(array $query = []): EworksResponse
    {
        return $this->get('inventoryorders', $query);
    }

    public function inventoryOrder(string|int $orderNumber): EworksResponse
    {
        return $this->get('inventoryorders/'.$this->pathSegment($orderNumber));
    }

    public function inventoryOrderLineItems(string|int $orderNumber): EworksResponse
    {
        return $this->get('inventoryorders/'.$this->pathSegment($orderNumber).'/lineitems');
    }

    public function inventoryReturns(array $query = []): EworksResponse
    {
        return $this->get('inventoryreturns', $query);
    }

    public function inventoryReturn(string|int $returnNumber): EworksResponse
    {
        return $this->get('inventoryreturns/'.$this->pathSegment($returnNumber));
    }

    public function inventoryReturnLineItems(string|int $returnNumber): EworksResponse
    {
        return $this->get('inventoryreturns/'.$this->pathSegment($returnNumber).'/lineitems');
    }

    public function transferOrders(array $query = []): EworksResponse
    {
        return $this->get('transferorders', $query);
    }

    public function transferOrder(string|int $orderNumber): EworksResponse
    {
        return $this->get('transferorders/'.$this->pathSegment($orderNumber));
    }

    public function transferOrderLineItems(string|int $orderNumber): EworksResponse
    {
        return $this->get('transferorders/'.$this->pathSegment($orderNumber).'/lineitems');
    }

    public function countingOrders(array $query = []): EworksResponse
    {
        return $this->get('countingorders', $query);
    }

    public function countingOrderLineItems(string|int $countingOrderNumber): EworksResponse
    {
        return $this->get('countingorders/'.$this->pathSegment($countingOrderNumber).'/lineitems');
    }

    /**
     * @param  array<string, mixed>  $query
     */
    public function laborMaterialsByAsset(array $query = []): EworksResponse
    {
        return $this->get('reports/LaborMaterialsByAsset', $query);
    }

    public function vendors(array $query = []): EworksResponse
    {
        return $this->get('vendors', $query);
    }

    private function pathSegment(string|int $value): string
    {
        return rawurlencode((string) $value);
    }
}
