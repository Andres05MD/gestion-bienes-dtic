<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTransferenciaInternaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('editar transferencias');
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
            'estatus_acta_id' => ['required', 'exists:estatus_actas,id'],
            'fecha_firma' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'procedencia_id.required' => 'La procedencia es obligatoria.',
            'destino_id.required' => 'El destino es obligatorio.',
            'fecha.required' => 'La fecha es obligatoria.',
            'estatus_acta_id.required' => 'El estatus del acta es obligatorio.',
            'estatus_acta_id.exists' => 'El estatus seleccionado no es válido.',
        ];
    }

    /**
     * Acciones posteriores a las reglas básicas. (Prevención de IDOR)
     */
    public function after(): array
    {
        return [];
    }
}
