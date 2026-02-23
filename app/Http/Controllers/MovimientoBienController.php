<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Departamento;
use App\Models\MovimientoBien;
use App\Models\Bien;
use App\Models\BienExterno;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MovimientoBienController extends Controller
{
    /**
     * Muestra el historial general de movimientos con búsqueda y filtros.
     */
    public function index(Request $request): View
    {
        // Subconsulta para obtener el ID del último movimiento por cada bien único
        $ultimosIds = MovimientoBien::query()
            ->selectRaw('MAX(id) as id')
            ->groupBy('bien_type', 'bien_id');

        // Si hay filtros aplicados, primero filtrar y luego agrupar
        if ($request->filled('buscar')) {
            $buscar = $request->input('buscar');
            $ultimosIds->where(function ($q) use ($buscar) {
                $q->where('numero_bien', 'like', "%{$buscar}%")
                    ->orWhere('descripcion', 'like', "%{$buscar}%");
            });
        }

        if ($request->filled('tipo_movimiento')) {
            $ultimosIds->where('tipo_movimiento', $request->input('tipo_movimiento'));
        }

        if ($request->filled('departamento_origen_id')) {
            $ultimosIds->where('departamento_origen_id', $request->input('departamento_origen_id'));
        }

        if ($request->filled('area_origen_id')) {
            $ultimosIds->where('area_origen_id', $request->input('area_origen_id'));
        }

        if ($request->filled('departamento_destino_id')) {
            $ultimosIds->where('departamento_destino_id', $request->input('departamento_destino_id'));
        }

        if ($request->filled('area_destino_id')) {
            $ultimosIds->where('area_destino_id', $request->input('area_destino_id'));
        }

        if ($request->filled('origen_dtic')) {
            $dticId = Departamento::where('nombre', 'DTIC')->value('id');
            if ($dticId) {
                $ultimosIds->where('departamento_origen_id', $dticId);
            }
        }

        if ($request->filled('fecha_desde')) {
            $ultimosIds->whereDate('fecha', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $ultimosIds->whereDate('fecha', '<=', $request->fecha_hasta);
        }

        // Consulta principal: solo los últimos movimientos por bien
        $query = MovimientoBien::with([
            'departamentoOrigen',
            'departamentoDestino',
            'areaOrigen',
            'areaDestino',
            'user',
            'bien'
        ])
            ->whereIn('id', $ultimosIds)
            ->selectRaw('movimientos_bienes.*, (SELECT COUNT(*) FROM movimientos_bienes AS mb WHERE mb.bien_type = movimientos_bienes.bien_type AND mb.bien_id = movimientos_bienes.bien_id) as total_movimientos')
            ->latest();

        $movimientos = $query->paginate(15)->withQueryString();
        $departamentos = Departamento::orderBy('nombre')->get();
        $areas = \App\Models\Area::orderBy('nombre')->get();
        $dticId = $departamentos->where('nombre', 'DTIC')->first()?->id;
        $tiposMovimiento = MovimientoBien::etiquetasTipo();

        // Estadísticas Rápidas basadas en los filtros actuales
        $statsIds = clone $ultimosIds;

        // Contamos sobre la tabla movimientos usando los IDs filtrados
        $estadisticas = [
            'total' => MovimientoBien::whereIn('id', clone $statsIds)->count(),
            'transferencias' => MovimientoBien::whereIn('id', clone $statsIds)->where('tipo_movimiento', 'transferencia')->count(),
            'mantenimientos' => MovimientoBien::whereIn('id', clone $statsIds)->whereIn('tipo_movimiento', ['mantenimiento', 'mantenimiento_devolucion'])->count(),
            'desincorporaciones' => MovimientoBien::whereIn('id', clone $statsIds)->where('tipo_movimiento', 'desincorporacion')->count(),
            'origen_dtic' => $dticId ? MovimientoBien::whereIn('id', clone $statsIds)->where('departamento_origen_id', $dticId)->count() : 0,
        ];

        return view('movimientos.index', compact('movimientos', 'departamentos', 'areas', 'dticId', 'tiposMovimiento', 'estadisticas'));
    }

    /**
     * Muestra la línea de tiempo de movimientos de un bien específico.
     */
    public function porBien(string $tipo, int $id): View
    {
        $bienType = $tipo === 'dtic' ? Bien::class : BienExterno::class;
        $bien = $bienType === Bien::class ? Bien::findOrFail($id) : BienExterno::findOrFail($id);

        $movimientos = MovimientoBien::where('bien_type', $bienType)
            ->where('bien_id', $id)
            ->with([
                'departamentoOrigen',
                'departamentoDestino',
                'areaOrigen',
                'areaDestino',
                'user',
            ])
            ->orderBy('fecha', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();

        // También buscar movimientos por numero_bien (para bienes que cambiaron de tabla)
        $movimientosPorNumero = MovimientoBien::where('numero_bien', $bien->numero_bien)
            ->where(function ($q) use ($bienType, $id) {
                $q->where('bien_type', '!=', $bienType)
                    ->orWhere('bien_id', '!=', $id);
            })
            ->with([
                'departamentoOrigen',
                'departamentoDestino',
                'areaOrigen',
                'areaDestino',
                'user',
            ])
            ->orderBy('fecha', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();

        // Combinar y ordenar cronológicamente
        $todosMovimientos = $movimientos->merge($movimientosPorNumero)->sortBy([
            ['fecha', 'asc'],
            ['created_at', 'asc'],
        ]);

        return view('movimientos.por-bien', compact('bien', 'tipo', 'todosMovimientos'));
    }
}
