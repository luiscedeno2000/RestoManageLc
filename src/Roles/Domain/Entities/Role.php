<?php

namespace Src\Roles\Domain\Entities;

/**
 * Entidad de Dominio Role
 * 
 * Representa un rol en el sistema siguiendo principios DDD
 * 
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property bool $state
 * @property int|null $created_by
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Role
{
    public function __construct(
        public ?int $id,
        public string $name,
        public string $slug,
        public ?string $description,
        public bool $state,
        public ?int $created_by,
        public ?string $created_at = null,
        public ?string $updated_at = null,
    ) {}

    /**
     * Crear una nueva instancia de Role desde un array
     * 
     * @param array $data Datos del rol
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            name: $data['name'],
            slug: $data['slug'],
            description: $data['description'] ?? null,
            state: $data['state'] ?? true,
            created_by: $data['created_by'] ?? null,
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
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'state' => $this->state,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
