<?php

namespace Src\Roles\Application\UseCases;

use Src\Roles\Application\DTOs\DTODeleteRolesRequest;
use Src\Roles\Domain\Exceptions\RoleDeletionException;
use Src\Roles\Domain\Exceptions\RoleInUseException;
use Src\Roles\Domain\Exceptions\RoleNotFoundException;
use Src\Roles\Domain\Repositories\RolesRepositoryInterface;
use Src\Shared\Application\Services\AuditService;

/**
 * Caso de uso para eliminar un rol
 * 
 * Implementa la lógica de negocio para eliminar un rol siguiendo principios DDD
 */
class DeleteRolesUseCase
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
     * Ejecutar el caso de uso para eliminar un rol
     * 
     * @param DTODeleteRolesRequest $dto DTO con los datos del rol a eliminar
     * @return bool True si se eliminó correctamente
     * 
     * @throws RoleNotFoundException Si no se encuentra el rol a eliminar
     * @throws RoleInUseException Si el rol está siendo utilizado por usuarios
     * @throws RoleDeletionException Si ocurre un error al eliminar el rol
     */
    public function execute(DTODeleteRolesRequest $dto): bool
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

            // Verificar si el rol está en uso por usuarios
            $usersCount = $this->repository->countUsersWithRole($dto->id);
            if ($usersCount > 0) {
                $this->auditService->logFailure(
                    $controllerName,
                    $useCaseName,
                    $functionName,
                    "No se puede eliminar el rol con ID: {$dto->id} porque está siendo utilizado por {$usersCount} usuario(s)",
                    [
                        'role_id' => $dto->id,
                        'role_name' => $existingRole->name,
                        'users_count' => $usersCount,
                        'dto' => $dto->toArray()
                    ]
                );
                throw new RoleInUseException($dto->id, $usersCount);
            }

            // Eliminar el rol usando el repositorio (guardará el motivo antes de eliminar)
            $result = $this->repository->delete($dto->id, $dto->deletion_reason);

            if (!$result) {
                $this->auditService->logFailure(
                    $controllerName,
                    $useCaseName,
                    $functionName,
                    "No se pudo eliminar el rol con ID: {$dto->id}",
                    ['role_id' => $dto->id, 'deletion_reason' => $dto->deletion_reason, 'dto' => $dto->toArray()]
                );
                throw new RoleDeletionException($dto->id);
            }

            // Log de éxito
            $this->auditService->logSuccess(
                $controllerName,
                $useCaseName,
                $functionName,
                [
                    'role_id' => $dto->id,
                    'role_name' => $existingRole->name,
                    'deletion_reason' => $dto->deletion_reason,
                    'dto' => $dto->toArray(),
                ]
            );

            return $result;
        } catch (RoleNotFoundException $e) {
            $this->auditService->logError(
                $controllerName,
                $useCaseName,
                $functionName,
                $e,
                ['dto' => $dto->toArray(), 'role_id' => $dto->id]
            );
            throw $e;
        } catch (RoleInUseException $e) {
            $this->auditService->logError(
                $controllerName,
                $useCaseName,
                $functionName,
                $e,
                [
                    'dto' => $dto->toArray(),
                    'role_id' => $dto->id,
                    'users_count' => $e->getUsersCount()
                ]
            );
            throw $e;
        } catch (RoleDeletionException $e) {
            $this->auditService->logError(
                $controllerName,
                $useCaseName,
                $functionName,
                $e,
                ['dto' => $dto->toArray(), 'role_id' => $dto->id]
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
