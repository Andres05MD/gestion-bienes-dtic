<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\EstatusActa;
use App\Http\Requests\StoreTransferenciaInternaRequest;
use App\Http\Requests\UpdateTransferenciaInternaRequest;
use App\Models\Bien;
use App\Models\BienExterno;
use App\Models\Departamento;
use App\Models\TransferenciaInterna;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Str;
use App\Services\BienMovimientoService;

class TransferenciaInternaController extends Controller
{
    public function __construct(private BienMovimientoService $movimientoService) {}

    /**
     * Muestra el listado de transferencias internas con búsqueda y filtros.
     */
    public function index(Request $request): View
    {
        $query = TransferenciaInterna::query()->with([
            'procedencia',
            'destino',
            'estatusActa',
            'bien',
            'bienExterno'
        ]);

        // Búsqueda por texto (aplicada a nivel registro)
        if ($request->filled('buscar')) {
            $buscar = $request->input('buscar');
            $query->where(function ($q) use ($buscar) {
                $q->where('numero_bien', 'like', "%{$buscar}%")
                    ->orWhere('descripcion', 'like', "%{$buscar}%")
                    ->orWhere('serial', 'like', "%{$buscar}%");
            });
        }

        if ($request->filled('estatus_acta_id')) {
            $query->where('estatus_acta_id', $request->input('estatus_acta_id'));
        }
        if ($request->filled('procedencia_id')) {
            $query->where('procedencia_id', $request->input('procedencia_id'));
        }
        if ($request->filled('destino_id')) {
            $query->where('destino_id', $request->input('destino_id'));
        }
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha', '<=', $request->fecha_hasta);
        }

        // Obtener la colección de actas (códigos únicos o IDs si son legacy) que coinciden con los filtros, paginadas
        // Esto soluciona la fragmentación, garantizando que si el código de acta está en la página, vengan todos sus bienes

        // Creamos una subquery para extraer los identificadores de agrupación únicos
        $agrupadores = $query->clone()
            ->selectRaw('COALESCE(codigo_acta, CONCAT("legacy_", id)) as group_key, MAX(id) as max_id')
            ->groupBy('group_key')
            ->orderByDesc('max_id');

        // Paginamos sobre la subconsulta de agrupadores
        $gruposPaginados = \Illuminate\Support\Facades\DB::query()->select('group_key')
            ->fromSub($agrupadores, 'agrupados')
            ->paginate(10)
            ->withQueryString();

        // Extraemos las claves actuales de esta página plana
        $clavesPagina = collect($gruposPaginados->items())->pluck('group_key');

        // Consultamos la tabla real, extrayendo *todos* los bienes de esos códigos de acta / legacy keys
        $transferenciasFinales = TransferenciaInterna::with([
            'procedencia',
            'destino',
            'estatusActa',
            'bien',
            'bienExterno'
        ])
            ->whereIn(\Illuminate\Support\Facades\DB::raw('COALESCE(codigo_acta, CONCAT("legacy_", id))'), $clavesPagina)
            ->latest('id')
            ->get();

        // Agrupamos en memoria
        $transferenciasAgrupadas = $transferenciasFinales->groupBy(function ($item) {
            return $item->codigo_acta ?? 'legacy_' . $item->id;
        });

        $departamentos = Departamento::orderBy('nombre')->get();
        $estatuses = EstatusActa::all();

