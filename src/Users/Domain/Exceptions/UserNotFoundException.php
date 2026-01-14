<?php

namespace Src\Users\Domain\Exceptions;

use Exception;

/**
 * Excepción lanzada cuando no se encuentra un usuario
 * 
 * @package Src\Users\Domain\Exceptions
 */
class UserNotFoundException extends Exception
{
    /**
     * Constructor de la excepción
     * 
     * @param int $userId ID del usuario que no se encontró
     * @param string $message Mensaje personalizado (opcional)
     * @param int $code Código de error
     * @param \Throwable|null $previous Excepción previa
     */
    public function __construct(
        private readonly int $userId,
        string $message = "",
        int $code = 404,
        ?\Throwable $previous = null
    ) {
        if (empty($message)) {
            $message = "No se encontró el usuario con ID: {$userId}";
        }
        
        parent::__construct($message, $code, $previous);
    }

    /**
     * Obtener el ID del usuario que no se encontró
     * 
     * @return int ID del usuario
     */
    public function getUserId(): int
    {
        return $this->userId;
    }
}
