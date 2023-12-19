<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Invoices\Presentation\Controllers\Http;

use App\Domain\Company\Entity as Company;
use App\Domain\Enums\StatusEnum;
use App\Domain\Invoice\Entity as Invoice;
use App\Domain\Invoice\InvoiceAggregate;
use App\Domain\Product\Entity as Product;
use App\Domain\Product\ProductLineEntity;
use App\Domain\Shared\ValueObject\Currency\Currency;
use App\Domain\Shared\ValueObject\Email\Email;
use App\Domain\Shared\ValueObject\Email\Exceptions\InvalidEmailException;
use App\Domain\Shared\ValueObject\Phone\Exceptions\InvalidPhoneException;
use App\Domain\Shared\ValueObject\Phone\Phone;
use App\Domain\Shared\ValueObject\Price\Exceptions\InvalidPriceException;
use App\Domain\Shared\ValueObject\Price\Price;
use App\Domain\Shared\ValueObject\Quantity\Exceptions\InvalidQuantityException;
use App\Domain\Shared\ValueObject\Quantity\Quantity;
use App\Modules\Invoices\Presentation\Controllers\Http\RejectInvoice;
use Faker\Factory;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;
use Ramsey\Uuid\Uuid;

/** @coversDefaultClass \App\Modules\Invoices\Presentation\Controllers\Http\RejectInvoice */
class RejectInvoiceControllerTest extends TestCase
{
    use DatabaseTransactions;

    private Invoice $invoice;

    /**
     * @throws InvalidPriceException
     * @throws InvalidPhoneException
     * @throws InvalidEmailException
     * @throws InvalidQuantityException
     */
    public function setUp(): void
    {
        parent::setUp();

        $faker = Factory::create();
        $productRazer = new Product(
            id: Uuid::uuid4(),
            name: 'razer',
            price: new Price(price: 1000),
            currency: new Currency(currency: 'usd'),
            createdAt: null,
            updatedAt: null,
        );
        $productPepsi = new Product(
            id: Uuid::uuid4(),
            name: 'pepsi',
            price: new Price(price: 1200),
            currency: new Currency(currency: 'usd'),
            createdAt: null,
            updatedAt: null,
        );
        $pl1 = new ProductLineEntity(
            id: Uuid::uuid4(),
            invoice: null,
            product: $productRazer,
            quantity: new Quantity(quantity: 12),
            createdAt: null,
            updatedAt: null,
        );
        $pl2 = new ProductLineEntity(
            id: Uuid::uuid4(),
            invoice: null,
            product: $productPepsi,
            quantity: new Quantity(quantity: 5),
            createdAt: null,
            updatedAt: null,
        );
        $invoiceAggregate = new InvoiceAggregate(
            id: Uuid::uuid4(),
            number: Uuid::uuid4(),
            date: Carbon::now(),
            dueDate: Carbon::now(),
            company: new Company(
                id: Uuid::uuid4(),
                name: $faker->company(),
                street: $faker->streetAddress(),
                city: $faker->city(),
                zip: $faker->postcode(),
                phone: new Phone(phone: $faker->phoneNumber()),
                email: new Email(email: $faker->email()),
                createdAt: null,
                updatedAt: null,
            ),
            productLines: Collection::make([
                $pl1,
                $pl2,
            ]),
            status: StatusEnum::DRAFT,
            createdAt: Carbon::now(),
            updatedAt: Carbon::now()
        );
        $this->invoice = new Invoice(
            id: $invoiceAggregate->getId(),
            number: $invoiceAggregate->getNumber(),
            date: $invoiceAggregate->getDate(),
            dueDate: $invoiceAggregate->getDueDate(),
            companyId: $invoiceAggregate->getCompany()->getId(),
            status: $invoiceAggregate->getStatus(),
            createdAt: $invoiceAggregate->getCreatedAt(),
            updatedAt: $invoiceAggregate->getUpdatedAt(),
        );
        DB::table('companies')->insert([
            'id' => $invoiceAggregate->getCompany()->getId(),
            'name' => $invoiceAggregate->getCompany()->getName(),
            'street' => $invoiceAggregate->getCompany()->getStreet(),
            'city' => $invoiceAggregate->getCompany()->getCity(),
            'zip' => $invoiceAggregate->getCompany()->getZip(),
            'phone' => $invoiceAggregate->getCompany()->getPhone()->toString(),
            'email' => $invoiceAggregate->getCompany()->getEmail()->toString(),
            'created_at' => $invoiceAggregate->getCompany()->getCreatedAt(),
            'updated_at' => $invoiceAggregate->getCompany()->getUpdatedAt(),
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

    /**
     * @throws BindingResolutionException
     */
    public function test_invoke()
    {
        Event::fake();
        $controller = app()->make(RejectInvoice::class);

        // real invoice in draft status
        $request = Request::create("/api/invoices/{$this->invoice->getId()->toString()}/reject");
        $request->setRouteResolver(function () use ($request) {
            return (new Route('PATCH', '/api/invoices/{invoice_id}/reject', []))->bind($request);
        });
        $response = $controller($request);
        self::assertInstanceOf(JsonResponse::class, $response);
        self::assertEquals(204, $response->getStatusCode());

        // fake uuid
        $uuid = Uuid::uuid4();
        $request = Request::create("/api/invoices/{$uuid}/reject");
        $request->setRouteResolver(function () use ($request) {
            return (new Route('PATCH', '/api/invoices/{invoice_id}/reject', []))->bind($request);
        });
        self::expectException(ValidationException::class);
        self::expectExceptionMessage('The given data was invalid.');
        $controller($request);

        // invoice is no longer in draft
        $request = Request::create("/api/invoices/{$this->invoice->getId()->toString()}/reject");
        $request->setRouteResolver(function () use ($request) {
            return (new Route('PATCH', '/api/invoices/{invoice_id}/reject', []))->bind($request);
        });
        self::expectException(ValidationException::class);
        self::expectExceptionMessage('The given data was invalid.');
        $controller($request);
    }
}
