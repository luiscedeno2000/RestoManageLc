<?php

namespace Src\Users\Application\UseCases;

use Src\Users\Application\DTOs\DTOUpdateUsersRequest;
use Src\Users\Domain\Entities\User;
use Src\Users\Domain\Exceptions\UserEmailAlreadyExistsException;
use Src\Users\Domain\Exceptions\UserNotFoundException;
use Src\Users\Domain\Repositories\UsersRepositoryInterface;
use Src\Shared\Application\Services\AuditService;

/**
 * Caso de uso para actualizar un usuario existente
 * 
 * Implementa la lógica de negocio para actualizar un usuario siguiendo principios DDD
 */
class UpdateUsersUseCase
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
     * Ejecutar el caso de uso para actualizar un usuario
     * 
     * @param DTOUpdateUsersRequest $dto DTO con los datos del usuario a actualizar
     * @return User Entidad del usuario actualizado
     * 
     * @throws UserNotFoundException Si no se encuentra el usuario a actualizar
     * @throws UserEmailAlreadyExistsException Si el email ya está en uso por otro usuario
     */
    public function execute(DTOUpdateUsersRequest $dto): User
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

            // Validar que el email no esté en uso por otro usuario
            $userWithEmail = $this->repository->findByEmail($dto->email);
            if ($userWithEmail && $userWithEmail->id !== $dto->id) {
                $this->auditService->logFailure(
                    $controllerName,
                    $useCaseName,
                    $functionName,
                    "Ya existe otro usuario con el email: {$dto->email}",
                    ['user_id' => $dto->id, 'email' => $dto->email, 'dto' => $dto->toArray()]
                );
                throw new UserEmailAlreadyExistsException($dto->email);
            }

            // Preparar datos para actualizar el usuario
            $data = $dto->toArray();

            // Actualizar el usuario usando el repositorio
            $user = $this->repository->update($dto->id, $data);

            // Log de éxito
            $this->auditService->logSuccess(
                $controllerName,
                $useCaseName,
                $functionName,
                [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_email' => $user->email,
                    'dto' => $dto->toArray(),
                ]
            );

            return $user;
        } catch (UserNotFoundException $e) {
            $this->auditService->logError(
                $controllerName,
                $useCaseName,
                $functionName,
                $e,
                ['dto' => $dto->toArray(), 'user_id' => $dto->id]
            );
            throw $e;
        } catch (UserEmailAlreadyExistsException $e) {
            $this->auditService->logError(
                $controllerName,
                $useCaseName,
                $functionName,
                $e,
                ['dto' => $dto->toArray(), 'email' => $dto->email]
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
