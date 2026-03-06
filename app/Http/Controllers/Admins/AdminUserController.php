<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use App\Models\Admin\Admin;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class AdminUserController extends Controller
{
    /**
     * Display a listing of admin users.
     */
    public function index()
    {
        $admins = Admin::orderBy('created_at', 'desc')->paginate(10);

        return view('admins.users.index', compact('admins'));
    }

    /**
     * Show the form for creating a new admin user.
     */
    public function create()
    {
        return view('admins.users.create');
    }

    /**
     * Store a newly created admin user.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:admins,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        Admin::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Administrador creado correctamente.');
    }
}
