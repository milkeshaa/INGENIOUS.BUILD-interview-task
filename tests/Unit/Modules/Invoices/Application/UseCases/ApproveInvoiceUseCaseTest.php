<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Invoices\Application\UseCases;

use App\Domain\Enums\StatusEnum;
use App\Domain\Invoice\Entity as Invoice;
use App\Modules\Approval\Api\Events\EntityApproved;
use App\Modules\Approval\Application\ApprovalFacade;
use App\Modules\Invoices\Application\UseCases\ApproveInvoice;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use LogicException;
use Tests\TestCase;
use Ramsey\Uuid\Uuid;

/** @coversDefaultClass \App\Modules\Invoices\Application\UseCases\ApproveInvoice */
class ApproveInvoiceUseCaseTest extends TestCase
{
    private ApproveInvoice $useCase;

    public function setUp(): void
    {
        parent::setUp();

        $this->useCase = new ApproveInvoice(approvalFacade: new ApprovalFacade(dispatcher: Event::fake()));
    }

    /**
     * @dataProvider data
     */
    public function test_execute_approve_invoice_use_case(callable $createInvoice, array $result)
    {
        if (!$result['is_valid']) {
            self::expectException(LogicException::class);
            self::expectExceptionMessage($result['message']);
        }
        $this->useCase->execute(invoice: $createInvoice());
        if ($result['is_valid']) {
            Event::assertDispatched(EntityApproved::class);
        }
    }

    public function data(): array
    {
        return [
            'valid invoice' => [
                // create invoice callback
                function (): Invoice {
                    return new Invoice(
                        id: Uuid::uuid4(),
                        number: Uuid::uuid4(),
                        date: Carbon::now(),
                        dueDate: Carbon::now(),
                        companyId: Uuid::uuid4(),
                        status: StatusEnum::DRAFT,
                        createdAt: Carbon::now(),
                        updatedAt: Carbon::now()
                    );
                },
                // result
                [
                    'is_valid' => true,
                    'message' => ''
                ]
            ],
            'invalid invoice (approved)' => [
                // create invoice callback
                function (): Invoice {
                    return new Invoice(
                        id: Uuid::uuid4(),
                        number: Uuid::uuid4(),
                        date: Carbon::now(),
                        dueDate: Carbon::now(),
                        companyId: Uuid::uuid4(),
                        status: StatusEnum::APPROVED,
                        createdAt: Carbon::now(),
                        updatedAt: Carbon::now()
                    );
                },
                // result
                [
                    'is_valid' => false,
                    'message' => 'approval status is already assigned'
                ]
            ],
            'invalid invoice (rejected)' => [
                // create invoice callback
                function (): Invoice {
                    return new Invoice(
                        id: Uuid::uuid4(),
                        number: Uuid::uuid4(),
                        date: Carbon::now(),
                        dueDate: Carbon::now(),
                        companyId: Uuid::uuid4(),
                        status: StatusEnum::REJECTED,
                        createdAt: Carbon::now(),
                        updatedAt: Carbon::now()
                    );
                },
                // result
                [
                    'is_valid' => false,
                    'message' => 'approval status is already assigned'
                ]
            ],
        ];
    }
}
