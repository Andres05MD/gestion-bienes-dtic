<?php

declare(strict_types=1);

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBienRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado a realizar esta solicitud.
     */
    public function authorize(): bool
    {
        return $this->user()->can('crear bienes');
    }

    /**
     * Reglas de validación para crear un bien.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'equipo' => ['required', 'string', 'max:255'],
            'marca' => ['nullable', 'string', 'max:255'],
            'modelo' => ['nullable', 'string', 'max:255'],
            'serial' => ['nullable', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:255'],
            'numero_bien' => ['required', 'string', 'max:255', 'unique:bienes,numero_bien'],
            'categoria_bien_id' => ['required', 'exists:categoria_bienes,id'],
            'estado_id' => ['required', 'exists:estados,id'],
            'observaciones' => ['nullable', 'string'],
            'area_id' => ['required', 'exists:areas,id'],
        ];
    }

    /**
     * Mensajes de error personalizados en español.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'equipo.required' => 'El nombre del equipo es obligatorio.',
            'numero_bien.required' => 'El número de bien es obligatorio.',
            'numero_bien.unique' => 'Este número de bien ya está registrado.',
            'categoria_bien_id.required' => 'La categoría del bien es obligatoria.',
            'categoria_bien_id.exists' => 'La categoría seleccionada no es válida.',
            'estado_id.required' => 'El estado del bien es obligatorio.',
            'estado_id.exists' => 'El estado seleccionado no es válido.',
            'area_id.required' => 'El área es obligatoria.',
            'area_id.exists' => 'El área seleccionada no es válida.',
        ];
    }
}
