<?php

declare(strict_types=1);

namespace App\Modules\Invoices\Infrastructure\Repository;

use App\Domain\Company\Entity as Company;
use App\Domain\Enums\StatusEnum;
use App\Domain\Invoice\InvoiceAggregate;
use App\Domain\Invoice\Entity as Invoice;
use App\Domain\Product\Entity as Product;
use App\Domain\Product\ProductLineEntity;
use App\Domain\Shared\ValueObject\Currency\Currency;
use App\Domain\Shared\ValueObject\Email\Email;
use App\Domain\Shared\ValueObject\Email\Exceptions\InvalidEmailException;
use App\Domain\Shared\ValueObject\Phone\Exceptions\InvalidPhoneException;
use App\Domain\Shared\ValueObject\Phone\Phone;
use App\Domain\Shared\ValueObject\Price\Price;
use App\Domain\Shared\ValueObject\Quantity\Quantity;
use App\Modules\Invoices\Infrastructure\Repository\Dto\InvoiceUpdateDto;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class InvoiceRepository implements InvoiceRepositoryInterface
{
    private string $table = 'invoices';
    private string $primaryKey = 'id';

    public function update(UuidInterface $invoiceId, InvoiceUpdateDto $invoice): int
    {
        return DB::table($this->table)->where($this->primaryKey, $invoiceId)->update($invoice->toArray());
    }

    public function find(UuidInterface $invoiceId): ?Invoice
    {
        $invoice = DB::table($this->table)->where($this->primaryKey, $invoiceId)->first();

        if (!$invoice) {
            return null;
        }

        return new Invoice(
            id: Uuid::fromString($invoice->id),
            number: Uuid::fromString($invoice->number),
            date: Carbon::parse($invoice->date),
            dueDate: Carbon::parse($invoice->due_date),
            companyId: Uuid::fromString($invoice->company_id),
            status: StatusEnum::tryFrom($invoice->status),
            createdAt: $invoice->created_at ? Carbon::parse($invoice->created_at) : null,
            updatedAt: $invoice->updated_at ? Carbon::parse($invoice->updated_at) : null,
        );
    }

    /**
     * @throws InvalidEmailException
     * @throws InvalidPhoneException
     */
    public function getAggregate(UuidInterface $invoiceId): ?InvoiceAggregate
    {
        $invoice = DB::table($this->table)->where($this->primaryKey, $invoiceId)->first();

        if (!$invoice) {
            return null;
        }

        return new InvoiceAggregate(
            id: Uuid::fromString($invoice->id),
            number: Uuid::fromString($invoice->number),
            date: Carbon::parse($invoice->date),
            dueDate: Carbon::parse($invoice->due_date),
            company: $this->findInvoiceCompany(companyId: Uuid::fromString($invoice->company_id)),
            productLines: $this->getInvoiceProductLines(invoiceId: Uuid::fromString($invoice->id)),
            status: StatusEnum::tryFrom($invoice->status),
            createdAt: $invoice->created_at ? Carbon::parse($invoice->created_at) : null,
            updatedAt: $invoice->updated_at ? Carbon::parse($invoice->updated_at) : null,
        );
    }

    /**
     * @throws InvalidPhoneException
     * @throws InvalidEmailException
     */
    private function findInvoiceCompany(UuidInterface $companyId): ?Company
    {
        $companyDto = DB::table('companies')->where('id', $companyId)->first();
        if ($companyDto) {
            $company = new Company(
                id: Uuid::fromString($companyDto->id),
                name: $companyDto->name,
                street: $companyDto->street,
                city: $companyDto->city,
                zip: $companyDto->zip,
                phone: new Phone($companyDto->phone),
                email: new Email($companyDto->email),
                createdAt: $companyDto->created_at ? Carbon::parse($companyDto->created_at) : null,
                updatedAt: $companyDto->updated_at ? Carbon::parse($companyDto->updated_at) : null,
            );
        }

        return $company ?? null;
    }

    private function getInvoiceProductLines(UuidInterface $invoiceId): Collection
    {
        return DB::table('invoice_product_lines')->select([
            'invoice_product_lines.id',
            'invoice_product_lines.product_id',
            'products.name',
            'products.price',
            'products.currency',
            'products.created_at as product_created_at',
            'products.updated_at as product_updated_at',
            'invoice_product_lines.quantity',
            'invoice_product_lines.created_at',
            'invoice_product_lines.updated_at',
        ])
            ->join('products', 'invoice_product_lines.product_id', '=', 'products.id')
            ->where('invoice_product_lines.invoice_id', $invoiceId)
            ->get()
            ->map(function (object $dto) {
                return new ProductLineEntity(
                    id: Uuid::fromString($dto->id),
                    invoice: null,
                    product: new Product(
                        id: Uuid::fromString($dto->product_id),
                        name: $dto->name,
                        price: new Price($dto->price),
                        currency: new Currency($dto->currency),
                        createdAt: $dto->product_created_at ? Carbon::parse($dto->product_created_at) : null,
                        updatedAt: $dto->product_updated_at ? Carbon::parse($dto->product_updated_at) : null
                    ),
                    quantity: new Quantity($dto->quantity),
                    createdAt: $dto->created_at ? Carbon::parse($dto->created_at) : null,
                    updatedAt: $dto->updated_at ? Carbon::parse($dto->updated_at) : null,
                );
            })
        ;
    }
}
