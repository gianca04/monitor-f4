<?php

namespace App\Observers;

use App\Models\QuoteDetail;
use App\Services\QuoteService;

class QuoteDetailObserver
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
     * Handle the QuoteDetail "created" event.
     */
    public function created(QuoteDetail $quoteDetail): void
    {
        $this->quoteService->handleDetailChange($quoteDetail);
    }

    /**
     * Handle the QuoteDetail "updated" event.
     */
    public function updated(QuoteDetail $quoteDetail): void
    {
        $this->quoteService->handleDetailChange($quoteDetail);
    }

    /**
     * Handle the QuoteDetail "deleted" event.
     */
    public function deleted(QuoteDetail $quoteDetail): void
    {
        $this->quoteService->handleDetailChange($quoteDetail);
    }

    /**
     * Handle the QuoteDetail "restored" event.
     */
    public function restored(QuoteDetail $quoteDetail): void
    {
        $this->quoteService->handleDetailChange($quoteDetail);
    }

    /**
     * Handle the QuoteDetail "force deleted" event.
     */
    public function forceDeleted(QuoteDetail $quoteDetail): void
    {
        $this->quoteService->handleDetailChange($quoteDetail);
    }
}
