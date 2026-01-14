<?php

namespace Src\Users\Application\DTOs;

/**
 * DTO para eliminar un usuario
 * 
 * Data Transfer Object que contiene los datos necesarios para eliminar un usuario
 * 
 * @property int $id ID del usuario a eliminar
 */
class DTODeleteUsersRequest
{
    /**
     * Constructor del DTO
     * 
     * @param int $id ID del usuario a eliminar
     */
    public function __construct(
        public readonly int $id,
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
            'id' => $this->id,
        ];
    }
}
