<?php

namespace Src\Roles\Domain\Exceptions;

use Exception;

/**
 * Excepción lanzada cuando ocurre un error al eliminar un rol
 * 
 * @package Src\Roles\Domain\Exceptions
 */
class RoleDeletionException extends Exception
{
    /**
     * Constructor de la excepción
     * 
     * @param int $roleId ID del rol que no se pudo eliminar
     * @param string $message Mensaje personalizado (opcional)
     * @param int $code Código de error
     * @param \Throwable|null $previous Excepción previa
     */
    public function __construct(
        private readonly int $roleId,
        string $message = "",
        int $code = 500,
        ?\Throwable $previous = null
    ) {
        if (empty($message)) {
            $message = "No se pudo eliminar el rol con ID: {$roleId}";
        }
        
        parent::__construct($message, $code, $previous);
    }

    /**
     * Obtener el ID del rol que no se pudo eliminar
     * 
     * @return int ID del rol
     */
    public function getRoleId(): int
    {
        return $this->roleId;
    }
}
