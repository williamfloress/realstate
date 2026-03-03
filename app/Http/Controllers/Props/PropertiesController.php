<?php

namespace App\Http\Controllers\Props;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Prop\Property;

class PropertiesController extends Controller
{
    //
    public function index()
    {
        $properties = Property::select()->take(9)->orderBy('created_at', 'desc')->get();
        return view('home', compact('properties'));
    }
}
