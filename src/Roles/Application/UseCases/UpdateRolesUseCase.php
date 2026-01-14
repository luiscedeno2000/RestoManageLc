<?php

namespace Src\Roles\Application\UseCases;

use Src\Roles\Application\DTOs\DTOUpdateRolesRequest;
use Src\Roles\Domain\Entities\Role;
use Src\Roles\Domain\Exceptions\RoleNotFoundException;
use Src\Roles\Domain\Exceptions\RoleSlugAlreadyExistsException;
use Src\Roles\Domain\Repositories\RolesRepositoryInterface;
use Src\Shared\Application\Services\AuditService;

/**
 * Caso de uso para actualizar un rol existente
 * 
 * Implementa la lógica de negocio para actualizar un rol siguiendo principios DDD
 */
class UpdateRolesUseCase
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
     * Ejecutar el caso de uso para actualizar un rol
     * 
     * @param DTOUpdateRolesRequest $dto DTO con los datos del rol a actualizar
     * @return Role Entidad del rol actualizado
     * 
     * @throws RoleNotFoundException Si no se encuentra el rol a actualizar
     * @throws RoleSlugAlreadyExistsException Si el slug ya está en uso por otro rol
     */
    public function execute(DTOUpdateRolesRequest $dto): Role
    {
        $functionName = __FUNCTION__;
        $useCaseName = self::class;
        $controllerName = 'RolesController';

        try {
            // Verificar que el rol existe
            $existingRole = $this->repository->findById($dto->id);
            if (!$existingRole) {
                $this->auditService->logFailure(
                    $controllerName,
                    $useCaseName,
                    $functionName,
                    "No se encontró el rol con ID: {$dto->id}",
                    ['role_id' => $dto->id, 'dto' => $dto->toArray()]
                );
                throw new RoleNotFoundException($dto->id);
            }

            // Validar que el slug no esté en uso por otro rol
            $roleWithSlug = $this->repository->findBySlug($dto->slug);
            if ($roleWithSlug && $roleWithSlug->id !== $dto->id) {
                $this->auditService->logFailure(
                    $controllerName,
                    $useCaseName,
                    $functionName,
                    "Ya existe otro rol con el slug: {$dto->slug}",
                    ['role_id' => $dto->id, 'slug' => $dto->slug, 'dto' => $dto->toArray()]
                );
                throw new RoleSlugAlreadyExistsException($dto->slug);
            }

            // Preparar datos para actualizar el rol
            $data = $dto->toArray();

            // Actualizar el rol usando el repositorio
            $role = $this->repository->update($dto->id, $data);

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
        } catch (RoleNotFoundException $e) {
            $this->auditService->logError(
                $controllerName,
                $useCaseName,
                $functionName,
                $e,
                ['dto' => $dto->toArray(), 'role_id' => $dto->id]
            );
            throw $e;
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
