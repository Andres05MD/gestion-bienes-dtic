<?php

namespace App\Http\Controllers;

use App\Models\Area;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    public function index()
    {
        $areas = Area::orderBy('nombre')->paginate(10);
        return view('areas.index', compact('areas'));
    }

    public function create()
    {
        return view('areas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:areas',
            'descripcion' => 'nullable|string',
        ]);

        Area::create($request->all());

        return redirect()->route('areas.index')->with('success', 'Área creada exitosamente.');
    }

    public function show(Area $area)
    {
        return view('areas.show', compact('area'));
    }

    public function edit(Area $area)
    {
        return view('areas.edit', compact('area'));
    }

    public function update(Request $request, Area $area)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:areas,nombre,' . $area->id,
            'descripcion' => 'nullable|string',
        ]);

        $area->update($request->all());

        return redirect()->route('areas.index')->with('success', 'Área actualizada exitosamente.');
    }

    public function destroy(Area $area)
    {
        $area->forceDelete();
        return redirect()->route('areas.index')->with('success', 'Área eliminada exitosamente.');
    }
}
