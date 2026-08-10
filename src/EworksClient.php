<?php

declare(strict_types=1);

namespace ChrisJohnLeah\EworksManager;

use ChrisJohnLeah\EworksManager\Data\EworksCredentials;
use ChrisJohnLeah\EworksManager\Data\EworksResponse;
use ChrisJohnLeah\EworksManager\Requests\GetRequest;
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

    public function users(array $query = []): EworksResponse
    {
        return $this->get('users', $query);
    }

    public function workorders(array $query = []): EworksResponse
    {
        return $this->get('workorders', $query);
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

    private function pathSegment(string|int $value): string
    {
        return rawurlencode((string) $value);
    }
}
