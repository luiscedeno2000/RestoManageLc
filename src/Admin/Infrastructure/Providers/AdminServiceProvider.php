<?php

namespace Src\Admin\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Src\Admin\Application\Services\AdminService;
use Src\Roles\Application\Services\RolesService;
use Src\Users\Application\Services\UsersService;

/**
 * Service Provider para el módulo Admin
 * 
 * Registra las dependencias y bindings del módulo siguiendo principios DDD
 */
class AdminServiceProvider extends ServiceProvider
{
    /**
     * Registrar servicios del contenedor
     * 
     * @return void
     */
    public function register(): void
    {
        // Registrar el servicio
        $this->app->bind(AdminService::class, function ($app) {
            return new AdminService(
                $app->make(UsersService::class),
                $app->make(RolesService::class)
            );
        });
    }

    /**
     * Bootstrap servicios de la aplicación
     * 
     * @return void
     */
    public function boot(): void
    {
        //
    }
}
