<?php

namespace Src\Users\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form Request para validar la eliminación de usuarios
 * 
 * Valida los datos recibidos antes de eliminar un usuario
 */
class DeleteUsersRequest extends FormRequest
{
    /**
     * Determinar si el usuario está autorizado para realizar esta solicitud
     * 
     * @return bool
     */
    public function authorize(): bool
    {
        return true; // TODO: Implementar política de autorización
    }

    /**
     * Obtener las reglas de validación para la solicitud
     * 
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // No se requieren reglas adicionales para eliminar
        ];
    }
}
