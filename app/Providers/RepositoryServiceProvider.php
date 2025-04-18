<?php

namespace App\Providers;

use App\Repositories\DocumentRepository;
use App\Repositories\RepositoryInterface;
use App\Services\DocumentService;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Documentos
        $this->app->bind(
            DocumentRepository::class, 
            function ($app) {
                return new DocumentRepository($app->make('App\Models\Document'));
            }
        );

        $this->app->bind(
            DocumentService::class, 
            function ($app) {
                return new DocumentService($app->make(DocumentRepository::class));
            }
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
} 