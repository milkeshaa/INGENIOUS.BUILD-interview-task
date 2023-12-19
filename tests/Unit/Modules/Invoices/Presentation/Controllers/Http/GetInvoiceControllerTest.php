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
use App\Modules\Invoices\Presentation\Controllers\Http\GetInvoice;
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

/** @coversDefaultClass \App\Modules\Invoices\Presentation\Controllers\Http\GetInvoice */
class GetInvoiceControllerTest extends TestCase
{
    use DatabaseTransactions;

    private Invoice $invoice;
    private InvoiceAggregate $invoiceAggregate;

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
        $this->invoiceAggregate = new InvoiceAggregate(
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
            id: $this->invoiceAggregate->getId(),
            number: $this->invoiceAggregate->getNumber(),
            date: $this->invoiceAggregate->getDate(),
            dueDate: $this->invoiceAggregate->getDueDate(),
            companyId: $this->invoiceAggregate->getCompany()->getId(),
            status: $this->invoiceAggregate->getStatus(),
            createdAt: $this->invoiceAggregate->getCreatedAt(),
            updatedAt: $this->invoiceAggregate->getUpdatedAt(),
        );
        DB::table('companies')->insert([
            'id' => $this->invoiceAggregate->getCompany()->getId(),
            'name' => $this->invoiceAggregate->getCompany()->getName(),
            'street' => $this->invoiceAggregate->getCompany()->getStreet(),
            'city' => $this->invoiceAggregate->getCompany()->getCity(),
            'zip' => $this->invoiceAggregate->getCompany()->getZip(),
            'phone' => $this->invoiceAggregate->getCompany()->getPhone()->toString(),
            'email' => $this->invoiceAggregate->getCompany()->getEmail()->toString(),
            'created_at' => $this->invoiceAggregate->getCompany()->getCreatedAt(),
            'updated_at' => $this->invoiceAggregate->getCompany()->getUpdatedAt(),
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
        DB::table('products')->insert([
            'id' => $productPepsi->getId(),
            'name' => $productPepsi->getName(),
            'price' => $productPepsi->getPrice()->getPrice(),
            'currency' => $productPepsi->getCurrency()->toString(),
            'created_at' => $productPepsi->getCreatedAt(),
            'updated_at' => $productPepsi->getUpdatedAt(),
        ]);
        DB::table('products')->insert([
            'id' => $productRazer->getId(),
            'name' => $productRazer->getName(),
            'price' => $productRazer->getPrice()->getPrice(),
            'currency' => $productRazer->getCurrency()->toString(),
            'created_at' => $productRazer->getCreatedAt(),
            'updated_at' => $productRazer->getUpdatedAt(),
        ]);
        DB::table('invoice_product_lines')->insert([
            'id' => $pl1->getId(),
            'invoice_id' => $this->invoice->getId(),
            'product_id' => $pl1->getProduct()->getId(),
            'quantity' => $pl1->getQuantity()->getNumber(),
            'created_at' => $pl1->getCreatedAt(),
            'updated_at' => $pl1->getUpdatedAt(),
        ]);
        DB::table('invoice_product_lines')->insert([
            'id' => $pl2->getId(),
            'invoice_id' => $this->invoice->getId(),
            'product_id' => $pl2->getProduct()->getId(),
            'quantity' => $pl2->getQuantity()->getNumber(),
            'created_at' => $pl2->getCreatedAt(),
            'updated_at' => $pl2->getUpdatedAt(),
        ]);
    }

    /**
     * @throws BindingResolutionException
     */
    public function test_invoke()
    {
        Event::fake();
        $controller = app()->make(GetInvoice::class);

        // real invoice in draft status
        $request = Request::create("/api/invoices/{$this->invoice->getId()->toString()}");
        $request->setRouteResolver(function () use ($request) {
            return (new Route('GET', '/api/invoices/{invoice_id}', []))->bind($request);
        });
        $response = $controller($request);
        self::assertInstanceOf(JsonResponse::class, $response);
        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals([
            'id' => $this->invoiceAggregate->getId()->toString(),
            'number' => $this->invoiceAggregate->getNumber()->toString(),
            'date' => $this->invoiceAggregate->getDate()->toDateString(),
            'due_date' => $this->invoiceAggregate->getDueDate()->toDateString(),
            'company' => [
                'name' => $this->invoiceAggregate->getCompany()->getName(),
                'street_address' => $this->invoiceAggregate->getCompany()->getStreet(),
                'city' => $this->invoiceAggregate->getCompany()->getCity(),
                'zip' => $this->invoiceAggregate->getCompany()->getZip(),
                'phone' => $this->invoiceAggregate->getCompany()->getPhone()->toString(),
            ],
            'products' => $this->invoiceAggregate->getProductLines()->map(function (ProductLineEntity $pl) {
                return [
                    'name' => $pl->getProduct()?->getName(),
                    'quantity' => $pl->getQuantity()->toString(),
                    'unit_price' => $pl->getProduct()->getPrice()->toString(),
                    'total' => $pl->getTotal()->toString(),
                    'currency' => $pl->getProduct()->getCurrency()->toString(),
                ];
            })->toArray(),
            'total' => $this->invoiceAggregate->getTotal()->toString(),
            // decided to add these fields as well for testing purpose
            'currency' => $this->invoiceAggregate->getCurrency()->toString(),
            'status' => $this->invoiceAggregate->getStatus()->value,
        ], $response->getOriginalContent());

        // fake uuid
        $uuid = Uuid::uuid4();
        $request = Request::create("/api/invoices/{$uuid}");
        $request->setRouteResolver(function () use ($request) {
            return (new Route('GET', '/api/invoices/{invoice_id}', []))->bind($request);
        });
        self::expectException(ValidationException::class);
        self::expectExceptionMessage('The selected invoice id is invalid');
        $controller($request);
    }
}
