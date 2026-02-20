<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDesincorporacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('editar desincorporaciones');
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'numero_bien' => ['required', 'string', 'max:255'],
            'descripcion' => ['required', 'string', 'max:255'],
            'serial' => ['nullable', 'string', 'max:255'],
            'procedencia_id' => ['required', 'exists:departamentos,id'],
            'destino_id' => ['required', 'exists:departamentos,id'],
            'fecha' => ['required', 'date'],
            'numero_informe' => ['required', 'string', 'max:255', 'regex:/^\d{2}-\d{2}-\d{2}$/'],
            'estatus_acta_id' => ['required', 'exists:estatus_actas,id'],
            'observaciones' => ['nullable', 'string'],
            'bien_id' => ['nullable', 'exists:bienes,id'],
            'bien_externo_id' => ['nullable', 'exists:bienes_externos,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'numero_bien.required' => 'El número de bien es obligatorio.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'procedencia_id.required' => 'La procedencia es obligatoria.',
            'fecha.required' => 'La fecha es obligatoria.',
            'numero_informe.required' => 'El número de informe es obligatorio.',
            'numero_informe.regex' => 'El formato del número de informe debe ser 00-00-00.',
            'estatus_acta_id.required' => 'El estatus del acta es obligatorio.',
            'estatus_acta_id.exists' => 'El estatus seleccionado no es válido.',
        ];
    }
}
