<?php

declare(strict_types=1);

namespace App\Domain\Product;

use App\Domain\Shared\ValueObject\Currency\Currency;
use App\Domain\Shared\ValueObject\Price\Price;
use Illuminate\Support\Carbon;
use Ramsey\Uuid\UuidInterface;

readonly class Entity
{
    public function __construct(
        private UuidInterface $id,
        private string $name,
        private Price $price,
        private Currency $currency,
        private ?Carbon $createdAt,
        private ?Carbon $updatedAt,
    ) {}

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): Price
    {
        return $this->price;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function getCreatedAt(): ?Carbon
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?Carbon
    {
        return $this->updatedAt;
    }
}
