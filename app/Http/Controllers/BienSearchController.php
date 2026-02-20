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
     * Retorna JSON con hasta 20 resultados combinados.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $q = trim($request->input('q', ''));

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        // Separar la cadena de búsqueda en términos
        $terminos = explode(' ', $q);
        // Filtrar términos vacíos por si hay dobles espacios
        $terminos = array_filter($terminos, fn($t) => trim($t) !== '');

        $bienes = Bien::query()
            ->where(function ($query) use ($terminos) {
                foreach ($terminos as $termino) {
                    $query->where(function ($qTerm) use ($termino) {
                        $qTerm->where('numero_bien', 'like', "%{$termino}%")
                            ->orWhere('equipo', 'like', "%{$termino}%")
                            ->orWhere('serial', 'like', "%{$termino}%")
                            ->orWhere('marca', 'like', "%{$termino}%")
                            ->orWhere('modelo', 'like', "%{$termino}%");
                    });
                }
            })
            ->limit(15)
            ->get(['id', 'numero_bien', 'equipo', 'serial', 'marca', 'modelo'])
            ->toBase()
            ->map(fn($b) => [...$b->toArray(), 'tipo' => 'dtic']);

        $externos = BienExterno::query()
            ->where(function ($query) use ($terminos) {
                foreach ($terminos as $termino) {
                    $query->where(function ($qTerm) use ($termino) {
                        $qTerm->where('numero_bien', 'like', "%{$termino}%")
                            ->orWhere('equipo', 'like', "%{$termino}%")
                            ->orWhere('serial', 'like', "%{$termino}%")
                            ->orWhere('marca', 'like', "%{$termino}%")
                            ->orWhere('modelo', 'like', "%{$termino}%");
                    });
                }
            })
            ->limit(15)
            ->get(['id', 'numero_bien', 'equipo', 'serial', 'marca', 'modelo', 'departamento_id'])
            ->toBase()
            ->map(fn($b) => [...$b->toArray(), 'tipo' => 'externo']);

        return response()->json($bienes->merge($externos)->take(20)->values());
    }
}
