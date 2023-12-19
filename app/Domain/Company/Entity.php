<?php

declare(strict_types=1);

namespace App\Domain\Company;

use App\Domain\Shared\ValueObject\Email\Email;
use App\Domain\Shared\ValueObject\Phone\Phone;
use Illuminate\Support\Carbon;
use Ramsey\Uuid\UuidInterface;

readonly class Entity
{
    public function __construct(
        private UuidInterface $id,
        private string $name,
        private string $street,
        private string $city,
        private string $zip,
        private Phone $phone,
        private Email $email,
        private ?Carbon $createdAt,
        private ?Carbon $updatedAt,
    ) {}

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getStreet(): string
    {
        return $this->street;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getZip(): string
    {
        return $this->zip;
    }

    public function getPhone(): Phone
    {
        return $this->phone;
    }

    public function getEmail(): Email
    {
        return $this->email;
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
