<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Bien;
use App\Models\Desincorporacion;
use App\Models\TransferenciaInterna;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Activitylog\Models\Activity;

class DashboardController extends Controller
{
    /**
     * Muestra el panel principal con estadísticas de bienes.
     */
    public function __invoke(Request $request): View
    {
        // --- Filtro de período ---
        $periodo = $request->input('periodo', 'all');
        $fechaDesde = null;

        switch ($periodo) {
            case 'hoy':
                $fechaDesde = Carbon::today();
                break;
            case 'semana':
                $fechaDesde = Carbon::now()->subWeek();
                break;
            case 'mes':
                $fechaDesde = Carbon::now()->subMonth();
                break;
            case 'trimestre':
                $fechaDesde = Carbon::now()->subMonths(3);
                break;
            default:
                $fechaDesde = null;
        }

        $periodoActual = $periodo;

        // --- Totales (siempre globales) ---
        $totalBienesDTIC = Bien::count();
        $totalBienesExternos = \App\Models\BienExterno::count();
        $totalBienes = $totalBienesDTIC + $totalBienesExternos;

        // --- Helpers para aplicar filtro de fecha ---
        $applyDateFilter = function ($query, string $column = 'created_at') use ($fechaDesde) {
            if ($fechaDesde) {
                $query->where($column, '>=', $fechaDesde);
            }
            return $query;
        };

        // Datos para gráfica de estado (Combinado)
        $porEstadoDTICQuery = Bien::selectRaw('estados.nombre as estado, count(*) as count')
            ->join('estados', 'bienes.estado_id', '=', 'estados.id');
        $applyDateFilter($porEstadoDTICQuery, 'bienes.created_at');
        $porEstadoDTIC = $porEstadoDTICQuery->groupBy('estados.nombre')->get();

        $porEstadoExternoQuery = \App\Models\BienExterno::selectRaw('estados.nombre as estado, count(*) as count')
            ->join('estados', 'bienes_externos.estado_id', '=', 'estados.id');
        $applyDateFilter($porEstadoExternoQuery, 'bienes_externos.created_at');
        $porEstadoExterno = $porEstadoExternoQuery->groupBy('estados.nombre')->get();

        // Combinar datos de estado
        $porEstado = $porEstadoDTIC->concat($porEstadoExterno)
            ->groupBy('estado')
            ->map(function ($items, $key) {
                return [
                    'estado' => $key,
                    'count' => $items->sum('count')
                ];
            })->values();

        // Datos para gráfica de categorías (Combinado)
        $porCategoriaDTICQuery = Bien::selectRaw('categoria_bienes.nombre as categoria, count(*) as count')
            ->join('categoria_bienes', 'bienes.categoria_bien_id', '=', 'categoria_bienes.id');
        $applyDateFilter($porCategoriaDTICQuery, 'bienes.created_at');
        $porCategoriaDTIC = $porCategoriaDTICQuery->groupBy('categoria_bienes.nombre')->get();

        $porCategoriaExternoQuery = \App\Models\BienExterno::selectRaw('categoria_bienes.nombre as categoria, count(*) as count')
            ->join('categoria_bienes', 'bienes_externos.categoria_bien_id', '=', 'categoria_bienes.id');
        $applyDateFilter($porCategoriaExternoQuery, 'bienes_externos.created_at');
        $porCategoriaExterno = $porCategoriaExternoQuery->groupBy('categoria_bienes.nombre')->get();

        // Combinar datos de categoría
        $porCategoria = $porCategoriaDTIC->concat($porCategoriaExterno)
            ->groupBy('categoria')
            ->map(function ($items, $key) {
                return (object) [
                    'categoria' => $key,
                    'count' => $items->sum('count')
                ];
            })->sortByDesc('count')->values();

        // Últimos bienes registrados (Ambos tipos)
        $ultimosBienesDTIC = Bien::with(['categoria', 'estado', 'area'])->latest()->take(5)->get()->map(function($b) {
            $b->tipo_label = 'DTIC';
            return $b;
        });
        
        $ultimosBienesExternos = \App\Models\BienExterno::with(['categoria', 'estado', 'departamento'])->latest()->take(5)->get()->map(function($b) {
            $b->tipo_label = 'Externo';
            return $b;
        });
        
        $ultimosBienes = $ultimosBienesDTIC->concat($ultimosBienesExternos)
            ->sortByDesc('created_at')
            ->take(5);

        // --- Operaciones (con filtro de fecha) ---
        $desincQuery = \App\Models\Desincorporacion::query();
        $transfQuery = \App\Models\TransferenciaInterna::query();
        $distribQuery = \App\Models\DistribucionDireccion::query();

        if ($fechaDesde) {
            $desincQuery->where('created_at', '>=', $fechaDesde);
            $transfQuery->where('created_at', '>=', $fechaDesde);
            $distribQuery->where('created_at', '>=', $fechaDesde);
        }

        $totalDesincorporaciones = $desincQuery->count();
        $totalTransferencias = $transfQuery->count();
        $totalDistribuciones = $distribQuery->count();

        // Estadísticas de Trámites (Estatus de Actas)
        $estatusDesincQuery = \App\Models\Desincorporacion::selectRaw('estatus_actas.nombre as estatus, count(*) as count')
            ->join('estatus_actas', 'desincorporaciones.estatus_acta_id', '=', 'estatus_actas.id');
        $applyDateFilter($estatusDesincQuery, 'desincorporaciones.created_at');
        $estatusDesincorporaciones = $estatusDesincQuery->groupBy('estatus_actas.nombre')->get();
        
        $estatusTransfQuery = \App\Models\TransferenciaInterna::selectRaw('estatus_actas.nombre as estatus, count(*) as count')
            ->join('estatus_actas', 'transferencias_internas.estatus_acta_id', '=', 'estatus_actas.id');
        $applyDateFilter($estatusTransfQuery, 'transferencias_internas.created_at');
        $estatusTransferencias = $estatusTransfQuery->groupBy('estatus_actas.nombre')->get();

        $porEstatusTramite = $estatusDesincorporaciones->concat($estatusTransferencias)
            ->groupBy('estatus')
            ->map(function ($items, $key) {
                return [
                    'estatus' => $key,
                    'count' => $items->sum('count')
                ];
            })->values();

        // --- Operaciones Pendientes (Alertas) ---
        $hoy = Carbon::now();

        $desincPendientes = Desincorporacion::with(['procedencia', 'bien', 'bienExterno', 'estatusActa'])
            ->whereDoesntHave('estatusActa', function ($query) {
                $query->where('nombre', 'Actas Listas');
            })
            ->orderBy('fecha', 'asc')
            ->get()
            ->map(function ($op) use ($hoy) {
                $op->tipo_operacion = 'Desincorporación';
                $op->dias_transcurridos = $op->fecha ? (int) $op->fecha->diffInDays($hoy) : 0;
                $op->nivel_urgencia = $op->dias_transcurridos >= 15 ? 'critico' : ($op->dias_transcurridos >= 5 ? 'atencion' : 'normal');
                $op->nombre_bien = $op->bien?->equipo ?? $op->bienExterno?->equipo ?? $op->descripcion;
                return $op;
            });

        $transfPendientes = TransferenciaInterna::with(['procedencia', 'destino', 'bien', 'bienExterno', 'estatusActa'])
            ->whereDoesntHave('estatusActa', function ($query) {
                $query->where('nombre', 'Actas Listas');
            })
            ->orderBy('fecha', 'asc')
            ->get()
            ->map(function ($op) use ($hoy) {
                $op->tipo_operacion = 'Transferencia';
                $op->dias_transcurridos = $op->fecha ? (int) $op->fecha->diffInDays($hoy) : 0;
                $op->nivel_urgencia = $op->dias_transcurridos >= 15 ? 'critico' : ($op->dias_transcurridos >= 5 ? 'atencion' : 'normal');
                $op->nombre_bien = $op->bien?->equipo ?? $op->bienExterno?->equipo ?? $op->descripcion;
                return $op;
            });

        $operacionesPendientes = $desincPendientes->concat($transfPendientes)
            ->sortByDesc('dias_transcurridos')
            ->values();

        // --- Timeline de Actividad (Spatie ActivityLog) ---
        $actividadesRecientes = Activity::with('causer')
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard', compact(
            'totalBienes', 
            'totalBienesDTIC', 
            'totalBienesExternos', 
            'porEstado', 
            'porCategoria', 
            'ultimosBienes',
            'totalDesincorporaciones',
            'totalTransferencias',
            'totalDistribuciones',
            'porEstatusTramite',
            'operacionesPendientes',
            'actividadesRecientes',
            'periodoActual'
        ));
    }
}
