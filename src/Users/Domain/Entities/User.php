<?php

namespace Src\Users\Domain\Entities;

use Src\Roles\Domain\Entities\Role;

/**
 * Entidad de Dominio User
 * 
 * Representa un usuario en el sistema siguiendo principios DDD
 * 
 * @property int $id
 * @property string $name
 * @property string $email
 * @property bool $state
 * @property int|null $created_by
 * @property array<Role>|null $roles Roles asignados al usuario
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class User
{
    /**
     * Constructor de la entidad User
     * 
     * @param int|null $id ID del usuario
     * @param string $name Nombre del usuario
     * @param string $email Email del usuario
     * @param bool $state Estado del usuario (activo/inactivo)
     * @param int|null $created_by ID del usuario que creó este usuario
     * @param array<Role>|null $roles Roles asignados al usuario
     * @param string|null $created_at Fecha de creación
     * @param string|null $updated_at Fecha de actualización
     */
    public function __construct(
        public ?int $id,
        public string $name,
        public string $email,
        public bool $state,
        public ?int $created_by = null,
        public ?array $roles = null,
        public ?string $created_at = null,
        public ?string $updated_at = null,
    ) {}

    /**
     * Crear una nueva instancia de User desde un array
     * 
     * @param array $data Datos del usuario
     * @return self
     */
    public static function fromArray(array $data): self
    {
        // Convertir roles si vienen como array de arrays
        $roles = null;
        if (isset($data['roles']) && is_array($data['roles'])) {
            $roles = array_map(function ($roleData) {
                if ($roleData instanceof Role) {
                    return $roleData;
                }
                return Role::fromArray((array) $roleData);
            }, $data['roles']);
        }

        return new self(
            id: $data['id'] ?? null,
            name: $data['name'],
            email: $data['email'],
            state: $data['state'] ?? true,
            created_by: $data['created_by'] ?? null,
            roles: $roles,
            created_at: $data['created_at'] ?? null,
            updated_at: $data['updated_at'] ?? null,
        );
    }

    /**
     * Convertir la entidad a array
     * 
     * @return array
     */
    public function toArray(): array
    {
        $rolesArray = null;
        if ($this->roles !== null) {
            $rolesArray = array_map(function ($role) {
                return $role instanceof Role ? $role->toArray() : $role;
            }, $this->roles);
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'state' => $this->state,
            'created_by' => $this->created_by,
            'roles' => $rolesArray,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
