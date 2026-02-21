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
            ->with(['categoria', 'estado', 'area'])
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
            ->get(['id', 'numero_bien', 'equipo', 'serial', 'marca', 'modelo', 'color', 'categoria_bien_id', 'estado_id', 'area_id'])
            ->map(function ($b) {
                $data = $b->toArray();
                $data['tipo'] = 'dtic';
                $data['categoria'] = $b->categoria ? $b->categoria->nombre : 'Sin Categoría';
                $data['estado_nombre'] = $b->estado ? $b->estado->nombre : 'Sin Estado';
                $data['area_nombre'] = $b->area ? $b->area->nombre : 'Sin Área';
                return $data;
            });

        $externos = BienExterno::query()
            ->with(['categoria', 'estado', 'departamento'])
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
            ->get(['id', 'numero_bien', 'equipo', 'serial', 'marca', 'modelo', 'color', 'categoria_bien_id', 'estado_id', 'departamento_id', 'area_id'])
            ->map(function ($b) {
                $data = $b->toArray();
                $data['tipo'] = 'externo';
                $data['categoria'] = $b->categoria ? $b->categoria->nombre : 'Sin Categoría';
                $data['estado_nombre'] = $b->estado ? $b->estado->nombre : 'Sin Estado';
                $data['departamento_nombre'] = $b->departamento ? $b->departamento->nombre : 'Sin Departamento';
                return $data;
            });

        return response()->json($bienes->merge($externos)->take(20)->values());
    }
}
