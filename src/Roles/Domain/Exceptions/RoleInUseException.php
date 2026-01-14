<?php

namespace Src\Roles\Domain\Exceptions;

use Exception;

/**
 * Excepción lanzada cuando se intenta eliminar un rol que está en uso por usuarios
 * 
 * @package Src\Roles\Domain\Exceptions
 */
class RoleInUseException extends Exception
{
    /**
     * Constructor de la excepción
     * 
     * @param int $roleId ID del rol que está en uso
     * @param int $usersCount Cantidad de usuarios que tienen este rol
     * @param string $message Mensaje personalizado (opcional)
     * @param int $code Código de error
     * @param \Throwable|null $previous Excepción previa
     */
    public function __construct(
        private readonly int $roleId,
        private readonly int $usersCount,
        string $message = "",
        int $code = 409,
        ?\Throwable $previous = null
    ) {
        if (empty($message)) {
            $message = "No se puede eliminar el rol con ID: {$roleId} porque está siendo utilizado por {$usersCount} usuario(s).";
        }
        
        parent::__construct($message, $code, $previous);
    }

    /**
     * Obtener el ID del rol que está en uso
     * 
     * @return int ID del rol
     */
    public function getRoleId(): int
    {
        return $this->roleId;
    }

    /**
     * Obtener la cantidad de usuarios que tienen este rol
     * 
     * @return int Cantidad de usuarios
     */
    public function getUsersCount(): int
    {
        return $this->usersCount;
    }
}
