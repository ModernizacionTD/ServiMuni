<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Google\Client as GoogleClient;
use Google\Service\Sheets;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('sheets', function ($app) {
            $client = new GoogleClient();
            $client->setApplicationName(config('services.google.application_name'));
            
            // Actualizado: Cambiar de READONLY a permisos completos para permitir escritura
            $client->setScopes([
                Sheets::SPREADSHEETS, // Permisos completos para leer y escribir
                // Sheets::SPREADSHEETS_READONLY  // Solo lectura (comentado)
            ]);
            
            // Use service account
            $serviceAccountPath = storage_path('app/google-credentials.json');
            if (file_exists($serviceAccountPath)) {
                $client->setAuthConfig($serviceAccountPath);
                $client->setAccessType('offline');
            } else {
                throw new \Exception('Google service account credentials file not found at: ' . $serviceAccountPath);
            }
            
            return new Sheets($client);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}