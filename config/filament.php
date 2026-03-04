<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Broadcasting
    |--------------------------------------------------------------------------
    |
    | Configuración de Echo para la integración de WebSockets en Filament.
    | Filament usa estos valores para instanciar Laravel Echo en el cliente.
    |
    */

    'broadcasting' => [

        'echo' => [
            'broadcaster' => 'reverb',
            'key' => env('VITE_REVERB_APP_KEY'),
            'wsHost' => env('VITE_REVERB_HOST', '127.0.0.1'),
            'wsPort' => env('VITE_REVERB_PORT', 80),
            'wssPort' => env('VITE_REVERB_PORT', 443),
            'forceTLS' => (env('VITE_REVERB_SCHEME', 'https') === 'https'),
            'enabledTransports' => ['ws', 'wss'],
        ],

    ],

];
