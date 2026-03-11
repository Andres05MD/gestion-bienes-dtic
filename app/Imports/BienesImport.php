<?php

namespace App\Imports;

use App\Models\Area;
use App\Models\Bien;
use App\Models\CategoriaBien;
use App\Models\Estado;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithLimit;

class BienesImport implements ToCollection, WithHeadingRow, WithLimit
{
    private $previewMode = false;
    private $limit = 0;
    private $maxSN = 0;

    public function __construct($previewMode = false, $limit = 0)
    {
        $this->previewMode = $previewMode;
        $this->limit = $limit;
        $this->initMaxSN();
    }
    
    private function initMaxSN(): void
    {
        $bienesSN = Bien::where('numero_bien', 'LIKE', 'S/N-%')->pluck('numero_bien');
        $externosSN = \App\Models\BienExterno::where('numero_bien', 'LIKE', 'S/N-%')->pluck('numero_bien');
        $todosLosSN = $bienesSN->merge($externosSN);

        foreach ($todosLosSN as $numero) {
            $partes = explode('-', $numero);
            if (isset($partes[1]) && is_numeric($partes[1])) {
                $num = (int)$partes[1];
                if ($num > $this->maxSN) {
                    $this->maxSN = $num;
                }
            }
        }
    }

    public function limit(): int
    {
        return $this->limit > 0 ? $this->limit : 10000; // Un límite razonable para evitar cuelgues accidentales
    }
    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $this->processRow($row);
        }
    }

    public function processRow($row)
    {
        // El slug de "N.º DE BIEN" puede variar según el motor de Laravel Excel
        $numeroBienRaw = trim((string) ($row['no_de_bien'] ?? $row['numero_de_bien'] ?? $row['n_de_bien'] ?? $row['no_bien'] ?? ''));
        $procesado = $this->parseNumeroBien($numeroBienRaw);

        $categoria = null;
        if ($procesado['categoria']) {
            if ($this->previewMode) {
                $categoria = CategoriaBien::where('nombre', Str::upper($procesado['categoria']))->first();
            } else {
                $categoria = CategoriaBien::firstOrCreate(
                    ['nombre' => Str::upper($procesado['categoria'])],
                    ['descripcion' => null]
                );
            }
        }

        // Procesar Ubicación (Área)
        $area = null;
        $ubicacionRaw = trim((string) ($row['ubicacion'] ?? ''));
        if (empty($ubicacionRaw)) {
            $ubicacionRaw = 'ALMACEN';
        }

        if ($this->previewMode) {
            $area = Area::where('nombre', Str::upper($ubicacionRaw))->first();
        } else {
            $area = Area::firstOrCreate(
                ['nombre' => Str::upper($ubicacionRaw)],
                ['descripcion' => null]
            );
        }

        // Procesar Estado
        $estatusRaw = trim((string) ($row['estatus'] ?? $row['estado'] ?? $row['status'] ?? ''));
        $estado = Estado::where('nombre', 'like', "%{$estatusRaw}%")->first();
        if (!$estado) {
            // Si no se encuentra, asignar el primero por defecto o dejar nulo (aunque es requerido en StoreBienRequest)
            $estado = Estado::first();
        }

        return [
            'equipo' => $row['equipo'] ?? 'SIN NOMBRE',
            'marca' => !empty($row['marca']) ? $row['marca'] : 'S/M',
            'modelo' => !empty($row['modelo']) ? $row['modelo'] : 'S/M',
            'serial' => !empty($row['serial']) ? $row['serial'] : 'S/N',
            'color' => $row['color'] ?? null,
            'numero_bien' => $procesado['numero'],
            'categoria_bien_id' => $categoria ? $categoria->id : null,
            'categoria_nombre' => $procesado['categoria'],
            'estado_id' => $estado ? $estado->id : null,
            'observaciones' => $row['observacion'] ?? $row['observaciones'] ?? null,
            'area_id' => $area ? $area->id : null,
            'area_nombre' => $ubicacionRaw,
            'user_id' => auth()->id(),
            'ya_existe' => ($procesado['numero'] !== 'S/N') 
                ? Bien::where('numero_bien', $procesado['numero'])->exists() 
                : false,
        ];
    }

    private function parseNumeroBien($raw)
    {
        $raw = trim((string)$raw);
        $upperRaw = Str::upper($raw);

        if (empty($raw) || $upperRaw === 'S/B' || $upperRaw === 'S/N') {
            $this->maxSN++;
            $nuevoNumero = 'S/N-' . str_pad((string)$this->maxSN, 3, '0', STR_PAD_LEFT);
            return ['categoria' => null, 'numero' => $nuevoNumero];
        }

        // Limpiar puntos y espacios al inicio/final
        $raw = trim($raw, '. ');
        $rawUpper = Str::upper($raw);

        $prefijos = [
            'BN' => 'BIEN NACIONAL',
            'BE' => 'BIEN ESTADAL',
            'BM' => 'BIEN MENOR',
            'UCLA' => 'BIEN UCLA',
            'C/I' => 'CONTROL INTERNO',
        ];

        foreach ($prefijos as $prefijo => $nombreCategoria) {
            // Buscamos el prefijo seguido de cualquier separador (coma, espacio, guion, punto) o directamente el número
            if (preg_match('#^' . preg_quote($prefijo) . '[\s,\.\-:]*(.*)$#i', $rawUpper, $matches)) {
                $numero = trim($matches[1]);
                // Eliminar cualquier caracter que no sea alfanumérico si es necesario, 
                // pero por ahora limpiamos puntos y otros separadores comunes
                $numero = preg_replace('/[\s\.\-:]+/', '', $numero); 
                return [
                    'categoria' => $nombreCategoria,
                    'numero' => !empty($numero) ? $numero : $this->obtenerNuevoSN()
                ];
            }
        }

        return ['categoria' => null, 'numero' => $rawUpper];
    }
    
    private function obtenerNuevoSN(): string
    {
        $this->maxSN++;
        return 'S/N-' . str_pad((string)$this->maxSN, 3, '0', STR_PAD_LEFT);
    }
}
