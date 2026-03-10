<?php

namespace App\Observers;

use App\Models\QuoteWarehouse;

class QuoteWarehouseObserver
{
    /**
     * Handle the QuoteWarehouse "created" event.
     */
    public function created(QuoteWarehouse $quoteWarehouse): void
    {
        //
    }

    /**
     * Handle the QuoteWarehouse "updated" event.
     */
    public function updated(QuoteWarehouse $quoteWarehouse): void
    {
        if ($quoteWarehouse->wasChanged('status') && $quoteWarehouse->status === 'Atendido') {
            $users = \App\Models\User::role(['Almacen', 'Gerencial'])->get();

            if ($users->isNotEmpty()) {
                // Notificación interna en el panel (Filament)
                \Filament\Notifications\Notification::make()
                    ->title('Despacho Completado')
                    ->body("La guía #{$quoteWarehouse->id} ha sido despachada y atendida.")
                    ->success()
                    ->sendToDatabase($users);

                // Notificación Push (WebPush a los dispositivos)
                $pushService = app(\App\Services\PushNotificationService::class);
                $pushService->sendToMany(
                    $users,
                    'Despacho Completado',
                    "La guía #{$quoteWarehouse->id} ha sido despachada y atendida en su totalidad.",
                    url: null,
                    tag: 'warehouse-dispatch',
                    icon: 'heroicon-o-check-circle',
                    iconColor: 'success',
                    status: 'success'
                );
            }
        }
    }

    /**
     * Handle the QuoteWarehouse "deleted" event.
     */
    public function deleted(QuoteWarehouse $quoteWarehouse): void
    {
        //
    }

    /**
     * Handle the QuoteWarehouse "restored" event.
     */
    public function restored(QuoteWarehouse $quoteWarehouse): void
    {
        //
    }

    /**
     * Handle the QuoteWarehouse "force deleted" event.
     */
    public function forceDeleted(QuoteWarehouse $quoteWarehouse): void
    {
        //
    }
}
