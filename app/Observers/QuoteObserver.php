<?php

namespace App\Observers;

use App\Models\Quote;

class QuoteObserver
{
    /**
     * Handle the Quote "created" event.
     */
    public function created(Quote $quote): void
    {
        //
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
        }
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
