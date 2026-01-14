<?php

namespace Src\Users\Application\Services;

use Src\Roles\Application\Services\RolesService;
use Src\Users\Application\DTOs\DTOCreateUsersRequest;
use Src\Users\Application\DTOs\DTOUpdateUsersRequest;
use Src\Users\Application\DTOs\DTODeleteUsersRequest;
use Src\Users\Application\UseCases\CreateUsersUseCase;
use Src\Users\Application\UseCases\UpdateUsersUseCase;
use Src\Users\Application\UseCases\DeleteUsersUseCase;
use Src\Users\Domain\Entities\User;
use Src\Users\Domain\Exceptions\UserDeletionException;
use Src\Users\Domain\Exceptions\UserEmailAlreadyExistsException;
use Src\Users\Domain\Exceptions\UserNotFoundException;
use Src\Users\Domain\Repositories\UsersRepositoryInterface;

/**
 * Servicio de aplicación para Usuarios
 * 
 * Coordina los casos de uso y proporciona una interfaz de alto nivel
 * para las operaciones relacionadas con usuarios
 */
class UsersService
{
    /**
     * Constructor del servicio
     * 
     * @param CreateUsersUseCase $createUsersUseCase Caso de uso para crear usuarios
     * @param UpdateUsersUseCase $updateUsersUseCase Caso de uso para actualizar usuarios
     * @param DeleteUsersUseCase $deleteUsersUseCase Caso de uso para eliminar usuarios
     * @param UsersRepositoryInterface $usersRepository Repositorio de usuarios
     * @param RolesService $rolesService Servicio de roles para obtener roles disponibles
     */
    public function __construct(
        private readonly CreateUsersUseCase $createUsersUseCase,
        private readonly UpdateUsersUseCase $updateUsersUseCase,
        private readonly DeleteUsersUseCase $deleteUsersUseCase,
        private readonly UsersRepositoryInterface $usersRepository,
        private readonly RolesService $rolesService
    ) {}

    /**
     * Crear un nuevo usuario
     * 
     * @param array $data Datos del usuario a crear
     * @return User Entidad del usuario creado
     * 
     * @throws UserEmailAlreadyExistsException Si ya existe un usuario con el email proporcionado
     */
    public function createUser(array $data): User
    {
        $dto = DTOCreateUsersRequest::fromArray($data);
        return $this->createUsersUseCase->execute($dto);
    }

    /**
     * Obtener todos los usuarios
     * 
     * @return array<User> Array de entidades User
     */
    public function getAllUsers(): array
    {
        return $this->usersRepository->findAll();
    }

    /**
     * Obtener un usuario por ID
     * 
     * @param int $id ID del usuario
     * @return User|null Entidad del usuario o null si no existe
     */
    public function getUserById(int $id): ?User
    {
        return $this->usersRepository->findById($id);
    }

    /**
     * Actualizar un usuario existente
     * 
     * @param int $id ID del usuario a actualizar
     * @param array $data Datos del usuario a actualizar
     * @return User Entidad del usuario actualizado
     * 
     * @throws UserNotFoundException Si no se encuentra el usuario a actualizar
     * @throws UserEmailAlreadyExistsException Si el email ya está en uso por otro usuario
     */
    public function updateUser(int $id, array $data): User
    {
        $data['id'] = $id;
        $dto = DTOUpdateUsersRequest::fromArray($data);
        return $this->updateUsersUseCase->execute($dto);
    }

    /**
     * Eliminar un usuario
     * 
     * @param int $id ID del usuario a eliminar
     * @return bool True si se eliminó correctamente
     * 
     * @throws UserNotFoundException Si no se encuentra el usuario a eliminar
     * @throws UserDeletionException Si ocurre un error al eliminar el usuario
     */
    public function deleteUser(int $id): bool
    {
        $data = ['id' => $id];
        $dto = DTODeleteUsersRequest::fromArray($data);
        return $this->deleteUsersUseCase->execute($dto);
    }

    /**
     * Obtener todos los roles disponibles
     * 
     * @return array Array de roles disponibles
     */
    public function getAvailableRoles(): array
    {
        $roles = $this->rolesService->getAllRoles();
        return array_map(function ($role) {
            return $role->toArray();
        }, $roles);
    }
}
