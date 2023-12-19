<?php

declare(strict_types=1);

namespace App\Modules\Invoices\Presentation\Controllers\Http;

use App\Infrastructure\Controller;
use App\Modules\Invoices\Infrastructure\Repository\InvoiceRepositoryInterface;
use App\Modules\Invoices\Presentation\ViewModels\InvoiceViewModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

class GetInvoice extends Controller
{
    public function __construct(
        private readonly InvoiceRepositoryInterface $repository,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $this->isValid($request);

        $invoice = $this->repository->getAggregate(invoiceId: Uuid::fromString($request->route('invoice_id')));
        $viewModel = new InvoiceViewModel(invoice: $invoice);
        return new JsonResponse(data: $viewModel->toArray(), status: JsonResponse::HTTP_OK);
    }

    private function isValid(Request $request): void
    {
        $request->merge(['invoice_id' => $request->route('invoice_id')]);
        $request->validate([
            'invoice_id' => [
                'uuid',
                'exists:invoices,id'
            ]
        ]);
    }
}
