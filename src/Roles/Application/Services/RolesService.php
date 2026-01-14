<?php

namespace Src\Roles\Application\Services;

use Src\Roles\Application\DTOs\DTOCreateRolesRequest;
use Src\Roles\Application\DTOs\DTOUpdateRolesRequest;
use Src\Roles\Application\DTOs\DTODeleteRolesRequest;
use Src\Roles\Application\UseCases\CreateRolesUseCase;
use Src\Roles\Application\UseCases\UpdateRolesUseCase;
use Src\Roles\Application\UseCases\DeleteRolesUseCase;
use Src\Roles\Domain\Entities\Role;
use Src\Roles\Domain\Exceptions\RoleDeletionException;
use Src\Roles\Domain\Exceptions\RoleNotFoundException;
use Src\Roles\Domain\Exceptions\RoleSlugAlreadyExistsException;
use Src\Roles\Domain\Repositories\RolesRepositoryInterface;

/**
 * Servicio de aplicación para Roles
 * 
 * Coordina los casos de uso y proporciona una interfaz de alto nivel
 * para las operaciones relacionadas con roles
 */
class RolesService
{
    /**
     * Constructor del servicio
     * 
     * @param CreateRolesUseCase $createRolesUseCase Caso de uso para crear roles
     * @param UpdateRolesUseCase $updateRolesUseCase Caso de uso para actualizar roles
     * @param DeleteRolesUseCase $deleteRolesUseCase Caso de uso para eliminar roles
     * @param RolesRepositoryInterface $rolesRepository Repositorio de roles
     */
    public function __construct(
        private readonly CreateRolesUseCase $createRolesUseCase,
        private readonly UpdateRolesUseCase $updateRolesUseCase,
        private readonly DeleteRolesUseCase $deleteRolesUseCase,
        private readonly RolesRepositoryInterface $rolesRepository
    ) {}

    /**
     * Crear un nuevo rol
     * 
     * @param array $data Datos del rol a crear
     * @return Role Entidad del rol creado
     * 
     * @throws RoleSlugAlreadyExistsException Si ya existe un rol con el slug proporcionado
     */
    public function createRole(array $data): Role
    {
        $dto = DTOCreateRolesRequest::fromArray($data);
        return $this->createRolesUseCase->execute($dto);
    }

    /**
     * Obtener todos los roles
     * 
     * @return array<Role> Array de entidades Role
     */
    public function getAllRoles(): array
    {
        return $this->rolesRepository->findAll();
    }

    /**
     * Obtener un rol por ID
     * 
     * @param int $id ID del rol
     * @return Role|null Entidad del rol o null si no existe
     */
    public function getRoleById(int $id): ?Role
    {
        return $this->rolesRepository->findById($id);
    }

    /**
     * Actualizar un rol existente
     * 
     * @param int $id ID del rol a actualizar
     * @param array $data Datos del rol a actualizar
     * @return Role Entidad del rol actualizado
     * 
     * @throws RoleNotFoundException Si no se encuentra el rol a actualizar
     * @throws RoleSlugAlreadyExistsException Si el slug ya está en uso por otro rol
     */
    public function updateRole(int $id, array $data): Role
    {
        $data['id'] = $id;
        $dto = DTOUpdateRolesRequest::fromArray($data);
        return $this->updateRolesUseCase->execute($dto);
    }

    /**
     * Eliminar un rol
     * 
     * @param int $id ID del rol a eliminar
     * @param string $deletion_reason Motivo de eliminación
     * @return bool True si se eliminó correctamente
     * 
     * @throws RoleNotFoundException Si no se encuentra el rol a eliminar
     * @throws RoleDeletionException Si ocurre un error al eliminar el rol
     */
    public function deleteRole(int $id, string $deletion_reason): bool
    {
        $data = [
            'id' => $id,
            'deletion_reason' => $deletion_reason,
        ];
        $dto = DTODeleteRolesRequest::fromArray($data);
        return $this->deleteRolesUseCase->execute($dto);
    }
}
