<?php

namespace App\Observers;

use App\Models\Quote;
use App\Services\QuoteService;

class QuoteObserver
{
    /**
     * @var QuoteService
     */
    protected $quoteService;

    public function __construct(QuoteService $quoteService)
    {
        $this->quoteService = $quoteService;
    }

    /**
     * Handle the Quote "created" event.
     */
    public function created(Quote $quote): void
    {
        // Only trigger initial sync if status is already 'Aprobado' (uncommon but possible)
        if ($quote->status === 'Aprobado') {
            $this->quoteService->handleWarehouseLogic($quote);
            $this->quoteService->syncProjectStatus($quote);
            $this->quoteService->generateProjectRequirements($quote);
        }
    }

    /**
     * Handle the Quote "updated" event.
     */
    public function updated(Quote $quote): void
    {
        if ($quote->isDirty('status')) {
            // Apply business rules for status changes
            $this->quoteService->syncProjectStatus($quote);
            $this->quoteService->handleWarehouseLogic($quote);
            $this->quoteService->generateProjectRequirements($quote);
        }
    }

    /**
     * Handle the Quote "deleted" event.
     */
    public function deleted(Quote $quote): void
    {
        // Add cleanup logic here if needed via service
    }

    /**
     * Handle the Quote "restored" event.
     */
    public function restored(Quote $quote): void
    {
        //
    }

    /**
     * Handle the Quote "force deleted" event.
     */
    public function forceDeleted(Quote $quote): void
    {
        //
    }
}
