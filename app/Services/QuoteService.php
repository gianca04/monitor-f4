<?php

namespace App\Services;

use App\Models\Quote;
use App\Models\QuoteWarehouse;
use App\Models\Project;
use App\Models\ProjectRequirement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Enums\QuoteItemType;

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
    public function handleDetailChange(\App\Models\QuoteDetail $detail): void
    {
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

        // Load project and subClient for the name
        $quote->loadMissing(['project.subClient', 'details']);
        $project = $quote->project;

        if (!$project) {
            return;
        }

        // Create the Dispatch Guide
        $subClientName = $project->subClient->name ?? 'N/A';
        $listName = "Guía #01 - {$project->name} - {$subClientName}";

        $dispatchGuide = \App\Models\DispatchGuide::firstOrCreate(
            [
                'project_id' => $project->id,
                'name' => $listName,
            ],
            [
                'status' => 'pending',
                'tracking_number' => null,
                'required_shipping_date' => null,
            ]
        );

        foreach ($quote->details as $detail) {
            // Filter only 'SUMINISTRO' type (or others if needed in the future)
            if ($detail->item_type === QuoteItemType::SUMINISTRO) {
                ProjectRequirement::updateOrCreate(
                    [
                        'requirementable_id'   => $detail->id,
                        'requirementable_type' => \App\Models\QuoteDetail::class,
                    ],
                    [
                        'project_id'          => $quote->project_id,
                        'dispatch_guide_id'   => $dispatchGuide->id,
                        'quantity'            => $detail->quantity,
                        'price_unit'          => $detail->unit_price,
                        'comments'            => $detail->description ?? $detail->comment,
                        'type'                => \App\Enums\RequirementType::CONSUMIBLE,
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
        // Delete requirements associated with this project
        ProjectRequirement::where('project_id', $quote->project_id)->delete();

        // Also delete the dispatch guides associated with this project
        \App\Models\DispatchGuide::where('project_id', $quote->project_id)->delete();
    }
}
