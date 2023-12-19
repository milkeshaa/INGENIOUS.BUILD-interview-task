<?php

declare(strict_types=1);

namespace App\Modules\Invoices\Presentation\Controllers\Http;

use App\Domain\Enums\StatusEnum;
use App\Infrastructure\Controller;
use App\Modules\Invoices\Application\UseCases\RejectInvoice as RejectInvoiceUseCase;
use App\Modules\Invoices\Infrastructure\Repository\InvoiceRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Ramsey\Uuid\Uuid;

class RejectInvoice extends Controller
{
    public function __construct(
        private readonly InvoiceRepositoryInterface $repository,
        private readonly RejectInvoiceUseCase $useCase,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $this->isValid($request);

        try {
            $invoice = $this->repository->find(invoiceId: Uuid::fromString($request->route('invoice_id')));
            $this->useCase->execute($invoice);
            return new JsonResponse(data: [], status: JsonResponse::HTTP_NO_CONTENT);
        } catch (\Throwable $exception) {
            info($exception->getMessage());
            info($exception->getTraceAsString());
            return new JsonResponse(data: [], status: JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    private function isValid(Request $request): void
    {
        $request->merge(['invoice_id' => $request->route('invoice_id')]);
        $request->validate([
            'invoice_id' => [
                'uuid',
                Rule::exists('invoices', 'id')
                    ->where('status', StatusEnum::DRAFT->value)
            ]
        ], [
            'invoice_id' => ['Should be UUID of invoice in draft status']
        ]);
    }
}
