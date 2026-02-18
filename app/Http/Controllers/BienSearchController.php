<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Bien;
use App\Models\BienExterno;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BienSearchController extends Controller
{
    /**
     * Búsqueda AJAX de bienes (DTIC + Externos).
     * Retorna JSON con hasta 15 resultados combinados.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $q = trim($request->input('q', ''));

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $bienes = Bien::query()
            ->where(function ($query) use ($q) {
                $query->where('numero_bien', 'like', "%{$q}%")
                    ->orWhere('equipo', 'like', "%{$q}%")
                    ->orWhere('serial', 'like', "%{$q}%");
            })
            ->limit(10)
            ->get(['id', 'numero_bien', 'equipo', 'serial', 'marca'])
            ->map(fn($b) => [...$b->toArray(), 'tipo' => 'dtic']);

        $externos = BienExterno::query()
            ->where(function ($query) use ($q) {
                $query->where('numero_bien', 'like', "%{$q}%")
                    ->orWhere('equipo', 'like', "%{$q}%")
                    ->orWhere('serial', 'like', "%{$q}%");
            })
            ->limit(10)
            ->get(['id', 'numero_bien', 'equipo', 'serial', 'marca', 'departamento_id'])
            ->map(fn($b) => [...$b->toArray(), 'tipo' => 'externo']);

        return response()->json($bienes->merge($externos)->take(15)->values());
    }
}
