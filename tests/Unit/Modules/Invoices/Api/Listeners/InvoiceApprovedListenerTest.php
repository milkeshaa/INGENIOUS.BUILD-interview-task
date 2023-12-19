<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Invoices\Api\Listeners;

use App\Domain\Enums\StatusEnum;
use App\Domain\Invoice\Entity as Invoice;
use App\Modules\Approval\Api\Dto\ApprovalDto;
use App\Modules\Approval\Api\Events\EntityApproved;
use App\Modules\Invoices\Api\Listeners\InvoiceApprovedListener;
use App\Modules\Invoices\Infrastructure\Repository\InvoiceRepository;
use App\Modules\Invoices\Infrastructure\Repository\InvoiceRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Ramsey\Uuid\Uuid;

/** @coversDefaultClass \App\Modules\Invoices\Api\Listeners\InvoiceApprovedListener */
class InvoiceApprovedListenerTest extends TestCase
{
    use DatabaseTransactions;

    private InvoiceApprovedListener $listener;
    private Invoice $invoice;

    /**
     * @throws BindingResolutionException
     */
    public function setUp(): void
    {
        parent::setUp();

        app()->bind(InvoiceRepositoryInterface::class, InvoiceRepository::class);
        $this->listener = app()->make(InvoiceApprovedListener::class);
        $this->invoice = new Invoice(
            id: Uuid::uuid4(),
            number: Uuid::uuid4(),
            date: Carbon::now(),
            dueDate: Carbon::now(),
            companyId: Uuid::uuid4(),
            status: StatusEnum::DRAFT,
            createdAt: Carbon::now(),
            updatedAt: Carbon::now()
        );
        DB::table('companies')->insert([
            'id' => $this->invoice->getCompanyId(),
            'name' => 'fake',
            'street' => 'fake',
            'city' => 'fake',
            'zip' => 'fake',
            'phone' => '000000000',
            'email' => 'fake@fake.com',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('invoices')->insert([
            'id' => $this->invoice->getId(),
            'number' => $this->invoice->getNumber(),
            'date' => $this->invoice->getDate(),
            'due_date' => $this->invoice->getDueDate(),
            'company_id' => $this->invoice->getCompanyId(),
            'status' => $this->invoice->getStatus(),
            'created_at' => $this->invoice->getCreatedAt(),
            'updated_at' => $this->invoice->getUpdatedAt(),
        ]);
    }

    public function test_handle_invoice_approve()
    {
        $approvalDto = new ApprovalDto(
            id: Uuid::uuid4(),
            status: $this->invoice->getStatus(),
            entity: (string)$this->invoice,
        );

        $this->listener->handle(event: new EntityApproved(approvalDto: $approvalDto));

        $invoiceFromDb = DB::table('invoices')->where('id', $this->invoice->getId())->first();
        self::assertEquals(StatusEnum::APPROVED->value, $invoiceFromDb->status);
    }
}
