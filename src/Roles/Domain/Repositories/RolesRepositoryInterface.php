<?php

namespace Src\Roles\Domain\Repositories;

use Src\Roles\Domain\Entities\Role;

/**
 * Interfaz del repositorio de Roles
 * 
 * Define los contratos para las operaciones de persistencia de roles
 * siguiendo principios DDD
 */
interface RolesRepositoryInterface
{
    /**
     * Crear un nuevo rol
     * 
     * @param array $data Datos del rol a crear
     * @return Role Entidad del rol creado
     */
    public function create(array $data): Role;

    /**
     * Obtener todos los roles
     * 
     * @return array<Role> Array de entidades Role
     */
    public function findAll(): array;

    /**
     * Obtener un rol por ID
     * 
     * @param int $id ID del rol
     * @return Role|null Entidad del rol o null si no existe
     */
    public function findById(int $id): ?Role;

    /**
     * Obtener un rol por slug
     * 
     * @param string $slug Slug del rol
     * @return Role|null Entidad del rol o null si no existe
     */
    public function findBySlug(string $slug): ?Role;

    /**
     * Actualizar un rol
     * 
     * @param int $id ID del rol a actualizar
     * @param array $data Datos a actualizar
     * @return Role Entidad del rol actualizado
     */
    public function update(int $id, array $data): Role;

    /**
     * Verificar si un rol está en uso por usuarios
     * 
     * @param int $id ID del rol
     * @return int Cantidad de usuarios que tienen este rol asignado
     */
    public function countUsersWithRole(int $id): int;

    /**
     * Eliminar un rol
     * 
     * @param int $id ID del rol a eliminar
     * @param string $deletion_reason Motivo de eliminación
     * @return bool True si se eliminó correctamente
     */
    public function delete(int $id, string $deletion_reason): bool;
}
