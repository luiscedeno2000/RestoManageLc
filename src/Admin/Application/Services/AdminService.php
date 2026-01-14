<?php

namespace Src\Admin\Application\Services;

use Src\Roles\Application\Services\RolesService;
use Src\Users\Application\Services\UsersService;

/**
 * Servicio de aplicación para Admin
 * 
 * Proporciona información del administrador actual y sus roles
 */
class AdminService
{
    /**
     * Constructor del servicio
     * 
     * @param UsersService $usersService Servicio de usuarios
     * @param RolesService $rolesService Servicio de roles
     */
    public function __construct(
        private readonly UsersService $usersService,
        private readonly RolesService $rolesService
    ) {}

    /**
     * Obtener información del administrador actual
     * 
     * @param int $userId ID del usuario autenticado
     * @return array Información del administrador con sus roles
     */
    public function getAdminInfo(int $userId): array
    {
        // Obtener el usuario con sus roles
        $user = $this->usersService->getUserById($userId);

        if (!$user) {
            return [
                'id' => $userId,
                'name' => '',
                'email' => '',
                'roles' => [],
            ];
        }

        // Convertir roles a array
        $rolesArray = [];
        if ($user->roles !== null && count($user->roles) > 0) {
            $rolesArray = array_map(function ($role) {
                return $role->toArray();
            }, $user->roles);
        }

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'state' => $user->state,
            'roles' => $rolesArray,
        ];
    }

    /**
     * Obtener todos los roles disponibles en el sistema
     * 
     * @return array Array de roles disponibles
     */
    public function getAllRoles(): array
    {
        $roles = $this->rolesService->getAllRoles();
        return array_map(function ($role) {
            return $role->toArray();
        }, $roles);
    }
}
