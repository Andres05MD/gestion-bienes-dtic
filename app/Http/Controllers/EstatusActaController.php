<?php

namespace App\Http\Controllers;

use App\Models\EstatusActa;
use Illuminate\Http\Request;

class EstatusActaController extends Controller
{
    public function index()
    {
        $estatusActas = EstatusActa::orderBy('nombre')->paginate(10);
        return view('estatus-actas.index', compact('estatusActas'));
    }

    public function create()
    {
        return view('estatus-actas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:estatus_actas',
            'color' => 'nullable|string|max:20',
        ]);

        EstatusActa::create($request->all());

        return redirect()->route('estatus-actas.index')->with('success', 'Estatus de acta creado exitosamente.');
    }

    public function edit(EstatusActa $estatusActa)
    {
        return view('estatus-actas.edit', compact('estatusActa'));
    }

    public function update(Request $request, EstatusActa $estatusActa)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:estatus_actas,nombre,' . $estatusActa->id,
            'color' => 'nullable|string|max:20',
        ]);

        $estatusActa->update($request->all());

        return redirect()->route('estatus-actas.index')->with('success', 'Estatus de acta actualizado exitosamente.');
    }

    public function destroy(EstatusActa $estatusActa)
    {
        $estatusActa->delete();
        return redirect()->route('estatus-actas.index')->with('success', 'Estatus de acta eliminado exitosamente.');
    }
}
