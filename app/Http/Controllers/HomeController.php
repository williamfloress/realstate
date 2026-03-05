<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->only(['index']);
    }

    /**
     * Show the application dashboard.
     */
    public function index()
    {
        return view('home');
    }

    /**
     * Página About (pública).
     */
    public function about()
    {
        return view('pages.about');
    }

    /**
     * Página de contacto (pública).
     */
    public function contact()
    {
        return view('pages.contact');
    }
}
