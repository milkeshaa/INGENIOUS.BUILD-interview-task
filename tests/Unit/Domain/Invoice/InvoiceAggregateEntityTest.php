<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Invoice;

use App\Domain\Enums\StatusEnum;
use App\Domain\Invoice\InvoiceAggregate;
use App\Domain\Company\Entity as Company;
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
use App\Modules\Invoices\Presentation\ViewModels\InvoiceViewModel;
use Faker\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/** @coversDefaultClass \App\Domain\Invoice\InvoiceAggregate */
class InvoiceAggregateEntityTest extends TestCase
{
    /**
     * @throws InvalidPriceException
     * @throws InvalidEmailException
     * @throws InvalidPhoneException
     * @throws InvalidQuantityException
     */
    public function test_can_create_invoice_aggregate_object()
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

        $viewModel = new InvoiceViewModel(invoice: $invoice);
        self::assertIsArray($viewModel->toArray());
        self::assertNotEmpty($viewModel->toArray());
        self::assertArrayHasKey('id', $viewModel->toArray());
        self::assertArrayHasKey('number', $viewModel->toArray());
        self::assertArrayHasKey('date', $viewModel->toArray());
        self::assertArrayHasKey('due_date', $viewModel->toArray());
        self::assertArrayHasKey('company', $viewModel->toArray());
        self::assertIsArray($viewModel->toArray()['company']);
        self::assertArrayHasKey('name', $viewModel->toArray()['company']);
        self::assertArrayHasKey('street_address', $viewModel->toArray()['company']);
        self::assertArrayHasKey('city', $viewModel->toArray()['company']);
        self::assertArrayHasKey('zip', $viewModel->toArray()['company']);
        self::assertArrayHasKey('phone', $viewModel->toArray()['company']);
        self::assertArrayHasKey('products', $viewModel->toArray());
        self::assertIsArray($viewModel->toArray()['products']);
        foreach ($viewModel->toArray()['products'] as $product) {
            self::assertIsArray($product);
            self::assertArrayHasKey('name', $product);
            self::assertArrayHasKey('quantity', $product);
            self::assertArrayHasKey('unit_price', $product);
            self::assertArrayHasKey('total', $product);
            self::assertArrayHasKey('currency', $product);
        }
        self::assertArrayHasKey('total', $viewModel->toArray());
        self::assertArrayHasKey('currency', $viewModel->toArray());
        self::assertArrayHasKey('status', $viewModel->toArray());
    }
}
