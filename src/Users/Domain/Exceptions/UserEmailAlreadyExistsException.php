<?php

namespace Src\Users\Domain\Exceptions;

use Exception;

/**
 * Excepción lanzada cuando ya existe un usuario con el email proporcionado
 * 
 * @package Src\Users\Domain\Exceptions
 */
class UserEmailAlreadyExistsException extends Exception
{
    /**
     * Constructor de la excepción
     * 
     * @param string $email Email que ya existe
     * @param string $message Mensaje personalizado (opcional)
     * @param int $code Código de error
     * @param \Throwable|null $previous Excepción previa
     */
    public function __construct(
        private readonly string $email,
        string $message = "",
        int $code = 409,
        ?\Throwable $previous = null
    ) {
        if (empty($message)) {
            $message = "Ya existe un usuario con el email: {$email}";
        }
        
        parent::__construct($message, $code, $previous);
    }

    /**
     * Obtener el email que ya existe
     * 
     * @return string Email duplicado
     */
    public function getEmail(): string
    {
        return $this->email;
    }
}
