<?php

declare(strict_types=1);

namespace App\Modules\Invoices\Application\UseCases;

use App\Domain\Invoice\Entity as Invoice;
use App\Modules\Approval\Api\Dto\ApprovalDto;
use App\Modules\Approval\Application\ApprovalFacade;
use Ramsey\Uuid\Uuid;

readonly class ApproveInvoice
{
    public function __construct(
        private ApprovalFacade $approvalFacade,
    ) {}

    public function execute(Invoice $invoice): void
    {
        $this->approvalFacade->approve(new ApprovalDto(
            id: Uuid::uuid4(),
            status: $invoice->getStatus(),
            entity: (string)$invoice,
        ));
    }
}
