<?php

declare(strict_types=1);

namespace App\Domain\Product;

use App\Domain\Invoice\Entity as Invoice;
use App\Domain\Product\Entity as Product;
use App\Domain\Shared\ValueObject\Price\Exceptions\InvalidPriceException;
use App\Domain\Shared\ValueObject\Price\Price;
use App\Domain\Shared\ValueObject\Quantity\Quantity;
use Illuminate\Support\Carbon;
use Ramsey\Uuid\UuidInterface;

readonly class ProductLineEntity
{
    public function __construct(
        private UuidInterface $id,
        private ?Invoice $invoice,
        private ?Product $product,
        private Quantity $quantity,
        private ?Carbon $createdAt,
        private ?Carbon $updatedAt,
    ) {}

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getInvoice(): ?Invoice
    {
        return $this->invoice;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function getQuantity(): Quantity
    {
        return $this->quantity;
    }

    /**
     * @throws InvalidPriceException
     */
    public function getTotal(): ?Price
    {
        return $this->product?->getPrice()->times(quantity: $this->getQuantity());
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
