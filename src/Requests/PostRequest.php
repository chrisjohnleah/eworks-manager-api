<?php

declare(strict_types=1);

namespace ChrisJohnLeah\EworksManager\Requests;

use ChrisJohnLeah\EworksManager\Data\EworksCredentials;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Traits\Body\HasJsonBody;

final class PostRequest extends EworksRequest implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    /**
     * @param  array<string, mixed>  $payload
     */
    public function __construct(
        string $path,
        array $payload,
        EworksCredentials $credentials,
    ) {
        parent::__construct($credentials);
        $this->path = $path;
        $this->payload = $payload;
    }

    private readonly string $path;

    /** @var array<string, mixed> */
    private readonly array $payload;

    public function resolveEndpoint(): string
    {
        return ltrim($this->path, '/');
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        return $this->payload;
    }
}
