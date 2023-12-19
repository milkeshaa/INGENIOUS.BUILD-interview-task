<?php

declare(strict_types=1);

namespace App\Modules\Invoices\Infrastructure\Repository;

use App\Domain\Invoice\InvoiceAggregate;
use App\Domain\Invoice\Entity as Invoice;
use App\Modules\Invoices\Infrastructure\Repository\Dto\InvoiceUpdateDto;
use Ramsey\Uuid\UuidInterface;

interface InvoiceRepositoryInterface
{
    public function update(UuidInterface $invoiceId, InvoiceUpdateDto $invoice): int;

    public function find(UuidInterface $invoiceId): ?Invoice;

    public function getAggregate(UuidInterface $invoiceId): ?InvoiceAggregate;
}
