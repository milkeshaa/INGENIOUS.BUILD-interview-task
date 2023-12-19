<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Shared\ValueObject\Price;

use App\Domain\Shared\ValueObject\Price\Exceptions\InvalidPriceException;
use App\Domain\Shared\ValueObject\Price\Price;
use App\Domain\Shared\ValueObject\Quantity\Exceptions\InvalidQuantityException;
use App\Domain\Shared\ValueObject\Quantity\Quantity;
use PHPUnit\Framework\TestCase;

/** @coversDefaultClass \App\Domain\Shared\ValueObject\Price\Price */
class PriceValueObjectTest extends TestCase
{
    /**
     * @return void
     * @dataProvider data
     */
    public function test_can_create_price_object(int $price, array $result)
    {
        if (!$result['is_valid']) {
            self::expectException(InvalidPriceException::class);
            self::expectExceptionMessage($result['message']);
        }
        $priceObject = new Price(price: $price);
        if ($result['is_valid']) {
            self::assertEquals((string)$price, (string)$priceObject);
            self::assertEquals((string)$price, $priceObject->toString());
        }
    }

    /**
     * @throws InvalidPriceException
     */
    public function test_price_can_be_summed()
    {
        $price = new Price(price: 1000);
        $sum = $price->sum(price: new Price(price: 235));

        self::assertInstanceOf(Price::class, $sum);
        self::assertEquals(1235, $sum->getPrice());
    }

    /**
     * @throws InvalidPriceException
     * @throws InvalidQuantityException
     */
    public function test_price_can_be_multiplied()
    {
        $price = new Price(price: 1000);
        $result = $price->times(quantity: new Quantity(quantity: 12));

        self::assertInstanceOf(Price::class, $result);
        self::assertEquals(12000, $result->getPrice());
    }

    public function data(): array
    {
        return [
            'valid price' => [
                // price
                10000,
                // result
                [
                    'is_valid' => true,
                    'message' => ''
                ],
            ],
            'invalid price' => [
                // price
                -10,
                // result
                [
                    'is_valid' => false,
                    'message' => 'negative price is not allowed'
                ],
            ]
        ];
    }
}
