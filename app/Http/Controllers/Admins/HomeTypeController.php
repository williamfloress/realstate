<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use App\Models\Prop\HomeType;
use Illuminate\Http\Request;

class HomeTypeController extends Controller
{
    /**
     * Display a listing of home types.
     */
    public function index()
    {
        $homeTypes = HomeType::withCount('properties')->orderBy('order')->orderBy('name')->paginate(10);

        return view('admins.hometypes.index', compact('homeTypes'));
    }

    /**
     * Show the form for creating a new home type.
     */
    public function create()
    {
        return view('admins.hometypes.create');
    }

    /**
     * Store a newly created home type.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'home_type' => ['required', 'string', 'max:50', 'unique:home_types,home_type', 'regex:/^[a-z0-9\-]+$/'],
            'name' => ['required', 'string', 'max:100'],
            'order' => ['nullable', 'integer', 'min:0'],
        ], [
            'home_type.regex' => 'El slug solo puede contener letras minúsculas, números y guiones.',
        ]);

        HomeType::create([
            'home_type' => strtolower($validated['home_type']),
            'name' => $validated['name'],
            'order' => $validated['order'] ?? (HomeType::max('order') ?? 0) + 1,
        ]);

        return redirect()->route('admin.hometypes.index')
            ->with('success', 'Tipo de propiedad creado correctamente.');
    }
}
