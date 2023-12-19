<?php

declare(strict_types=1);

namespace App\Domain\Invoice;

use App\Domain\Enums\StatusEnum;
use Illuminate\Support\Carbon;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Uuid;

readonly class Entity
{
    public function __construct(
        private UuidInterface $id,
        private UuidInterface $number,
        private Carbon $date,
        private Carbon $dueDate,
        private UuidInterface $companyId,
        private StatusEnum $status,
        private ?Carbon $createdAt,
        private ?Carbon $updatedAt,
    ) {}

    public function __toString(): string
    {
        return serialize($this);
    }

    public function __serialize(): array
    {
        return [
            'id' => $this->getId()->toString(),
            'number' => $this->getNumber()->toString(),
            'date' => $this->getDate()->toString(),
            'due_date' => $this->getDueDate()->toString(),
            'company_id' => $this->getCompanyId()->toString(),
            'status' => $this->getStatus()->value,
            'created_at' => $this->getCreatedAt()->toString(),
            'updated_at' => $this->getUpdatedAt()->toString(),
        ];
    }

    public function __unserialize(array $data): void
    {
        $this->id = Uuid::fromString($data['id']);
        $this->number = Uuid::fromString($data['number']);
        $this->date = Carbon::parse($data['date']);
        $this->dueDate = Carbon::parse($data['due_date']);
        $this->companyId = Uuid::fromString($data['company_id']);
        $this->status = StatusEnum::tryFrom($data['status']);
        $this->createdAt = Carbon::parse($data['created_at']);
        $this->updatedAt = Carbon::parse($data['updated_at']);
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getNumber(): UuidInterface
    {
        return $this->number;
    }

    public function getCompanyId(): UuidInterface
    {
        return $this->companyId;
    }

    public function getDate(): Carbon
    {
        return $this->date;
    }

    public function getDueDate(): Carbon
    {
        return $this->dueDate;
    }

    public function getStatus(): StatusEnum
    {
        return $this->status;
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
