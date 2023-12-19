<?php

declare(strict_types=1);

namespace App\Domain\Invoice;

use App\Domain\Enums\StatusEnum;
use App\Domain\Product\ProductLineEntity;
use App\Domain\Shared\ValueObject\Currency\Currency;
use App\Domain\Shared\ValueObject\Price\Exceptions\InvalidPriceException;
use App\Domain\Shared\ValueObject\Price\Price;
use Illuminate\Support\Carbon;
use App\Domain\Company\Entity as Company;
use Ramsey\Uuid\UuidInterface;
use Illuminate\Support\Collection;

readonly class Entity
{
    public function __construct(
        private UuidInterface $id,
        private UuidInterface $number,
        private Carbon $date,
        private Carbon $dueDate,
        private ?Company $company,
        private Collection $productLines,
        private StatusEnum $status,
        private ?Carbon $createdAt,
        private ?Carbon $updatedAt,
    ) {}

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getNumber(): UuidInterface
    {
        return $this->number;
    }

    public function getDate(): Carbon
    {
        return $this->date;
    }

    public function getDueDate(): Carbon
    {
        return $this->dueDate;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function getProductLines(): Collection
    {
        return $this->productLines;
    }

    public function getStatus(): StatusEnum
    {
        return $this->status;
    }

    /**
     * @throws InvalidPriceException
     */
    public function getTotal(): Price
    {
        return $this->productLines->reduce(function (Price $carry, ProductLineEntity $current) {
            return $current->getTotal()->sum($carry);
        }, new Price(price: 0));
    }

    public function getCurrency(): ?Currency
    {
        // since currency is currently the same everywhere
        /** @var ProductLineEntity $pl */
        $pl = $this->productLines->first();
        return $pl?->getProduct()->getCurrency();
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
