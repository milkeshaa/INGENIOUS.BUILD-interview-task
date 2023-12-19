<?php

declare(strict_types=1);

namespace App\Modules\Invoices\Api\Listeners;

use App\Domain\Enums\StatusEnum;
use App\Domain\Invoice\Entity as Invoice;
use App\Modules\Approval\Api\Events\EntityApproved;
use App\Modules\Invoices\Infrastructure\Repository\Dto\InvoiceUpdateDto;
use App\Modules\Invoices\Infrastructure\Repository\InvoiceRepositoryInterface;
use Carbon\Carbon;

readonly class InvoiceApprovedListener
{
    public function __construct(
        private InvoiceRepositoryInterface $invoiceRepository,
    ) {}

    public function handle(EntityApproved $event): void
    {
        /** @var Invoice $invoice */
        $invoice = unserialize($event->approvalDto->entity);
        $invoiceUpdateDto = new InvoiceUpdateDto(
            number: $invoice->getNumber()->toString(),
            date: $invoice->getDate()->toDateTimeString(),
            dueDate: $invoice->getDueDate()->toDateTimeString(),
            companyId: $invoice->getCompanyId()->toString(),
            status: StatusEnum::APPROVED->value,
            createdAt: $invoice->getCreatedAt()->toDateTimeString(),
            updatedAt: Carbon::now()->toDateTimeString(),
        );
        $this->invoiceRepository->update(invoiceId: $invoice->getId(), invoice: $invoiceUpdateDto);
    }
}
