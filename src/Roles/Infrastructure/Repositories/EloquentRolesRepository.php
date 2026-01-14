<?php

namespace Src\Roles\Infrastructure\Repositories;

use Illuminate\Support\Facades\DB;
use Src\Roles\Domain\Entities\Role;
use Src\Roles\Domain\Repositories\RolesRepositoryInterface;

/**
 * Implementación del repositorio de Roles usando Eloquent
 * 
 * Implementa las consultas a la base de datos usando Eloquent ORM
 */
class EloquentRolesRepository implements RolesRepositoryInterface
{
    /**
     * Crear un nuevo rol
     * 
     * @param array $data Datos del rol a crear
     * @return Role Entidad del rol creado
     */
    public function create(array $data): Role
    {
        $role = DB::table('roles')->insertGetId([
            'name' => $data['name'],
            'slug' => $data['slug'],
            'description' => $data['description'] ?? null,
            'state' => $data['state'] ?? true,
            'created_by' => $data['created_by'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $this->findById($role);
    }

    /**
     * Obtener todos los roles
     * 
     * @return array<Role> Array de entidades Role
     */
    public function findAll(): array
    {
        $roles = DB::table('roles')
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();

        return array_map(function ($role) {
            $roleArray = (array) $role;
            $roleArray['state'] = (bool) $roleArray['state'];
            return Role::fromArray($roleArray);
        }, $roles);
    }

    /**
     * Obtener un rol por ID
     * 
     * @param int $id ID del rol
     * @return Role|null Entidad del rol o null si no existe
     */
    public function findById(int $id): ?Role
    {
        $role = DB::table('roles')->where('id', $id)->first();

        if (!$role) {
            return null;
        }

        $roleArray = (array) $role;
        $roleArray['state'] = (bool) $roleArray['state'];
        
        return Role::fromArray($roleArray);
    }

    /**
     * Obtener un rol por slug
     * 
     * @param string $slug Slug del rol
     * @return Role|null Entidad del rol o null si no existe
     */
    public function findBySlug(string $slug): ?Role
    {
        $role = DB::table('roles')->where('slug', $slug)->first();

        if (!$role) {
            return null;
        }

        $roleArray = (array) $role;
        $roleArray['state'] = (bool) $roleArray['state'];
        
        return Role::fromArray($roleArray);
    }

    /**
     * Actualizar un rol
     * 
     * @param int $id ID del rol a actualizar
     * @param array $data Datos a actualizar
     * @return Role Entidad del rol actualizado
     */
    public function update(int $id, array $data): Role
    {
        $data['updated_at'] = now();

        DB::table('roles')
            ->where('id', $id)
            ->update($data);

        return $this->findById($id);
    }

    /**
     * Verificar si un rol está en uso por usuarios
     * 
     * @param int $id ID del rol
     * @return int Cantidad de usuarios que tienen este rol asignado
     */
    public function countUsersWithRole(int $id): int
    {
        return DB::table('role_user')
            ->where('role_id', $id)
            ->where('state', true)
            ->count();
    }

    /**
     * Eliminar un rol
     * 
     * @param int $id ID del rol a eliminar
     * @param string $deletion_reason Motivo de eliminación
     * @return bool True si se eliminó correctamente
     */
    public function delete(int $id, string $deletion_reason): bool
    {
        // Primero actualizar el motivo de eliminación
        DB::table('roles')
            ->where('id', $id)
            ->update(['deletion_reason' => $deletion_reason]);

        // Luego eliminar el rol
        return DB::table('roles')->where('id', $id)->delete() > 0;
    }
}
