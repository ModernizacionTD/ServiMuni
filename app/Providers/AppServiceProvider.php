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
            $client->setApplicationName('ServiMuni');
            
            // Usar los scopes de la configuración
            $client->setScopes(config('services.google.scopes', [Sheets::SPREADSHEETS]));
            
            // Buscar credenciales en múltiples ubicaciones
            $credentialPaths = [
                storage_path('app/google-credentials.json'),
                storage_path('app/google/credentials.json'),
                storage_path('app/credentials.json'),
            ];
            
            $serviceAccountPath = null;
            foreach ($credentialPaths as $path) {
                if (file_exists($path)) {
                    $serviceAccountPath = $path;
                    break;
                }
            }
            
            if ($serviceAccountPath) {
                $client->setAuthConfig($serviceAccountPath);
                $client->setAccessType('offline');
                \Log::info('Google credentials loaded from: ' . $serviceAccountPath);
            } else {
                $searchedPaths = implode(', ', $credentialPaths);
                throw new \Exception('Google service account credentials file not found. Searched in: ' . $searchedPaths);
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