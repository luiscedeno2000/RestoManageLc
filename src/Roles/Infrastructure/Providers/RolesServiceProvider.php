<?php

namespace Src\Roles\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Src\Roles\Application\Services\RolesService;
use Src\Roles\Application\UseCases\CreateRolesUseCase;
use Src\Roles\Application\UseCases\UpdateRolesUseCase;
use Src\Roles\Application\UseCases\DeleteRolesUseCase;
use Src\Roles\Domain\Repositories\RolesRepositoryInterface;
use Src\Roles\Infrastructure\Repositories\EloquentRolesRepository;
use Src\Shared\Application\Services\AuditService;

/**
 * Service Provider para el módulo Roles
 * 
 * Registra las dependencias y bindings del módulo siguiendo principios DDD
 */
class RolesServiceProvider extends ServiceProvider
{
    /**
     * Registrar servicios del contenedor
     * 
     * @return void
     */
    public function register(): void
    {
        // Registrar el repositorio
        $this->app->bind(
            RolesRepositoryInterface::class,
            EloquentRolesRepository::class
        );

        // Registrar el caso de uso para crear roles
        $this->app->bind(CreateRolesUseCase::class, function ($app) {
            return new CreateRolesUseCase(
                $app->make(RolesRepositoryInterface::class),
                $app->make(AuditService::class)
            );
        });

        // Registrar el caso de uso para actualizar roles
        $this->app->bind(UpdateRolesUseCase::class, function ($app) {
            return new UpdateRolesUseCase(
                $app->make(RolesRepositoryInterface::class),
                $app->make(AuditService::class)
            );
        });

        // Registrar el caso de uso para eliminar roles
        $this->app->bind(DeleteRolesUseCase::class, function ($app) {
            return new DeleteRolesUseCase(
                $app->make(RolesRepositoryInterface::class),
                $app->make(AuditService::class)
            );
        });

        // Registrar el servicio
        $this->app->bind(RolesService::class, function ($app) {
            return new RolesService(
                $app->make(CreateRolesUseCase::class),
                $app->make(UpdateRolesUseCase::class),
                $app->make(DeleteRolesUseCase::class),
                $app->make(RolesRepositoryInterface::class)
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
