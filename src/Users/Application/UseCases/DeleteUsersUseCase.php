<?php

namespace Src\Users\Application\UseCases;

use Src\Users\Application\DTOs\DTODeleteUsersRequest;
use Src\Users\Domain\Exceptions\UserDeletionException;
use Src\Users\Domain\Exceptions\UserNotFoundException;
use Src\Users\Domain\Repositories\UsersRepositoryInterface;
use Src\Shared\Application\Services\AuditService;

/**
 * Caso de uso para eliminar un usuario
 * 
 * Implementa la lógica de negocio para eliminar un usuario siguiendo principios DDD
 */
class DeleteUsersUseCase
{
    /**
     * Constructor del caso de uso
     * 
     * @param UsersRepositoryInterface $repository Repositorio de usuarios
     * @param AuditService $auditService Servicio de auditoría
     */
    public function __construct(
        private readonly UsersRepositoryInterface $repository,
        private readonly AuditService $auditService
    ) {}

    /**
     * Ejecutar el caso de uso para eliminar un usuario
     * 
     * @param DTODeleteUsersRequest $dto DTO con los datos del usuario a eliminar
     * @return bool True si se eliminó correctamente
     * 
     * @throws UserNotFoundException Si no se encuentra el usuario a eliminar
     * @throws UserDeletionException Si ocurre un error al eliminar el usuario
     */
    public function execute(DTODeleteUsersRequest $dto): bool
    {
        $functionName = __FUNCTION__;
        $useCaseName = self::class;
        $controllerName = 'UsersController';

        try {
            // Verificar que el usuario existe
            $existingUser = $this->repository->findById($dto->id);
            if (!$existingUser) {
                $this->auditService->logFailure(
                    $controllerName,
                    $useCaseName,
                    $functionName,
                    "No se encontró el usuario con ID: {$dto->id}",
                    ['user_id' => $dto->id, 'dto' => $dto->toArray()]
                );
                throw new UserNotFoundException($dto->id);
            }

            // Eliminar el usuario usando el repositorio
            $result = $this->repository->delete($dto->id);

            if (!$result) {
                $this->auditService->logFailure(
                    $controllerName,
                    $useCaseName,
                    $functionName,
                    "No se pudo eliminar el usuario con ID: {$dto->id}",
                    ['user_id' => $dto->id, 'dto' => $dto->toArray()]
                );
                throw new UserDeletionException($dto->id);
            }

            // Log de éxito
            $this->auditService->logSuccess(
                $controllerName,
                $useCaseName,
                $functionName,
                [
                    'user_id' => $dto->id,
                    'user_name' => $existingUser->name,
                    'user_email' => $existingUser->email,
                    'dto' => $dto->toArray(),
                ]
            );

            return $result;
        } catch (UserNotFoundException $e) {
            $this->auditService->logError(
                $controllerName,
                $useCaseName,
                $functionName,
                $e,
                ['dto' => $dto->toArray(), 'user_id' => $dto->id]
            );
            throw $e;
        } catch (UserDeletionException $e) {
            $this->auditService->logError(
                $controllerName,
                $useCaseName,
                $functionName,
                $e,
                ['dto' => $dto->toArray(), 'user_id' => $dto->id]
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
