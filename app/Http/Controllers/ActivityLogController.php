<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    /**
     * Muestra el historial de actividad del sistema.
     */
    public function index(Request $request): View
    {
        $query = Activity::with('causer')->latest();

        // Búsqueda por texto
        if ($request->filled('buscar')) {
            $buscar = $request->input('buscar');
            $query->where(function ($q) use ($buscar) {
                $q->where('description', 'like', "%{$buscar}%")
                  ->orWhere('log_name', 'like', "%{$buscar}%")
                  ->orWhereHas('causer', function ($qUser) use ($buscar) {
                      $qUser->where('name', 'like', "%{$buscar}%");
                  });
            });
        }

        // Filtro por tipo de log
        if ($request->filled('log_name')) {
            $query->where('log_name', $request->input('log_name'));
        }

        // Filtro por evento
        if ($request->filled('event')) {
            $query->where('event', $request->input('event'));
        }

        $activities = $query->paginate(15)->withQueryString();

        // Obtener todos los log_names únicos para el filtro
        $logNames = Activity::distinct()->pluck('log_name')->filter()->sort()->values();

        return view('activity-log.index', compact('activities', 'logNames'));
    }
}
