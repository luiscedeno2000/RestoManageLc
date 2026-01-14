<?php

namespace Src\Roles\Application\DTOs;

/**
 * DTO para eliminar un rol
 * 
 * Data Transfer Object que contiene los datos necesarios para eliminar un rol
 * 
 * @property int $id ID del rol a eliminar
 * @property string $deletion_reason Motivo de eliminación
 */
class DTODeleteRolesRequest
{
    /**
     * Constructor del DTO
     * 
     * @param int $id ID del rol a eliminar
     * @param string $deletion_reason Motivo de eliminación
     */
    public function __construct(
        public readonly int $id,
        public readonly string $deletion_reason,
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
            deletion_reason: $data['deletion_reason'],
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
            'deletion_reason' => $this->deletion_reason,
        ];
    }
}
