<?php

namespace Src\Users\Infrastructure\Repositories;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Src\Roles\Domain\Entities\Role;
use Src\Users\Domain\Entities\User;
use Src\Users\Domain\Repositories\UsersRepositoryInterface;

/**
 * Implementación del repositorio de Usuarios usando Eloquent
 * 
 * Implementa las consultas a la base de datos usando Eloquent ORM
 */
class EloquentUsersRepository implements UsersRepositoryInterface
{
    /**
     * Crear un nuevo usuario
     * 
     * @param array $data Datos del usuario a crear
     * @return User Entidad del usuario creado
     */
    public function create(array $data): User
    {
        $roleIds = $data['role_ids'] ?? null;
        unset($data['role_ids']);

        // Hashear la contraseña si está presente
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $userId = DB::table('users')->insertGetId([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'state' => $data['state'] ?? true,
            'created_by' => $data['created_by'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Sincronizar roles si se proporcionaron
        if ($roleIds !== null) {
            $this->syncRoles($userId, $roleIds);
        }

        return $this->findById($userId);
    }

    /**
     * Obtener todos los usuarios con sus roles
     * 
     * @return array<User> Array de entidades User
     */
    public function findAll(): array
    {
        $users = DB::table('users')
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();

        return array_map(function ($user) {
            return $this->mapUserWithRoles((array) $user);
        }, $users);
    }

    /**
     * Obtener un usuario por ID con sus roles
     * 
     * @param int $id ID del usuario
     * @return User|null Entidad del usuario o null si no existe
     */
    public function findById(int $id): ?User
    {
        $user = DB::table('users')->where('id', $id)->first();

        if (!$user) {
            return null;
        }

        return $this->mapUserWithRoles((array) $user);
    }

    /**
     * Obtener un usuario por email
     * 
     * @param string $email Email del usuario
     * @return User|null Entidad del usuario o null si no existe
     */
    public function findByEmail(string $email): ?User
    {
        $user = DB::table('users')->where('email', $email)->first();

        if (!$user) {
            return null;
        }

        return $this->mapUserWithRoles((array) $user);
    }

    /**
     * Actualizar un usuario
     * 
     * @param int $id ID del usuario a actualizar
     * @param array $data Datos a actualizar
     * @return User Entidad del usuario actualizado
     */
    public function update(int $id, array $data): User
    {
        $roleIds = $data['role_ids'] ?? null;
        unset($data['role_ids']);

        // Hashear la contraseña si está presente
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $data['updated_at'] = now();

        DB::table('users')
            ->where('id', $id)
            ->update($data);

        // Sincronizar roles si se proporcionaron
        if ($roleIds !== null) {
            $this->syncRoles($id, $roleIds);
        }

        return $this->findById($id);
    }

    /**
     * Eliminar un usuario
     * 
     * @param int $id ID del usuario a eliminar
     * @return bool True si se eliminó correctamente
     */
    public function delete(int $id): bool
    {
        // Eliminar relaciones de roles primero (cascade debería hacerlo, pero por si acaso)
        DB::table('role_user')->where('user_id', $id)->delete();
        
        // Eliminar el usuario
        return DB::table('users')->where('id', $id)->delete() > 0;
    }

    /**
     * Sincronizar roles de un usuario
     * 
     * @param int $userId ID del usuario
     * @param array<int>|null $roleIds IDs de los roles a asignar
     * @return bool True si se sincronizaron correctamente
     */
    public function syncRoles(int $userId, ?array $roleIds): bool
    {
        // Eliminar todos los roles actuales del usuario
        DB::table('role_user')->where('user_id', $userId)->delete();

        // Si se proporcionaron roles, insertarlos
        if ($roleIds !== null && count($roleIds) > 0) {
            $now = now();
            $insertData = array_map(function ($roleId) use ($userId, $now) {
                return [
                    'user_id' => $userId,
                    'role_id' => $roleId,
                    'state' => true,
                    'created_by' => Auth::id(),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }, $roleIds);

            DB::table('role_user')->insert($insertData);
        }

        return true;
    }

    /**
     * Mapear un usuario con sus roles desde la base de datos
     * 
     * @param array $userData Datos del usuario
     * @return User Entidad del usuario con roles
     */
    private function mapUserWithRoles(array $userData): User
    {
        // Obtener roles del usuario
        $rolesData = DB::table('role_user')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->where('role_user.user_id', $userData['id'])
            ->where('role_user.state', true)
            ->select('roles.*')
            ->get()
            ->toArray();

        $roles = null;
        if (count($rolesData) > 0) {
            $roles = array_map(function ($roleData) {
                $roleArray = (array) $roleData;
                $roleArray['state'] = (bool) $roleArray['state'];
                return Role::fromArray($roleArray);
            }, $rolesData);
        }

        $userData['state'] = (bool) $userData['state'];
        $userData['roles'] = $roles;

        return User::fromArray($userData);
    }
}
