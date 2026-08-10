<?php

declare(strict_types=1);

namespace ChrisJohnLeah\EworksManager\Requests;

use ChrisJohnLeah\EworksManager\Data\EworksCredentials;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;
use Saloon\Http\Request;
use Saloon\Traits\Plugins\AcceptsJson;
use Saloon\Traits\RequestProperties\HasHeaders;

abstract class EworksRequest extends Request
{
    use AcceptsJson;
    use HasHeaders;

    public ?int $tries = 3;

    public ?int $retryInterval = 1;

    public ?bool $throwOnMaxTries = false;

    public ?bool $useExponentialBackoff = true;

    public function __construct(protected readonly EworksCredentials $credentials) {}

    protected function defaultHeaders(): array
    {
        $timestamp = new \DateTimeImmutable('now', new \DateTimeZone($this->credentials->timestampTimezone));

        return [
            'Authorization' => 'Apikey '.$this->credentials->username.':'.$this->credentials->apiKey,
            'Timestamp' => $timestamp->format(\DateTimeInterface::ATOM),
            'Content-Type' => 'application/json',
        ];
    }

    public function handleRetry(FatalRequestException|RequestException $exception, Request $request): bool
    {
        $status = $exception instanceof RequestException ? $exception->getResponse()?->status() : null;

        return $status === 429 || ($status !== null && $status >= 500);
    }
}
