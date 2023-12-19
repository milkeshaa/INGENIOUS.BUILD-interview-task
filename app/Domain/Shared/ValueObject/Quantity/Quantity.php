<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObject\Quantity;

use App\Domain\Shared\ValueObject\Quantity\Exceptions\InvalidQuantityException;

readonly class Quantity
{
    public const MAX_QUANTITY = 100;
    public const MIN_QUANTITY = 1;

    private int $quantity;

    /**
     * @throws InvalidQuantityException
     */
    public function __construct(
        int $quantity
    ) {
        self::validate($quantity);
        $this->quantity = $quantity;
    }

    public function __toString(): string
    {
        return (string)$this->quantity;
    }

    public function toString(): string
    {
        return (string)$this->quantity;
    }

    public function getNumber(): int
    {
        return $this->quantity;
    }

    /**
     * @throws InvalidQuantityException
     */
    private static function validate(int $quantity): void
    {
        if ($quantity < self::MIN_QUANTITY) {
            throw new InvalidQuantityException('quantity is less than minimum');
        }
        if ($quantity > self::MAX_QUANTITY) {
            throw new InvalidQuantityException('quantity is greater than maximum');
        }
    }
}
