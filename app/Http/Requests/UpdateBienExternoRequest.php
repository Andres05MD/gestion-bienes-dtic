<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBienExternoRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado a realizar esta solicitud.
     */
    public function authorize(): bool
    {
        return $this->user()->can('editar bienes externos');
    }

    /**
     * Reglas de validación para actualizar un bien externo.
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
            'numero_bien' => [
                'nullable', 
                'string', 
                'max:255', 
                Rule::unique('bienes_externos', 'numero_bien')->ignore($this->route('bien_externo')),
            ],
            'categoria_bien_id' => ['nullable', 'exists:categoria_bienes,id'],
            'estado_id' => ['required', 'exists:estados,id'],
            'observaciones' => ['nullable', 'string'],
            'departamento_id' => ['required', 'exists:departamentos,id'],
        ];
    }
}
