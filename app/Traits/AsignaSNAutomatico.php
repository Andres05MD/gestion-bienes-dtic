<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Bien;
use App\Models\BienExterno;

trait AsignaSNAutomatico
{
    /**
     * Intercepta la validación y reemplaza los "s/n" por números autonuméricos.
     */
    protected function prepareForValidation(): void
    {
        $bienes = $this->input('bienes');
        $numeroBienSingular = $this->input('numero_bien');
        $haySn = false;

        // Comprobación en múltiples elementos (arreglo 'bienes')
        if (is_array($bienes)) {
            foreach ($bienes as $bien) {
                if (isset($bien['numero_bien']) && trim(strtolower($bien['numero_bien'])) === 's/n') {
                    $haySn = true;
                    break;
                }
            }
        }

        // Comprobación en elemento unitario (un solo campo 'numero_bien')
        if (is_string($numeroBienSingular) && trim(strtolower($numeroBienSingular)) === 's/n') {
            $haySn = true;
        }

        if ($haySn) {
            $maxNumero = 0;
            $bienesSN = Bien::where('numero_bien', 'LIKE', 'S/N-%')->pluck('numero_bien');
            $externosSN = BienExterno::where('numero_bien', 'LIKE', 'S/N-%')->pluck('numero_bien');
            $todosLosSN = $bienesSN->merge($externosSN);

            foreach ($todosLosSN as $numero) {
                $partes = explode('-', $numero);
                if (isset($partes[1]) && is_numeric($partes[1])) {
                    $num = (int)$partes[1];
                    if ($num > $maxNumero) {
                        $maxNumero = $num;
                    }
                }
            }

            // Asignación Secuencial al Arreglo de Bienes
            if (is_array($bienes)) {
                foreach ($bienes as $index => $bien) {
                    if (isset($bien['numero_bien']) && trim(strtolower($bien['numero_bien'])) === 's/n') {
                        $maxNumero++;
                        $nuevoNumero = 'S/N-' . str_pad((string)$maxNumero, 3, '0', STR_PAD_LEFT);
                        $bienes[$index]['numero_bien'] = $nuevoNumero;
                    }
                }
                $this->merge(['bienes' => $bienes]);
            }

            // Asignación Secuencial a Singular
            if (is_string($numeroBienSingular) && trim(strtolower($numeroBienSingular)) === 's/n') {
                $maxNumero++;
                $nuevoNumero = 'S/N-' . str_pad((string)$maxNumero, 3, '0', STR_PAD_LEFT);
                $this->merge(['numero_bien' => $nuevoNumero]);
            }
        }
    }
}
