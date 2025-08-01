<?php

namespace App\DTOs;

class PriceResult
{
    public function __construct(
        public readonly string $serviceName,
        public readonly float $total,
        public readonly array $breakdown,
        public readonly array $metadata = []
    ) {}

    public function toArray(): array
    {
        return [
            'service' => $this->serviceName,
            'cost' => $this->total,
            'details' => $this->breakdown,
            'metadata' => $this->metadata
        ];
    }

    public static function empty(string $serviceName): self
    {
        return new self($serviceName, 0.0, []);
    }
}