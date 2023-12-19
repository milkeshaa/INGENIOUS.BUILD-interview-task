<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Invoice;

use App\Domain\Enums\StatusEnum;
use App\Domain\Invoice\Entity as Invoice;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/** @coversDefaultClass \App\Domain\Invoice\Entity */
class InvoiceEntityTest extends TestCase
{
    public function test_can_create_invoice_object()
    {
        $invoice = new Invoice(
            id: Uuid::uuid4(),
            number: Uuid::uuid4(),
            date: Carbon::now(),
            dueDate: Carbon::now(),
            companyId: Uuid::uuid4(),
            status: StatusEnum::APPROVED,
            createdAt: Carbon::now(),
            updatedAt: Carbon::now()
        );

        self::assertEquals(StatusEnum::APPROVED, $invoice->getStatus());
        self::assertNotNull($invoice->getId());
        self::assertInstanceOf(UuidInterface::class, $invoice->getId());
        self::assertNotNull($invoice->getCompanyId());
        self::assertInstanceOf(UuidInterface::class, $invoice->getCompanyId());
        self::assertNotNull($invoice->getNumber());
        self::assertInstanceOf(UuidInterface::class, $invoice->getNumber());
        self::assertNotNull($invoice->getDate());
        self::assertInstanceOf(Carbon::class, $invoice->getDate());
        self::assertNotNull($invoice->getDueDate());
        self::assertInstanceOf(Carbon::class, $invoice->getDueDate());
        self::assertNotNull($invoice->getUpdatedAt());
        self::assertInstanceOf(Carbon::class, $invoice->getUpdatedAt());
        self::assertNotNull($invoice->getCreatedAt());
        self::assertInstanceOf(Carbon::class, $invoice->getCreatedAt());
    }
}
