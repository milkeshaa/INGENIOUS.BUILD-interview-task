<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObject\Price;

use App\Domain\Shared\ValueObject\Price\Exceptions\InvalidPriceException;
use App\Domain\Shared\ValueObject\Quantity\Quantity;

readonly class Price
{
    private float $price;

    /**
     * @throws InvalidPriceException
     */
    public function __construct(
        float $price
    ) {
        self::validate($price);
        $this->price = $price;
    }

    public function __toString(): string
    {
        return (string)$this->price;
    }

    public function toString(): string
    {
        return (string)$this->price;
    }

    /**
     * @throws InvalidPriceException
     */
    public function sum(Price $price): self
    {
        return new Price(price: $this->price + $price->price);
    }

    /**
     * @throws InvalidPriceException
     */
    public function times(Quantity $quantity): self
    {
        return new Price(price: $this->price * $quantity->getNumber());
    }

    /**
     * @throws InvalidPriceException
     */
    private static function validate(float $price): void
    {
        if ($price < 0) {
            throw new InvalidPriceException('negative price is not allowed');
        }
    }
}
