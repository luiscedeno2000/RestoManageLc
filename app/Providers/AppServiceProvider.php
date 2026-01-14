<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Src\Admin\Infrastructure\Providers\AdminServiceProvider;
use Src\Roles\Infrastructure\Providers\RolesServiceProvider;
use Src\Shared\Application\Services\AuditService;
use Src\Users\Infrastructure\Providers\UsersServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Registrar servicio de auditoría
        $this->app->singleton(AuditService::class, function ($app) {
            return new AuditService();
        });

        // Registrar Service Providers de módulos
        $this->app->register(RolesServiceProvider::class);
        $this->app->register(UsersServiceProvider::class);
        $this->app->register(AdminServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
