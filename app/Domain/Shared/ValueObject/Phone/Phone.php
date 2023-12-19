<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObject\Phone;

use App\Domain\Shared\ValueObject\Phone\Exceptions\InvalidPhoneException;

readonly class Phone
{
    public const MAX_LENGTH = 15;

    private string $phone;

    /**
     * @throws InvalidPhoneException
     */
    public function __construct(
        string $phone
    ) {
        $phone = str_replace(' ', '', $phone);
        self::validate($phone);
        $this->phone = $phone;
    }

    public function __toString(): string
    {
        return $this->phone;
    }

    public function toString(): string
    {
        return $this->phone;
    }

    /**
     * @throws InvalidPhoneException
     */
    private static function validate(string $phone): void
    {
        if ('' === $phone) {
            throw new InvalidPhoneException('empty string provided');
        }
        if (mb_strlen($phone) > self::MAX_LENGTH) {
            throw new InvalidPhoneException('phone length is greater than maximum available');
        }
        // TODO: we can also write validations for country codes and so on
    }
}
