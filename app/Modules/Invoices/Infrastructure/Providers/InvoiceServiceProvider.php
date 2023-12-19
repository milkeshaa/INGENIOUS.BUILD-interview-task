<?php

declare(strict_types=1);

namespace App\Modules\Invoices\Infrastructure\Providers;

use App\Modules\Approval\Api\Events\EntityApproved;
use App\Modules\Approval\Api\Events\EntityRejected;
use App\Modules\Invoices\Api\Listeners\InvoiceApprovedListener;
use App\Modules\Invoices\Api\Listeners\InvoiceRejectedListener;
use App\Modules\Invoices\Infrastructure\Repository\InvoiceRepository;
use App\Modules\Invoices\Infrastructure\Repository\InvoiceRepositoryInterface;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class InvoiceServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function register(): void
    {
        $this->app->scoped(InvoiceRepositoryInterface::class, InvoiceRepository::class);
        Event::listen( EntityApproved::class, InvoiceApprovedListener::class);
        Event::listen( EntityRejected::class, InvoiceRejectedListener::class);
    }

    /** @return array<class-string> */
    public function provides(): array
    {
        return [
            InvoiceRepositoryInterface::class,
        ];
    }
}
