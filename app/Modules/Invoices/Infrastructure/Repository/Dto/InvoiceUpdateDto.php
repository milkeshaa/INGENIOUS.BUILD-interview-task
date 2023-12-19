<?php

declare(strict_types=1);

namespace App\Modules\Invoices\Infrastructure\Repository\Dto;

readonly class InvoiceUpdateDto
{
    public function __construct(
        private string $number,
        private string $date,
        private string $dueDate,
        private string $companyId,
        private string $status,
        private string $createdAt,
        private string $updatedAt,
    ) {}

    public function getNumber(): string
    {
        return $this->number;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function getDueDate(): string
    {
        return $this->dueDate;
    }

    public function getCompanyId(): string
    {
        return $this->companyId;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }

    public function toArray(): array
    {
        return [
            'number' => $this->getNumber(),
            'date' => $this->getDate(),
            'due_date' => $this->getDueDate(),
            'company_id' => $this->getCompanyId(),
            'status' => $this->getStatus(),
            'created_at' => $this->getCreatedAt(),
            'updated_at' => $this->getUpdatedAt(),
        ];
    }
}
