<?php

namespace Src\Users\Application\UseCases;

use Src\Users\Application\DTOs\DTOCreateUsersRequest;
use Src\Users\Domain\Entities\User;
use Src\Users\Domain\Exceptions\UserEmailAlreadyExistsException;
use Src\Users\Domain\Repositories\UsersRepositoryInterface;
use Src\Shared\Application\Services\AuditService;

/**
 * Caso de uso para crear un nuevo usuario
 * 
 * Implementa la lógica de negocio para crear un usuario siguiendo principios DDD
 */
class CreateUsersUseCase
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
     * Ejecutar el caso de uso para crear un usuario
     * 
     * @param DTOCreateUsersRequest $dto DTO con los datos del usuario a crear
     * @return User Entidad del usuario creado
     * 
     * @throws UserEmailAlreadyExistsException Si ya existe un usuario con el email proporcionado
     */
    public function execute(DTOCreateUsersRequest $dto): User
    {
        $functionName = __FUNCTION__;
        $useCaseName = self::class;
        $controllerName = 'UsersController';

        try {
            // Validar que el email no exista
            $existingUser = $this->repository->findByEmail($dto->email);
            if ($existingUser) {
                $this->auditService->logFailure(
                    $controllerName,
                    $useCaseName,
                    $functionName,
                    "Ya existe un usuario con el email: {$dto->email}",
                    ['email' => $dto->email, 'dto' => $dto->toArray()]
                );
                throw new UserEmailAlreadyExistsException($dto->email);
            }

            // Preparar datos para crear el usuario
            $data = $dto->toArray();

            // Crear el usuario usando el repositorio
            $user = $this->repository->create($data);

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
