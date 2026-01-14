<?php

namespace Src\Users\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Src\Roles\Application\Services\RolesService;
use Src\Shared\Application\Services\AuditService;
use Src\Users\Application\Services\UsersService;
use Src\Users\Application\UseCases\CreateUsersUseCase;
use Src\Users\Application\UseCases\UpdateUsersUseCase;
use Src\Users\Application\UseCases\DeleteUsersUseCase;
use Src\Users\Domain\Repositories\UsersRepositoryInterface;
use Src\Users\Infrastructure\Repositories\EloquentUsersRepository;

/**
 * Service Provider para el módulo Users
 * 
 * Registra las dependencias y bindings del módulo siguiendo principios DDD
 */
class UsersServiceProvider extends ServiceProvider
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
            UsersRepositoryInterface::class,
            EloquentUsersRepository::class
        );

        // Registrar el caso de uso para crear usuarios
        $this->app->bind(CreateUsersUseCase::class, function ($app) {
            return new CreateUsersUseCase(
                $app->make(UsersRepositoryInterface::class),
                $app->make(AuditService::class)
            );
        });

        // Registrar el caso de uso para actualizar usuarios
        $this->app->bind(UpdateUsersUseCase::class, function ($app) {
            return new UpdateUsersUseCase(
                $app->make(UsersRepositoryInterface::class),
                $app->make(AuditService::class)
            );
        });

        // Registrar el caso de uso para eliminar usuarios
        $this->app->bind(DeleteUsersUseCase::class, function ($app) {
            return new DeleteUsersUseCase(
                $app->make(UsersRepositoryInterface::class),
                $app->make(AuditService::class)
            );
        });

        // Registrar el servicio
        $this->app->bind(UsersService::class, function ($app) {
            return new UsersService(
                $app->make(CreateUsersUseCase::class),
                $app->make(UpdateUsersUseCase::class),
                $app->make(DeleteUsersUseCase::class),
                $app->make(UsersRepositoryInterface::class),
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
