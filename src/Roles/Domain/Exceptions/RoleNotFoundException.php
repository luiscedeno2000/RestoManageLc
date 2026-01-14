<?php

namespace Src\Roles\Domain\Exceptions;

use Exception;

/**
 * Excepción lanzada cuando no se encuentra un rol
 * 
 * @package Src\Roles\Domain\Exceptions
 */
class RoleNotFoundException extends Exception
{
    /**
     * Constructor de la excepción
     * 
     * @param int $roleId ID del rol que no se encontró
     * @param string $message Mensaje personalizado (opcional)
     * @param int $code Código de error
     * @param \Throwable|null $previous Excepción previa
     */
    public function __construct(
        private readonly int $roleId,
        string $message = "",
        int $code = 404,
        ?\Throwable $previous = null
    ) {
        if (empty($message)) {
            $message = "No se encontró el rol con ID: {$roleId}";
        }
        
        parent::__construct($message, $code, $previous);
    }

    /**
     * Obtener el ID del rol que no se encontró
     * 
     * @return int ID del rol
     */
    public function getRoleId(): int
    {
        return $this->roleId;
    }
}
