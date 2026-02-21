<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDistribucionDireccionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('crear distribuciones');
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'numero_bien' => ['required', 'string', 'max:255'],
            'descripcion' => ['required', 'string', 'max:255'],
            'marca' => ['nullable', 'string', 'max:255'],
            'serial' => ['nullable', 'string', 'max:255'],
            'modelo' => ['nullable', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:255'],
            'categoria_bien_id' => ['required', 'exists:categoria_bienes,id'],
            'estado_id' => ['required', 'exists:estados,id'],
            'procedencia_id' => ['required', 'exists:departamentos,id'],
            'area_id' => ['nullable', 'exists:areas,id'],
            'fecha' => ['required', 'date'],
        ];
    }



    public function messages(): array
    {
        return [
            'numero_bien.required' => 'El número de bien es obligatorio.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'procedencia_id.required' => 'La procedencia es obligatoria.',
            'fecha.required' => 'La fecha es obligatoria.',
        ];
    }
}
