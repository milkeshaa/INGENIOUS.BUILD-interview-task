<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Shared\ValueObject\Quantity;

use App\Domain\Shared\ValueObject\Quantity\Exceptions\InvalidQuantityException;
use App\Domain\Shared\ValueObject\Quantity\Quantity;
use PHPUnit\Framework\TestCase;

/** @coversDefaultClass \App\Domain\Shared\ValueObject\Quantity\Quantity */
class QuantityValueObjectTest extends TestCase
{
    /**
     * @return void
     * @dataProvider data
     */
    public function test_can_create_quantity_object(int $quantity, array $result)
    {
        if (!$result['is_valid']) {
            self::expectException(InvalidQuantityException::class);
            self::expectExceptionMessage($result['message']);
        }
        $quantityObject = new Quantity(quantity: $quantity);
        if ($result['is_valid']) {
            self::assertEquals((string)$quantity, (string)$quantityObject);
            self::assertEquals((string)$quantity, $quantityObject->toString());
            self::assertEquals($quantity, $quantityObject->getNumber());
        }
    }

    public function data(): array
    {
        return [
            'valid quantity' => [
                // quantity
                15,
                // result
                [
                    'is_valid' => true,
                    'message' => ''
                ],
            ],
            'invalid quantity (less than min)' => [
                // quantity
                0,
                // result
                [
                    'is_valid' => false,
                    'message' => 'quantity is less than minimum'
                ],
            ],
            'invalid quantity (greater than max)' => [
                // quantity
                1000,
                // result
                [
                    'is_valid' => false,
                    'message' => 'quantity is greater than maximum'
                ],
            ],
        ];
    }
}
