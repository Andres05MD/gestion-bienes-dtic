<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMantenimientoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Usaremos el mismo permiso, ya que es una operación
        return $this->user()->can('crear transferencias');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'procedencia_id' => ['required', 'exists:departamentos,id'],
            'destino_id'     => ['required', 'exists:departamentos,id'],
            'area_id'        => ['nullable', 'exists:areas,id'],
            'area_procedencia_id' => ['nullable', 'exists:areas,id'], // Si se deucelve
            'fecha'          => ['required', 'date'],
            'estatus_acta_id' => ['required', 'exists:estatus_actas,id'],
            'fecha_firma'    => ['nullable', 'date'],
            'n_orden_acta'   => ['nullable', 'string', 'digits:4'],
            'fecha_acta'     => ['nullable', 'date'],

            // Validación para múltiples bienes
            'bienes'               => ['required', 'array', 'min:1'],
            'bienes.*.id'          => ['nullable'],
            'bienes.*.tipo'        => ['required', 'in:externo'],
            'bienes.*.numero_bien' => ['required', 'string', 'max:255'],
            'bienes.*.descripcion' => ['required', 'string', 'max:255'],
            'bienes.*.serial'      => ['nullable', 'string', 'max:255'],
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
            'bienes.required'            => 'Debe agregar al menos un bien a la lista de mantenimiento.',
            'bienes.min'                 => 'Debe agregar al menos un bien a la lista de mantenimiento.',
            'bienes.*.numero_bien.required' => 'El número de bien es obligatorio.',
            'bienes.*.descripcion.required' => 'La descripción es obligatoria.',
            'procedencia_id.required'    => 'La procedencia es obligatoria.',
            'procedencia_id.exists'      => 'La procedencia seleccionada no es válida.',
            'destino_id.required'        => 'El destino es obligatorio.',
            'destino_id.exists'          => 'El destino seleccionado no es válido.',
            'fecha.required'             => 'La fecha es obligatoria.',
            'estatus_acta_id.required'   => 'El estatus del acta es obligatorio.',
            'estatus_acta_id.exists'     => 'El estatus seleccionado no es válido.',
        ];
    }
}
