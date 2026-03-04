<?php

namespace App\Providers;

use App\Services\PushNotificationService;
use Filament\Facades\Filament;
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(PushNotificationService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureOpenSsl();

        FilamentView::registerRenderHook(
            'panels::auth.login.form.after',
            fn(): string => Blade::render('@vite(\'resources/css/custom-login.css\')'),
        );

        // Inyectar componente de suscripción push en el body del panel
        FilamentView::registerRenderHook(
            'panels::body.end',
            fn(): string => Blade::render('<x-push-notification-subscriber />'),
        );
        if ($this->app->environment('production') || env('APP_URL') == 'https://superfood.sat-sistemas.uk') {
            URL::forceScheme('https');

            // Esto arregla el problema de "Mixed Content" con el Load Balancer de Cloudflare
            $this->app['request']->server->set('HTTPS', 'on');
        }
    }

    /**
     * Configure OpenSSL for environments where openssl.cnf is not in the default path (e.g. XAMPP on Windows).
     */
    private function configureOpenSsl(): void
    {
        if ($this->app->environment('local') && PHP_OS_FAMILY === 'Windows') {
            $candidates = [
                // Laragon
                'C:\\laragon\\bin\\apache\\httpd-2.4.66-260107-Win64-VS18\\conf\\openssl.cnf',
                'C:\\laragon\\bin\\php\\php-8.3.30-Win32-vs16-x64\\extras\\ssl\\openssl.cnf',
                // XAMPP
                'C:\\xampp\\apache\\conf\\openssl.cnf',
                'C:\\xampp\\php\\extras\\openssl\\openssl.cnf',
            ];

            foreach ($candidates as $path) {
                if (file_exists($path)) {
                    putenv("OPENSSL_CONF={$path}");
                    break;
                }
            }
        }
    }
}
