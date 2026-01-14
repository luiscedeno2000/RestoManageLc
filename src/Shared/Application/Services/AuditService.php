<?php

namespace Src\Shared\Application\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Servicio de auditoría para registrar logs de acciones del sistema
 * 
 * Proporciona métodos para registrar logs de auditoría con información contextual
 */
class AuditService
{
    /**
     * Registrar un log de auditoría
     * 
     * @param string $level Nivel del log (info, warning, error, etc.)
     * @param string $action Acción realizada
     * @param string $controller Nombre del controlador
     * @param string $useCase Nombre del caso de uso
     * @param string $function Nombre de la función
     * @param array $context Contexto adicional (datos del request, errores, etc.)
     * @return void
     */
    public function log(
        string $level,
        string $action,
        string $controller,
        string $useCase,
        string $function,
        array $context = []
    ): void {
        $userId = Auth::id();
        $userEmail = Auth::user()?->email ?? 'Guest';

        $logData = [
            'action' => $action,
            'controller' => $controller,
            'use_case' => $useCase,
            'function' => $function,
            'user_id' => $userId,
            'user_email' => $userEmail,
            'timestamp' => now()->toIso8601String(),
            'context' => $context,
        ];

        $message = sprintf(
            '[AUDIT] %s | Controller: %s | UseCase: %s | Function: %s | UserId: %s | UserEmail: %s',
            $action,
            $controller,
            $useCase,
            $function,
            $userId ?? 'null',
            $userEmail
        );

        Log::channel('single')->{$level}($message, $logData);
    }

    /**
     * Registrar log de acción exitosa
     * 
     * @param string $controller Nombre del controlador
     * @param string $useCase Nombre del caso de uso
     * @param string $function Nombre de la función
     * @param array $context Contexto adicional
     * @return void
     */
    public function logSuccess(
        string $controller,
        string $useCase,
        string $function,
        array $context = []
    ): void {
        $this->log('info', 'SUCCESS', $controller, $useCase, $function, $context);
    }

    /**
     * Registrar log de error
     * 
     * @param string $controller Nombre del controlador
     * @param string $useCase Nombre del caso de uso
     * @param string $function Nombre de la función
     * @param \Throwable $exception Excepción capturada
     * @param array $context Contexto adicional
     * @return void
     */
    public function logError(
        string $controller,
        string $useCase,
        string $function,
        \Throwable $exception,
        array $context = []
    ): void {
        $errorContext = array_merge($context, [
            'error_message' => $exception->getMessage(),
            'error_code' => $exception->getCode(),
            'error_file' => $exception->getFile(),
            'error_line' => $exception->getLine(),
            'error_trace' => $exception->getTraceAsString(),
        ]);

        $this->log('error', 'ERROR', $controller, $useCase, $function, $errorContext);
    }

    /**
     * Registrar log de acción fallida (validación, negocio, etc.)
     * 
     * @param string $controller Nombre del controlador
     * @param string $useCase Nombre del caso de uso
     * @param string $function Nombre de la función
     * @param string $reason Motivo del fallo
     * @param array $context Contexto adicional
     * @return void
     */
    public function logFailure(
        string $controller,
        string $useCase,
        string $function,
        string $reason,
        array $context = []
    ): void {
        $failureContext = array_merge($context, [
            'failure_reason' => $reason,
        ]);

        $this->log('warning', 'FAILURE', $controller, $useCase, $function, $failureContext);
    }
}
