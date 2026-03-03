<?php

namespace App\Http\Controllers\Props;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Prop\Property;
use App\Models\Prop\PropImage;


class PropertiesController extends Controller
{
    //
    public function index()
    {
        $properties = Property::select()->take(9)->orderBy('created_at', 'desc')->get();
        return view('home', compact('properties'));
    }  
    public function single($id)
    {
        $singleProperty = Property::findOrFail($id);
        // Propiedades relacionadas: misma ciudad, excluyendo la actual (máx. 3)
        $relatedProperties = Property::where('city', $singleProperty->city)
            ->where('id', '!=', $singleProperty->id)
            ->take(3)
            ->get();
        $images = PropImage::where('property_id', $id)->orderBy('order')->get(); 
        return view('properties.single_property', compact('singleProperty', 'relatedProperties', 'images'));
    }   
}
