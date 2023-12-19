<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObject\Currency;

use App\Domain\Shared\ValueObject\Currency\Exceptions\InvalidCurrencyException;

readonly class Currency
{
    public const CURRENCIES = ['usd'];

    private string $currency;

    /**
     * @throws InvalidCurrencyException
     */
    public function __construct(
        string $currency
    ) {
        self::validate($currency);
        $this->currency = $currency;
    }

    public function __toString(): string
    {
        return $this->currency;
    }

    public function toString(): string
    {
        return $this->currency;
    }

    /**
     * @throws InvalidCurrencyException
     */
    private static function validate(string $currency): void
    {
        if (!in_array($currency, self::CURRENCIES)) {
            throw new InvalidCurrencyException('invalid currency provided');
        }
    }
}
