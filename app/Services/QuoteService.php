<?php

namespace App\Services;

use App\Models\Quote;
use App\Models\QuoteWarehouse;
use App\Models\Project;
use App\Models\ProjectRequirement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class QuoteService
{
    /**
     * Update the associated Project's status based on the Quote's status.
     *
     * @param Quote $quote
     * @return void
     */
    public function syncProjectStatus(Quote $quote): void
    {
        $project = $quote->project;

        if (!$project) {
            return;
        }

        switch ($quote->status) {
            case 'Pendiente':
                $project->update(['status' => 'Pendiente']);
                break;

            case 'Enviado':
                $project->update([
                    'status' => 'Enviado',
                    'quote_sent_at' => now(),
                ]);
                break;

            case 'Aprobado':
                // Cancel other approved quotes for the same project
                Quote::where('project_id', $quote->project_id)
                    ->where('id', '!=', $quote->id)
                    ->where('status', 'Aprobado')
                    ->update(['status' => 'Anulado']);

                $project->update([
                    'status' => 'Aprobado',
                    'quote_approved_at' => now(),
                ]);
                break;

            case 'Anulado':
                $project->update(['status' => 'Anulado']);
                // Limpiar requisitos generados si se anula la cotización
                $this->clearProjectRequirements($quote);
                break;
        }
    }

    /**
     * Handle warehouse record creation or deletion based on Quote status.
     *
     * @param Quote $quote
     * @return void
     */
    public function handleWarehouseLogic(Quote $quote): void
    {
        if ($quote->status === 'Aprobado') {
            $this->createWarehouse($quote);
        } elseif ($quote->status === 'Anulado') {
            $this->deleteWarehouse($quote);
        }
    }

    /**
     * Create a warehouse record for the quote if it doesn't exist.
     *
     * @param Quote $quote
     * @return void
     */
    protected function createWarehouse(Quote $quote): void
    {
        $exists = QuoteWarehouse::where('quote_id', $quote->id)->exists();
        if (!$exists) {
            QuoteWarehouse::create([
                'quote_id'    => $quote->id,
                'employee_id' => Auth::id() ?? 1, // Fallback purely for safety if triggered by CLI/Job
                'status'      => 'Pendiente',
                'observations' => null,
            ]);
        }
    }

    /**
     * Delete the warehouse record for the quote.
     *
     * @param Quote $quote
     * @return void
     */
    protected function deleteWarehouse(Quote $quote): void
    {
        QuoteWarehouse::where('quote_id', $quote->id)->delete();
    }
    /**
     * Handle updates to a quote detail.
     * Currently touches the parent quote to update timestamps.
     *
     * @param \App\Models\QuoteDetail $detail
     * @return void
     */
    public function handleDetailChange(\App\Models\QuoteDetail $detail): void
    {
        // Update parent quote timestamp or perform recalculations if needed
        $detail->quote()->touch();
    }

    /**
     * Generate Project Requirements from Quote Details when approved.
     *
     * @param Quote $quote
     * @return void
     */
    public function generateProjectRequirements(Quote $quote): void
    {
        if ($quote->status !== 'Aprobado' || !$quote->project_id) {
            return;
        }

        // Optional: Clear existing requirements for this quote to avoid duplicates or stale data
        // $this->clearProjectRequirements($quote); 

        // Load details if not already loaded
        $quote->loadMissing('details');

        foreach ($quote->details as $detail) {
            // Filter only 'SUMINISTRO' type
            if ($detail->item_type === 'SUMINISTRO') {
                \App\Models\ProjectRequirement::updateOrCreate(
                    [
                        'quote_detail_id' => $detail->id,
                    ],
                    [
                        'project_id'      => $quote->project_id,
                        'requirement_id'  => null,
                        'quantity'        => $detail->quantity,
                        'price_unit'      => $detail->unit_price,
                        'comments'        => $detail->description ?? $detail->comment,
                    ]
                );
            }
        }
    }

    /**
     * Clear Project Requirements generated from this Quote.
     *
     * @param Quote $quote
     * @return void
     */
    public function clearProjectRequirements(Quote $quote): void
    {
        // Delete requirements associated with this quote's details
        ProjectRequirement::whereIn(
            'quote_detail_id',
            $quote->details()->pluck('id')
        )->delete();
    }
}
