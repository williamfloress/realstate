<?php

namespace App\Http\Controllers;

use App\Models\Acabado;
use App\Models\Sector;

class AmcViewController extends Controller
{
    public function index()
    {
        $sectores = Sector::orderBy('nombre')->get();
        $acabadosPiso = Acabado::piso()->orderBy('nombre')->get();
        $acabadosCocina = Acabado::cocina()->orderBy('nombre')->get();
        $acabadosBano = Acabado::bano()->orderBy('nombre')->get();

        return view('amc.index', compact('sectores', 'acabadosPiso', 'acabadosCocina', 'acabadosBano'));
    }
}
