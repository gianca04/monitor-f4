<?php

namespace App\Observers;

use App\Models\Quote;
use App\Models\QuoteWarehouse;
use Illuminate\Support\Facades\Auth;

class QuoteObserver
{
    /**
     * Handle the Quote "created" event.
     */
    public function created(Quote $quote): void
    {
        if ($quote->status === 'Aprobado') {
            $this->createWarehouse($quote);
        }
    }


    /**
     * Handle the Quote "updated" event.
     */
    public function updated(Quote $quote): void
    {
        if ($quote->isDirty('status')) {
            $project = $quote->project;

            if ($project) {
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
                        // Anular otras cotizaciones aprobadas del mismo proyecto
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
                        break;
                }
            }

            // Gestión de Almacén basada en cambio de estado
            if ($quote->status === 'Aprobado') {
                $this->createWarehouse($quote);
            } elseif ($quote->status === 'Anulado') {
                $this->deleteWarehouse($quote);
            }
        }
    }

    /**
     * Create a warehouse record for the quote if it doesn't exist.
     */
    protected function createWarehouse(Quote $quote): void
    {
        $exists = QuoteWarehouse::where('quote_id', $quote->id)->exists();
        if (!$exists) {
            QuoteWarehouse::create([
                'quote_id'    => $quote->id,
                'employee_id' => Auth::id(),
                'status'      => 'Pendiente',
                'observations' => null,
            ]);
        }
    }

    /**
     * Delete the warehouse record for the quote.
     */
    protected function deleteWarehouse(Quote $quote): void
    {
        QuoteWarehouse::where('quote_id', $quote->id)->delete();
    }

    /**
     * Handle the Quote "deleted" event.
     */
    public function deleted(Quote $quote): void
    {
        //
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
