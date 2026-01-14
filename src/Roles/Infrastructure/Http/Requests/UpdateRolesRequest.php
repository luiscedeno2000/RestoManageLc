<?php

namespace Src\Roles\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Form Request para validar la actualización de roles
 * 
 * Valida los datos recibidos antes de actualizar un rol existente
 */
class UpdateRolesRequest extends FormRequest
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
        $roleId = $this->route('role'); // Obtener el ID del rol desde la ruta

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('roles', 'name')->ignore($roleId)],
            'slug' => ['required', 'string', 'max:255', Rule::unique('roles', 'slug')->ignore($roleId), 'regex:/^[a-z0-9-]+$/'],
            'description' => ['nullable', 'string', 'max:1000'],
            'state' => ['boolean'],
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
            'name.required' => 'El nombre del rol es obligatorio.',
            'name.unique' => 'Ya existe un rol con este nombre.',
            'slug.required' => 'El slug del rol es obligatorio.',
            'slug.unique' => 'Ya existe un rol con este slug.',
            'slug.regex' => 'El slug solo puede contener letras minúsculas, números y guiones.',
            'description.max' => 'La descripción no puede exceder los 1000 caracteres.',
        ];
    }

    /**
     * Preparar los datos para la validación
     * 
     * @return void
     */
    protected function prepareForValidation(): void
    {
        // Generar slug automáticamente si no se proporciona
        if (!$this->has('slug') && $this->has('name')) {
            $this->merge([
                'slug' => \Illuminate\Support\Str::slug($this->name),
            ]);
        }
    }
}
