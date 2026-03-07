<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use App\Models\Sector;
use Illuminate\Http\Request;

class AdminSectorController extends Controller
{
    public function index()
    {
        $sectores = Sector::withCount('properties')->orderBy('nombre')->paginate(15);
        return view('admins.sectores.index', compact('sectores'));
    }

    public function create()
    {
        return view('admins.sectores.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:100', 'unique:sectores,nombre'],
            'latitud' => ['nullable', 'numeric', 'between:-90,90'],
            'longitud' => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        Sector::create($validated);

        return redirect()->route('admin.sectores.index')
            ->with('success', 'Sector creado correctamente.');
    }

    public function edit(Sector $sector)
    {
        return view('admins.sectores.edit', compact('sector'));
    }

    public function update(Request $request, Sector $sector)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:100', 'unique:sectores,nombre,' . $sector->id],
            'latitud' => ['nullable', 'numeric', 'between:-90,90'],
            'longitud' => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        $sector->update($validated);

        return redirect()->route('admin.sectores.index')
            ->with('success', 'Sector actualizado correctamente.');
    }

    public function destroy(Sector $sector)
    {
        if ($sector->properties()->exists()) {
            return redirect()->route('admin.sectores.index')
                ->with('error', 'No se puede eliminar el sector porque tiene propiedades asociadas.');
        }

        $sector->delete();

        return redirect()->route('admin.sectores.index')
            ->with('success', 'Sector eliminado correctamente.');
    }
}
