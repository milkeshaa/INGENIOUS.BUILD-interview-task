<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Shared\ValueObject\Currency;

use App\Domain\Shared\ValueObject\Currency\Currency;
use App\Domain\Shared\ValueObject\Currency\Exceptions\InvalidCurrencyException;
use PHPUnit\Framework\TestCase;

/** @coversDefaultClass \App\Domain\Shared\ValueObject\Currency\Currency */
class CurrencyValueObjectTest extends TestCase
{

    /**
     * @return void
     * @dataProvider data
     */
    public function test_can_create_currency_object(string $currency, array $result)
    {
        if (!$result['is_valid']) {
            self::expectException(InvalidCurrencyException::class);
            self::expectExceptionMessage($result['message']);
        }
        $currencyObject = new Currency(currency: $currency);
        if ($result['is_valid']) {
            self::assertEquals($currency, (string)$currencyObject);
            self::assertEquals($currency, $currencyObject->toString());
        }
    }

    public function data(): array
    {
        return [
            'valid currency' => [
                // currency
                'usd',
                // result
                [
                    'is_valid' => true,
                    'message' => ''
                ],
            ],
            'invalid currency' => [
                // currency
                'eur',
                // result
                [
                    'is_valid' => false,
                    'message' => 'invalid currency provided'
                ],
            ]
        ];
    }
}