        return view('transferencias-internas.index', [
            'transferenciasPaginadas' => $gruposPaginados,
            'transferenciasAgrupadas' => $transferenciasAgrupadas,
            'departamentos' => $departamentos,
            'estatuses' => $estatuses,
        ]);
    }

    /**
     * Muestra el formulario para crear una nueva transferencia.
     */
    public function create(): View
    {
        $departamentos = Departamento::orderBy('nombre')->get();
        $estatuses = EstatusActa::all();
        $areas = \App\Models\Area::orderBy('nombre')->get();
        $dticId = Departamento::where('nombre', 'DTIC')->first()?->id;

        return view('transferencias-internas.create', compact('departamentos', 'estatuses', 'areas', 'dticId'));
    }

    /**
     * Almacena una nueva transferencia interna.
     */
    public function store(StoreTransferenciaInternaRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        \Illuminate\Support\Facades\DB::transaction(function () use ($validated, $request) {
            $codigoActa = Str::uuid()->toString();

            $commonData = [
                'codigo_acta' => $codigoActa,
                'procedencia_id' => $validated['procedencia_id'],
                'destino_id' => $validated['destino_id'],
                'fecha' => $validated['fecha'],
                'estatus_acta_id' => $validated['estatus_acta_id'],
                'fecha_firma' => $validated['fecha_firma'] ?? null,
                'user_id' => auth()->id(),
            ];

            foreach ($validated['bienes'] as $bienData) {
                $transferencia = TransferenciaInterna::create([
                    ...$commonData,
                    'numero_bien' => $bienData['numero_bien'],
                    'descripcion' => $bienData['descripcion'],
                    'serial' => $bienData['serial'] ?? null,
                    'bien_id' => $bienData['tipo'] === 'dtic' ? $bienData['id'] : null,
                    'bien_externo_id' => $bienData['tipo'] === 'externo' ? $bienData['id'] : null,
                ]);

                // Actualizar ubicación del bien transferido usando el servicio
                $this->movimientoService->actualizarUbicacionBien($transferencia, $request->input('area_id'));
            }
        });

        return redirect()->route('transferencias-internas.index')
            ->with('success', 'Transferencias registradas exitosamente.');
    }

    /**
     * Muestra el detalle de una transferencia interna.
     */
    public function show(TransferenciaInterna $transferencias_interna): View
    {
        $transferencias_interna->load(['procedencia', 'destino', 'bien', 'bienExterno', 'user', 'estatusActa']);
        $bienesGrupo = $transferencias_interna->codigo_acta
            ? TransferenciaInterna::where('codigo_acta', $transferencias_interna->codigo_acta)->get()
            : collect([$transferencias_interna]);

        return view('transferencias-internas.show', [
            'transferencia' => $transferencias_interna,
            'bienesGrupo' => $bienesGrupo
        ]);
    }

    /**
     * Muestra el formulario para editar una transferencia interna.
     */
    public function edit(TransferenciaInterna $transferencias_interna): View
    {
        $departamentos = Departamento::orderBy('nombre')->get();
        $estatuses = EstatusActa::all();
        $areas = \App\Models\Area::orderBy('nombre')->get();
        $dticId = Departamento::where('nombre', 'DTIC')->first()?->id;

        $bienesGrupo = $transferencias_interna->codigo_acta
            ? TransferenciaInterna::where('codigo_acta', $transferencias_interna->codigo_acta)->get()
            : collect([$transferencias_interna]);

        return view('transferencias-internas.edit', [
            'transferencia' => $transferencias_interna,
            'bienesGrupo' => $bienesGrupo,
            'departamentos' => $departamentos,
            'estatuses' => $estatuses,
            'areas' => $areas,
            'dticId' => $dticId,
        ]);
    }

    /**
     * Actualiza una transferencia interna existente.
     */
    public function update(UpdateTransferenciaInternaRequest $request, TransferenciaInterna $transferencias_interna): RedirectResponse
    {
        $validated = $request->validated();

        \Illuminate\Support\Facades\DB::transaction(function () use ($validated, $request, $transferencias_interna) {
            $commonData = [
                'procedencia_id' => $validated['procedencia_id'],
                'destino_id' => $validated['destino_id'],
                'fecha' => $validated['fecha'],
                'fecha_firma' => $validated['fecha_firma'] ?? null,
                'estatus_acta_id' => $validated['estatus_acta_id'],
            ];

            if ($transferencias_interna->codigo_acta) {
                TransferenciaInterna::where('codigo_acta', $transferencias_interna->codigo_acta)->update($commonData);

                // Actualizar ubicación en base a todas las actas de este grupo
                $grupo = TransferenciaInterna::where('codigo_acta', $transferencias_interna->codigo_acta)->get();
                foreach ($grupo as $t) {
                    $this->movimientoService->actualizarUbicacionBien($t, $request->input('area_id'));
                }
            } else {
                $transferencias_interna->update($commonData);
                $this->movimientoService->actualizarUbicacionBien($transferencias_interna, $request->input('area_id'));
            }
        });

        return redirect()->route('transferencias-internas.index')
            ->with('success', 'Transferencia interna actualizada exitosamente.');
    }

    /**
     * Elimina una transferencia interna.
     */
    public function destroy(TransferenciaInterna $transferencias_interna): RedirectResponse
    {
        \Illuminate\Support\Facades\DB::transaction(function () use ($transferencias_interna) {
            // Si tiene código de acta (transferencia múltiple), eliminar todas las del mismo acta
            if ($transferencias_interna->codigo_acta) {
                TransferenciaInterna::where('codigo_acta', $transferencias_interna->codigo_acta)->forceDelete();
            } else {
                // Fallback para las antiguas o simples sin codigo_acta
                $transferencias_interna->forceDelete();
            }
        });

        return redirect()->route('transferencias-internas.index')
            ->with('success', 'Transferencia interna eliminada exitosamente.');
    }
}
