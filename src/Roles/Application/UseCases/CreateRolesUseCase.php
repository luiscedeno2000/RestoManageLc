<?php

namespace Src\Roles\Application\UseCases;

use Src\Roles\Application\DTOs\DTOCreateRolesRequest;
use Src\Roles\Domain\Entities\Role;
use Src\Roles\Domain\Exceptions\RoleSlugAlreadyExistsException;
use Src\Roles\Domain\Repositories\RolesRepositoryInterface;
use Src\Shared\Application\Services\AuditService;

/**
 * Caso de uso para crear un nuevo rol
 * 
 * Implementa la lógica de negocio para crear un rol siguiendo principios DDD
 */
class CreateRolesUseCase
{
    /**
     * Constructor del caso de uso
     * 
     * @param RolesRepositoryInterface $repository Repositorio de roles
     * @param AuditService $auditService Servicio de auditoría
     */
    public function __construct(
        private readonly RolesRepositoryInterface $repository,
        private readonly AuditService $auditService
    ) {}

    /**
     * Ejecutar el caso de uso para crear un rol
     * 
     * @param DTOCreateRolesRequest $dto DTO con los datos del rol a crear
     * @return Role Entidad del rol creado
     * 
     * @throws RoleSlugAlreadyExistsException Si ya existe un rol con el slug proporcionado
     */
    public function execute(DTOCreateRolesRequest $dto): Role
    {
        $functionName = __FUNCTION__;
        $useCaseName = self::class;
        $controllerName = 'RolesController';

        try {
            // Validar que el slug no exista
            $existingRole = $this->repository->findBySlug($dto->slug);
            if ($existingRole) {
                $this->auditService->logFailure(
                    $controllerName,
                    $useCaseName,
                    $functionName,
                    "Ya existe un rol con el slug: {$dto->slug}",
                    ['slug' => $dto->slug, 'dto' => $dto->toArray()]
                );
                throw new RoleSlugAlreadyExistsException($dto->slug);
            }

            // Preparar datos para crear el rol
            $data = $dto->toArray();

            // Crear el rol usando el repositorio
            $role = $this->repository->create($data);

            // Log de éxito
            $this->auditService->logSuccess(
                $controllerName,
                $useCaseName,
                $functionName,
                [
                    'role_id' => $role->id,
                    'role_name' => $role->name,
                    'role_slug' => $role->slug,
                    'dto' => $dto->toArray(),
                ]
            );

            return $role;
        } catch (RoleSlugAlreadyExistsException $e) {
            $this->auditService->logError(
                $controllerName,
                $useCaseName,
                $functionName,
                $e,
                ['dto' => $dto->toArray(), 'slug' => $dto->slug]
            );
            throw $e;
        } catch (\Throwable $e) {
            $this->auditService->logError(
                $controllerName,
                $useCaseName,
                $functionName,
                $e,
                ['dto' => $dto->toArray()]
            );
            throw $e;
        }
    }
}
