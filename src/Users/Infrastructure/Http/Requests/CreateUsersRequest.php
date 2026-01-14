<?php

namespace Src\Users\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form Request para validar la creación de usuarios
 * 
 * Valida los datos recibidos antes de crear un nuevo usuario
 */
class CreateUsersRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'state' => ['boolean'],
            'role_ids' => ['nullable', 'array'],
            'role_ids.*' => ['integer', 'exists:roles,id'],
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
            'name.required' => 'El nombre del usuario es obligatorio.',
            'email.required' => 'El email del usuario es obligatorio.',
            'email.email' => 'El email debe ser una dirección de correo válida.',
            'email.unique' => 'Ya existe un usuario con este email.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'role_ids.array' => 'Los roles deben ser un array.',
            'role_ids.*.integer' => 'Cada rol debe ser un ID válido.',
            'role_ids.*.exists' => 'Uno o más roles seleccionados no existen.',
        ];
    }
}
