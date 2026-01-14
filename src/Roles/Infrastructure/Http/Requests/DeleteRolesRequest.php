<?php

namespace Src\Roles\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form Request para validar la eliminación de roles
 * 
 * Valida los datos recibidos antes de eliminar un rol
 */
class DeleteRolesRequest extends FormRequest
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
            'deletion_reason' => ['required', 'string', 'max:1000'],
        ];
    }

    /**
     * Obtener mensajes de error personalizados
     * 
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'deletion_reason.required' => 'El motivo de eliminación es obligatorio.',
            'deletion_reason.max' => 'El motivo de eliminación no puede exceder los 1000 caracteres.',
        ];
    }
}
