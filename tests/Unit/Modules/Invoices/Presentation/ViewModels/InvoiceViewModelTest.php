<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Invoices\Presentation\ViewModels;

use App\Domain\Company\Entity as Company;
use App\Domain\Enums\StatusEnum;
use App\Domain\Invoice\InvoiceAggregate;
use App\Domain\Product\Entity as Product;
use App\Domain\Product\ProductLineEntity;
use App\Domain\Shared\ValueObject\Currency\Currency;
use App\Domain\Shared\ValueObject\Email\Email;
use App\Domain\Shared\ValueObject\Phone\Phone;
use App\Domain\Shared\ValueObject\Price\Price;
use App\Domain\Shared\ValueObject\Quantity\Quantity;
use Faker\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/** @coversDefaultClass \App\Modules\Invoices\Presentation\ViewModels\InvoiceViewModel */
class InvoiceViewModelTest extends TestCase
{
    public function test_can_create_invoice_view_model()
    {
        $faker = Factory::create();
        $invoice = new InvoiceAggregate(
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
                createdAt: Carbon::now(),
                updatedAt: Carbon::now(),
            ),
            productLines: Collection::make([
                new ProductLineEntity(
                    id: Uuid::uuid4(),
                    invoice: null,
                    product: new Product(
                        id: Uuid::uuid4(),
                        name: 'razer',
                        price: new Price(price: 1000),
                        currency: new Currency(currency: 'usd'),
                        createdAt: Carbon::now(),
                        updatedAt: Carbon::now(),
                    ),
                    quantity: new Quantity(quantity: 12),
                    createdAt: Carbon::now(),
                    updatedAt: Carbon::now(),
                ),
                new ProductLineEntity(
                    id: Uuid::uuid4(),
                    invoice: null,
                    product: new Product(
                        id: Uuid::uuid4(),
                        name: 'pepsi',
                        price: new Price(price: 1200),
                        currency: new Currency(currency: 'usd'),
                        createdAt: Carbon::now(),
                        updatedAt: Carbon::now(),
                    ),
                    quantity: new Quantity(quantity: 5),
                    createdAt: Carbon::now(),
                    updatedAt: Carbon::now(),
                ),
            ]),
            status: StatusEnum::APPROVED,
            createdAt: Carbon::now(),
            updatedAt: Carbon::now()
        );

        self::assertEquals(18000, $invoice->getTotal()->getPrice());
        self::assertEquals('usd', $invoice->getCurrency());
        self::assertEquals(StatusEnum::APPROVED, $invoice->getStatus());
        self::assertNotNull($invoice->getCompany());
        self::assertInstanceOf(Company::class, $invoice->getCompany());
        self::assertNotNull($invoice->getProductLines());
        self::assertInstanceOf(Collection::class, $invoice->getProductLines());
        self::assertCount(2, $invoice->getProductLines());
    }
}
