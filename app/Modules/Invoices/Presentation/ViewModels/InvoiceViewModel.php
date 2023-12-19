<?php

declare(strict_types=1);

namespace App\Modules\Invoices\Presentation\ViewModels;
use App\Domain\Invoice\Entity as Invoice;
use App\Domain\Product\ProductLineEntity;
use App\Domain\Shared\ValueObject\Price\Exceptions\InvalidPriceException;

readonly class InvoiceViewModel
{
    public function __construct(
        private Invoice $invoice,
    ) {}

    /**
     * @throws InvalidPriceException
     */
    public function toArray(): array
    {
        return [
            'id' => $this->invoice->getId()->toString(),
            'number' => $this->invoice->getNumber()->toString(),
            'date' => $this->invoice->getDate()->toDateString(),
            'due_date' => $this->invoice->getDueDate()->toDateString(),
            'company' => $this->invoice->getCompany() ? [
                'name' => $this->invoice->getCompany()->getName(),
                'street_address' => $this->invoice->getCompany()->getStreet(),
                'city' => $this->invoice->getCompany()->getCity(),
                'zip' => $this->invoice->getCompany()->getZip(),
                'phone' => $this->invoice->getCompany()->getPhone()->toString(),
            ] : null,
            'products' => $this->invoice->getProductLines()->map(function (ProductLineEntity $pl) {
                return [
                    'name' => $pl->getProduct()?->getName(),
                    'quantity' => $pl->getQuantity()->toString(),
                    'unit_price' => $pl->getProduct()->getPrice()->toString(),
                    'total' => $pl->getTotal()->toString(),
                    'currency' => $pl->getProduct()->getCurrency()->toString(),
                ];
            })->toArray(),
            'total' => $this->invoice->getTotal()->toString(),
            // decided to add these fields as well for testing purpose
            'currency' => $this->invoice->getCurrency()->toString(),
            'status' => $this->invoice->getStatus()->value,
        ];
    }
}
