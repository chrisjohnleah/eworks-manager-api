<?php

declare(strict_types=1);

namespace ChrisJohnLeah\EworksManager\Data;

use InvalidArgumentException;

final readonly class EworksCredentials
{
    public const API_BASE_URL = 'https://api.eworkorders.com/v1';

    public function __construct(
        public string $username,
        public string $apiKey,
        public string $apiBaseUrl = self::API_BASE_URL,
        public string $timestampTimezone = 'America/Chicago',
    ) {
        if ($this->username === '' || $this->apiKey === '') {
            throw new InvalidArgumentException('eWorks Manager username and API key are required.');
        }

        if ($this->apiBaseUrl === '') {
            throw new InvalidArgumentException('eWorks Manager API base URL is required.');
        }
    }

    /**
     * @param  array<string, mixed>  $values
     */
    public static function fromArray(array $values): self
    {
        return new self(
            username: trim((string) ($values['username'] ?? '')),
            apiKey: trim((string) ($values['api_key'] ?? $values['apiKey'] ?? '')),
            apiBaseUrl: rtrim((string) ($values['api_base_url'] ?? $values['apiBaseUrl'] ?? self::API_BASE_URL), '/'),
            timestampTimezone: (string) ($values['timestamp_timezone'] ?? $values['timestampTimezone'] ?? 'America/Chicago'),
        );
    }
}
