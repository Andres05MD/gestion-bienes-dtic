<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'procedencia_id' => ['required', 'exists:departamentos,id'],
            'destino_id' => ['required', 'exists:departamentos,id'],
            'area_id' => ['nullable', 'exists:areas,id'],
            'area_procedencia_id' => ['nullable', 'exists:areas,id'],
            'fecha' => ['required', 'date'],
            'numero_informe' => ['nullable', 'array'],
            'numero_informe.*' => ['nullable', 'string', 'max:255'],
            'estatus_acta_id' => ['required', 'exists:estatus_actas,id'],
            'observaciones' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'procedencia_id.required' => 'La procedencia es obligatoria.',
            'fecha.required' => 'La fecha es obligatoria.',
            'numero_informe.regex' => 'El formato del número de informe debe ser 00-00-00.',
            'estatus_acta_id.required' => 'El estatus del acta es obligatorio.',
            'estatus_acta_id.exists' => 'El estatus seleccionado no es válido.',
        ];
    }
}
