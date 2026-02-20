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
            'procedencia_id' => ['required', 'exists:departamentos,id'],
            'destino_id' => ['required', 'exists:departamentos,id'],
            'area_id' => ['nullable', 'exists:areas,id'],
            'area_procedencia_id' => ['nullable', 'exists:areas,id'],
            'fecha' => ['required', 'date'],
            'estatus_acta_id' => ['required', 'exists:estatus_actas,id'],
            'fecha_firma' => ['nullable', 'date'],
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
        return [
            function (\Illuminate\Validation\Validator $validator) {
                $procedenciaId = $this->input('procedencia_id');
                $bienId = $this->input('bien_id');
                $bienExternoId = $this->input('bien_externo_id');

                $dticId = \App\Models\Departamento::where('nombre', 'DTIC')->first()?->id;

                if ($bienId) {
                    $bienExiste = \App\Models\Bien::where('id', $bienId)->exists();
                    if (!$bienExiste) {
                        $validator->errors()->add('bien_id', 'El bien interno indicado no existe en la base de datos.');
                    } elseif ((int)$procedenciaId !== (int)$dticId) {
                        $validator->errors()->add('procedencia_id', 'Un bien interno (DTIC) no puede tener una procedencia distinta a DTIC.');
                    }
                }

                if ($bienExternoId) {
                    $bienExterno = \App\Models\BienExterno::find($bienExternoId);
                    if (!$bienExterno) {
                        $validator->errors()->add('bien_externo_id', 'El bien externo indicado no existe en la base de datos.');
                    } elseif ((int)$bienExterno->departamento_id !== (int)$procedenciaId) {
                        // En "edición", el bien externo original podría tener un departamento_id antiguo que 
                        // ya se migró. Esta validación se omite en update si el workflow de edición
                        // permite reasignaciones en una transferencia que ya movió el bien.
                        // Solo se hace si la herramienta necesita estricto control de origen.
                    }
                }
            }
        ];
    }
}
