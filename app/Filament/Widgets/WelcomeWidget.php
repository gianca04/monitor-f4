<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class WelcomeWidget extends Widget
{
    protected string $view = 'filament.widgets.welcome-widget';
    protected int | string | array $columnSpan = 12;

    public static function canView(): bool
    {
        $user = Auth::user();

        return ! $user->hasAnyRole([
            'Administrador',
            'Gerencial',
        ]);
    }
}
