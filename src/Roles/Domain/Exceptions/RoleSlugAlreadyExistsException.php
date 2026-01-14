<?php

namespace Src\Roles\Domain\Exceptions;

use Exception;

/**
 * Excepción lanzada cuando ya existe un rol con el slug proporcionado
 * 
 * @package Src\Roles\Domain\Exceptions
 */
class RoleSlugAlreadyExistsException extends Exception
{
    /**
     * Constructor de la excepción
     * 
     * @param string $slug Slug que ya existe
     * @param string $message Mensaje personalizado (opcional)
     * @param int $code Código de error
     * @param \Throwable|null $previous Excepción previa
     */
    public function __construct(
        private readonly string $slug,
        string $message = "",
        int $code = 409,
        ?\Throwable $previous = null
    ) {
        if (empty($message)) {
            $message = "Ya existe un rol con el slug: {$slug}";
        }
        
        parent::__construct($message, $code, $previous);
    }

    /**
     * Obtener el slug que ya existe
     * 
     * @return string Slug duplicado
     */
    public function getSlug(): string
    {
        return $this->slug;
    }
}
