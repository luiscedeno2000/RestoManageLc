<?php

namespace Src\Users\Domain\Repositories;

use Src\Users\Domain\Entities\User;

/**
 * Interfaz del repositorio de Usuarios
 * 
 * Define los métodos que debe implementar cualquier repositorio de usuarios
 */
interface UsersRepositoryInterface
{
    /**
     * Crear un nuevo usuario
     * 
     * @param array $data Datos del usuario a crear
     * @return User Entidad del usuario creado
     */
    public function create(array $data): User;

    /**
     * Obtener todos los usuarios
     * 
     * @return array<User> Array de entidades User
     */
    public function findAll(): array;

    /**
     * Obtener un usuario por ID
     * 
     * @param int $id ID del usuario
     * @return User|null Entidad del usuario o null si no existe
     */
    public function findById(int $id): ?User;

    /**
     * Obtener un usuario por email
     * 
     * @param string $email Email del usuario
     * @return User|null Entidad del usuario o null si no existe
     */
    public function findByEmail(string $email): ?User;

    /**
     * Actualizar un usuario
     * 
     * @param int $id ID del usuario a actualizar
     * @param array $data Datos a actualizar
     * @return User Entidad del usuario actualizado
     */
    public function update(int $id, array $data): User;

    /**
     * Eliminar un usuario
     * 
     * @param int $id ID del usuario a eliminar
     * @return bool True si se eliminó correctamente
     */
    public function delete(int $id): bool;

    /**
     * Sincronizar roles de un usuario
     * 
     * @param int $userId ID del usuario
     * @param array<int>|null $roleIds IDs de los roles a asignar
     * @return bool True si se sincronizaron correctamente
     */
    public function syncRoles(int $userId, ?array $roleIds): bool;
}
