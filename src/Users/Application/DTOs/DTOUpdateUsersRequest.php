<?php

namespace Src\Users\Application\DTOs;

/**
 * DTO para actualizar un usuario existente
 * 
 * Data Transfer Object que contiene los datos necesarios para actualizar un usuario
 * 
 * @property int $id ID del usuario a actualizar
 * @property string $name Nombre del usuario
 * @property string $email Email único del usuario
 * @property string|null $password Contraseña del usuario (opcional)
 * @property bool $state Estado del usuario (activo/inactivo)
 * @property array<int>|null $role_ids IDs de los roles a asignar
 */
class DTOUpdateUsersRequest
{
    /**
     * Constructor del DTO
     * 
     * @param int $id ID del usuario a actualizar
     * @param string $name Nombre del usuario
     * @param string $email Email único del usuario
     * @param string|null $password Contraseña del usuario (opcional)
     * @param bool $state Estado del usuario
     * @param array<int>|null $role_ids IDs de los roles a asignar
     */
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly ?string $password = null,
        public readonly bool $state = true,
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
            id: $data['id'],
            name: $data['name'],
            email: $data['email'],
            password: $data['password'] ?? null,
            state: $data['state'] ?? true,
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
        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'state' => $this->state,
            'role_ids' => $this->role_ids,
        ];

        if ($this->password !== null) {
            $data['password'] = $this->password;
        }

        return $data;
    }
}
