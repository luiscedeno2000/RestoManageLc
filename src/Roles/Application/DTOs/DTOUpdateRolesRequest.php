<?php

namespace Src\Roles\Application\DTOs;

/**
 * DTO para actualizar un rol existente
 * 
 * Data Transfer Object que contiene los datos necesarios para actualizar un rol
 * 
 * @property int $id ID del rol a actualizar
 * @property string $name Nombre del rol
 * @property string $slug Slug único del rol
 * @property string|null $description Descripción del rol
 * @property bool $state Estado del rol (activo/inactivo)
 */
class DTOUpdateRolesRequest
{
    /**
     * Constructor del DTO
     * 
     * @param int $id ID del rol a actualizar
     * @param string $name Nombre del rol
     * @param string $slug Slug único del rol
     * @param string|null $description Descripción del rol
     * @param bool $state Estado del rol
     */
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $slug,
        public readonly ?string $description = null,
        public readonly bool $state = true,
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
            slug: $data['slug'],
            description: $data['description'] ?? null,
            state: $data['state'] ?? true,
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
        ];
    }
}
