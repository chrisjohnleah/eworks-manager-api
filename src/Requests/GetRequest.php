<?php

declare(strict_types=1);

namespace ChrisJohnLeah\EworksManager\Requests;

use ChrisJohnLeah\EworksManager\Data\EworksCredentials;
use Saloon\Enums\Method;
use Saloon\Traits\RequestProperties\HasQuery;

final class GetRequest extends EworksRequest
{
    use HasQuery;

    protected Method $method = Method::GET;

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function __construct(
        string $path,
        array $parameters,
        EworksCredentials $credentials,
    ) {
        parent::__construct($credentials);
        $this->path = $path;
        $this->parameters = $parameters;
    }

    private readonly string $path;

    /** @var array<string, mixed> */
    private readonly array $parameters;

    public function resolveEndpoint(): string
    {
        return ltrim($this->path, '/');
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultQuery(): array
    {
        return array_filter($this->parameters, static fn (mixed $value): bool => $value !== null && $value !== '');
    }
}
