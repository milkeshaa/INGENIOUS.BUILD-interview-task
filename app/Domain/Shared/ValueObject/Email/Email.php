<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObject\Email;

use App\Domain\Shared\ValueObject\Email\Exceptions\InvalidEmailException;

readonly class Email
{
    private string $email;

    /**
     * @throws InvalidEmailException
     */
    public function __construct(
        string $email
    ) {
        self::validate($email);
        $this->email = $email;
    }

    public function __toString(): string
    {
        return $this->email;
    }

    public function toString(): string
    {
        return $this->email;
    }

    /**
     * @throws InvalidEmailException
     */
    private static function validate(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidEmailException('invalid email format provided');
        }
    }
}
