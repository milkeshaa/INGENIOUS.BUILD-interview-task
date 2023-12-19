<?php

declare(strict_types=1);

namespace App\Modules\Invoices\Infrastructure\Repository;

use App\Domain\Invoice\Entity as Invoice;
use Ramsey\Uuid\UuidInterface;

interface InvoiceRepositoryInterface
{
    public function update(Invoice $invoice): int;

    public function getAggregate(UuidInterface $invoiceId): ?Invoice;
}
