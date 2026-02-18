<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'numero_bien' => ['required', 'string', 'max:255'],
            'descripcion' => ['required', 'string', 'max:255'],
            'serial' => ['nullable', 'string', 'max:255'],
            'procedencia_id' => ['nullable', 'exists:departamentos,id', 'required_without:bien_id'],
            'destino_id' => ['nullable', 'exists:departamentos,id'],
            'area_id' => ['nullable', 'exists:areas,id', 'required_without:destino_id'],
            'fecha' => ['required', 'date'],
            'estatus_acta_id' => ['required', 'exists:estatus_actas,id'],
            'fecha_firma' => ['nullable', 'date'],
            'bien_id' => ['nullable', 'exists:bienes,id'],
            'bien_externo_id' => ['nullable', 'exists:bienes_externos,id'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'procedencia_id' => $this->procedencia_id === 'DTIC' ? null : $this->procedencia_id,
            'destino_id' => $this->destino_id === 'DTIC' ? null : $this->destino_id,
        ]);
    }

    public function messages(): array
    {
        return [
            'numero_bien.required' => 'El número de bien es obligatorio.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'procedencia_id.required' => 'La procedencia es obligatoria.',
            'destino_id.required' => 'El destino es obligatorio.',
            'fecha.required' => 'La fecha es obligatoria.',
            'estatus_acta_id.required' => 'El estatus del acta es obligatorio.',
            'estatus_acta_id.exists' => 'El estatus seleccionado no es válido.',
        ];
    }
}
