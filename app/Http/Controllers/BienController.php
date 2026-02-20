<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreBienRequest;
use App\Http\Requests\UpdateBienRequest;
use App\Models\Area;
use App\Models\Bien;
use App\Models\CategoriaBien;
use App\Models\Estado;
use App\Imports\BienesImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BienController extends Controller
{
    /**
     * Muestra el listado de bienes con búsqueda y filtros.
     */
    public function index(Request $request): View
    {
        $query = Bien::query()->with('categoria');

        // Búsqueda por texto
        // Comparación por área (ubicación) - se busca en la relación
        if ($request->filled('buscar')) {
            $buscar = $request->input('buscar');
            $query->where(function ($q) use ($buscar) {
                $q->where('equipo', 'like', "%{$buscar}%")
                    ->orWhere('numero_bien', 'like', "%{$buscar}%")
                    ->orWhere('marca', 'like', "%{$buscar}%")
                    ->orWhere('modelo', 'like', "%{$buscar}%")
                    ->orWhere('serial', 'like', "%{$buscar}%")
                    ->orWhereHas('area', function ($qArea) use ($buscar) {
                        $qArea->where('nombre', 'like', "%{$buscar}%");
                    });
            });
        }

        // Filtro por estado
        if ($request->filled('estado_id')) {
            $query->where('estado_id', $request->input('estado_id'));
        }

        // Filtro por categoría
        if ($request->filled('categoria_bien_id')) {
            $query->where('categoria_bien_id', $request->input('categoria_bien_id'));
        }

        // Filtro por área
        if ($request->filled('area_id')) {
            $query->where('area_id', $request->input('area_id'));
        }

        $bienes = $query->with(['estado', 'area'])->latest()->paginate(10)->withQueryString();
        $estados = Estado::orderBy('nombre')->get();
        $categorias = CategoriaBien::orderBy('nombre')->get();
        $areas = Area::orderBy('nombre')->get();

        return view('bienes.index', compact('bienes', 'estados', 'categorias', 'areas'));
    }

    /**
     * Mostrar el formulario para crear un nuevo recurso.
     */
    public function create(): View
    {
        $estados = Estado::orderBy('nombre')->get();
        $categorias = CategoriaBien::orderBy('nombre')->get();
        $areas = Area::orderBy('nombre')->get();

        // Asegurar que exista la categoría "PENDIENTE POR CATEGORIA"
        $categoriaPendiente = CategoriaBien::firstOrCreate(
            ['nombre' => 'PENDIENTE POR CATEGORIA'],
            ['descripcion' => 'Categoría por defecto para bienes sin clasificar']
        );

        return view('bienes.create', compact('estados', 'categorias', 'areas', 'categoriaPendiente'));
    }

    /**
     * Almacenar un recurso recién creado en el almacenamiento.
     */
    public function store(StoreBienRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if (isset($data['numero_bien']) && strtoupper(trim($data['numero_bien'])) === 'S/N') {
            $data['numero_bien'] = \App\Models\Bien::generarNumeroSN();
        }

        Bien::create([
            ...$data,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('bienes.index')
            ->with('success', 'Bien creado exitosamente.');
    }

    /**
     * Muestra el detalle de un bien específico.
     */
    public function show(Bien $bien): View
    {
        $bien->load(['user', 'categoria', 'area', 'estado']);
        return view('bienes.show', compact('bien'));
    }

    /**
     * Muestra el formulario para editar el recurso especificado.
     */
    public function edit(Bien $bien): View
    {
        $estados = Estado::orderBy('nombre')->get();
        $categorias = CategoriaBien::orderBy('nombre')->get();
        $areas = Area::orderBy('nombre')->get();

        return view('bienes.edit', compact('bien', 'estados', 'categorias', 'areas'));
    }

    /**
     * Actualiza el recurso especificado en el almacenamiento.
     */
    public function update(UpdateBienRequest $request, Bien $bien): RedirectResponse
    {
        $data = $request->validated();

        // Si el usuario envía "S/N", manejamos la lógica para conservar el código interno o generar uno nuevo
        if (isset($data['numero_bien']) && strtoupper(trim($data['numero_bien'])) === 'S/N') {
            if (\Illuminate\Support\Str::startsWith($bien->numero_bien, 'S/N-')) {
                // Si ya era un S/N, mantenemos el código interno original
                unset($data['numero_bien']);
            } else {
                // Si se está cambiando de un número real a S/N, generamos un nuevo código interno único
                $data['numero_bien'] = \App\Models\Bien::generarNumeroSN();
            }
        }

        $bien->update($data);

        return redirect()->route('bienes.index')
            ->with('success', 'Bien actualizado exitosamente.');
    }

    /**
     * Muestra una vista previa de los datos a importar.
     */
    public function previewImport(Request $request): View|RedirectResponse
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', '1200');

        if ($request->isMethod('get') && !session()->has('error')) {
            return redirect()->route('bienes.index')->with('info', 'Por favor, suba el archivo nuevamente para procesar la importación.');
        }

        $request->validate([
            'archivo' => ['required', 'file', 'mimes:ods,xls,xlsx'],
        ]);

        try {

            $file = $request->file('archivo');
            // Usamos una instancia específica para el preview que no cree registros
            // Limitamos a 300 para asegurar que el usuario pueda ver los "al menos 200" que pidió
            $maxPreviewRows = 300;
            $importInstance = new BienesImport(true, $maxPreviewRows);
            $data = Excel::toArray($importInstance, $file)[0];

            $previewData = [];
            $count = 0;
            $processedCount = 0;

            foreach ($data as $row) {
                // Filtrar filas vacías o que solo contienen el número de índice (menos de 2 campos con datos)
                $filteredRow = array_filter($row, fn($value) => !empty(trim((string)$value)));
                if (count($filteredRow) < 2) continue;

                $processedCount++;
                if ($count < $maxPreviewRows) {
                    $previewData[] = $importInstance->processRow($row);
                    $count++;
                }
            }

            $totalRows = $processedCount;
            $isTruncated = $totalRows > $maxPreviewRows;

            return view('bienes.import-preview', compact('previewData', 'totalRows', 'isTruncated', 'maxPreviewRows'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al procesar el archivo: ' . $e->getMessage());
        }
    }

    /**
     * Procesa la importación final de los datos.
     */
    public function import(Request $request): RedirectResponse
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', '1200');

        $bienesJson = $request->input('bienes_json');

        \Illuminate\Support\Facades\Log::info('Iniciando importación final', [
            'json_size' => $bienesJson ? strlen((string)$bienesJson) : 0,
            'is_empty' => empty($bienesJson)
        ]);

        if (empty($bienesJson)) {
            return redirect()->route('bienes.index')->with('error', 'No se recibieron datos para importar o no has seleccionado ningún bien.');
        }

        $data = json_decode($bienesJson, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            \Illuminate\Support\Facades\Log::error('Error decodificando JSON de importación', [
                'error' => json_last_error_msg()
            ]);
            return redirect()->route('bienes.index')->with('error', 'Error en el formato de datos: ' . json_last_error_msg());
        }

        if (!is_array($data) || empty($data)) {
            \Illuminate\Support\Facades\Log::warning('Datos de importación vacíos o no válidos');
            return redirect()->route('bienes.index')->with('error', 'Error al procesar los datos de importación.');
        }

        \Illuminate\Support\Facades\Log::info('Procesando ' . count($data) . ' registros');

        $snGeneradosTemporales = [];

        DB::beginTransaction();
        try {
            foreach ($data as $bienData) {
                // Crear Categoría si no existe o asignar "PENDIENTE POR CATEGORIA"
                if (empty($bienData['categoria_bien_id'])) {
                    $nombreCategoria = !empty($bienData['categoria_nombre'])
                        ? \Illuminate\Support\Str::upper($bienData['categoria_nombre'])
                        : 'PENDIENTE POR CATEGORIA';

                    $categoria = CategoriaBien::firstOrCreate(
                        ['nombre' => $nombreCategoria],
                        ['descripcion' => null]
                    );
                    $bienData['categoria_bien_id'] = $categoria->id;
                }

                // Crear Área si no existe
                if (empty($bienData['area_id']) && !empty($bienData['area_nombre'])) {
                    $area = Area::firstOrCreate(
                        ['nombre' => \Illuminate\Support\Str::upper($bienData['area_nombre'])],
                        ['descripcion' => null]
                    );
                    $bienData['area_id'] = $area->id;
                }

                // Manejo de duplicados para S/N
                if (isset($bienData['numero_bien']) && strtoupper(trim($bienData['numero_bien'])) === 'S/N') {
                    $nuevoSn = \App\Models\Bien::generarNumeroSN();
                    // Evitar choques en la misma importación masiva
                    while (in_array($nuevoSn, $snGeneradosTemporales)) {
                        $partes = explode('-', $nuevoSn);
                        $nuevoSn = 'S/N-' . str_pad((string)((int)$partes[1] + 1), 3, '0', STR_PAD_LEFT);
                    }
                    $snGeneradosTemporales[] = $nuevoSn;
                    $bienData['numero_bien'] = $nuevoSn;
                }

                // Limpiar campos temporales
                unset($bienData['area_nombre'], $bienData['categoria_nombre'], $bienData['ya_existe']);

                Bien::create($bienData);
            }
            DB::commit();

            return redirect()->route('bienes.index')->with('success', 'Bienes importados exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Error en importación final: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('bienes.index')->with('error', 'Ha ocurrido un error durante la importación: ' . $e->getMessage());
        }
    }

    /**
     * Elimina el recurso especificado del almacenamiento (soft delete).
     */
    public function destroy(Bien $bien): RedirectResponse
    {
        $bien->forceDelete();

        return redirect()->route('bienes.index')
            ->with('success', 'Bien eliminado exitosamente.');
    }
}
