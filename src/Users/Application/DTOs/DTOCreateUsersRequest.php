<?php

namespace Src\Users\Application\DTOs;

/**
 * DTO para crear un nuevo usuario
 * 
 * Data Transfer Object que contiene los datos necesarios para crear un usuario
 * 
 * @property string $name Nombre del usuario
 * @property string $email Email único del usuario
 * @property string $password Contraseña del usuario
 * @property bool $state Estado del usuario (activo/inactivo)
 * @property int|null $created_by ID del usuario que crea el usuario
 * @property array<int>|null $role_ids IDs de los roles a asignar
 */
class DTOCreateUsersRequest
{
    /**
     * Constructor del DTO
     * 
     * @param string $name Nombre del usuario
     * @param string $email Email único del usuario
     * @param string $password Contraseña del usuario
     * @param bool $state Estado del usuario
     * @param int|null $created_by ID del usuario que crea el usuario
     * @param array<int>|null $role_ids IDs de los roles a asignar
     */
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
        public readonly bool $state = true,
        public readonly ?int $created_by = null,
        public readonly ?array $role_ids = null,
    ) {}

    /**
     * Crear DTO desde un array
     * 
     * @param array $data Datos del formulario
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            password: $data['password'],
            state: $data['state'] ?? true,
            created_by: $data['created_by'] ?? null,
            role_ids: $data['role_ids'] ?? null,
        );
    }

    /**
     * Convertir DTO a array
     * 
     * @return array
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'state' => $this->state,
            'created_by' => $this->created_by,
            'role_ids' => $this->role_ids,
        ];
    }
}
