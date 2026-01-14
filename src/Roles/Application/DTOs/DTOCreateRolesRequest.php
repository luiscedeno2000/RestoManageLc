<?php

namespace Src\Roles\Application\DTOs;

/**
 * DTO para crear un nuevo rol
 * 
 * Data Transfer Object que contiene los datos necesarios para crear un rol
 * 
 * @property string $name Nombre del rol
 * @property string $slug Slug único del rol
 * @property string|null $description Descripción del rol
 * @property bool $state Estado del rol (activo/inactivo)
 * @property int|null $created_by ID del usuario que crea el rol
 */
class DTOCreateRolesRequest
{
    /**
     * Constructor del DTO
     * 
     * @param string $name Nombre del rol
     * @param string $slug Slug único del rol
     * @param string|null $description Descripción del rol
     * @param bool $state Estado del rol
     * @param int|null $created_by ID del usuario que crea el rol
     */
    public function __construct(
        public readonly string $name,
        public readonly string $slug,
        public readonly ?string $description = null,
        public readonly bool $state = true,
        public readonly ?int $created_by = null,
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
            slug: $data['slug'],
            description: $data['description'] ?? null,
            state: $data['state'] ?? true,
            created_by: $data['created_by'] ?? null,
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
            'slug' => $this->slug,
            'description' => $this->description,
            'state' => $this->state,
            'created_by' => $this->created_by,
        ];
    }
}
