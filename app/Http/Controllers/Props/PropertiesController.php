<?php

namespace App\Http\Controllers\Props;

use App\Http\Controllers\Controller;
use App\Models\Prop\HomeType;
use App\Models\Prop\Property;
use App\Models\Prop\PropImage;
use App\Models\Prop\SavedProperties;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controlador de propiedades y favoritos.
 *
 * Gestiona el listado, detalle de propiedades y la acción de guardar/quitar
 * propiedades en favoritos (tabla saved_properties).
 */
class PropertiesController extends Controller
{
    public function index()
    {
        $properties = Property::with('homeType')->take(9)->orderBy('created_at', 'desc')->get();
        // IDs de propiedades guardadas por el usuario (para mostrar corazón lleno)
        $savedPropertyIds = Auth::check()
            ? SavedProperties::where('user_id', Auth::id())->pluck('property_id')
            : collect();
        return view('home', compact('properties', 'savedPropertyIds'));
    }

    /**
     * Lista propiedades filtradas por tipo: buy (sale) o rent (rent/lease).
     * Rutas: /buy, /rent
     */
    public function byType(string $type)
    {
        $query = Property::query()->orderBy('created_at', 'desc');

        if ($type === 'buy') {
            $query->where('offer_type', Property::OFFER_SALE);
        } else {
            $query->whereIn('offer_type', [Property::OFFER_RENT, Property::OFFER_LEASE]);
        }

        $properties = $query->with('homeType')->get();
        $savedPropertyIds = Auth::check()
            ? SavedProperties::where('user_id', Auth::id())->pluck('property_id')
            : collect();
        $filterLabel = $type === 'buy' ? 'For Sale' : 'For Rent';

        return view('home', compact('properties', 'savedPropertyIds', 'filterLabel'));
    }

    /**
     * Lista todas las propiedades. Ruta: /properties
     */
    public function all()
    {
        $properties = Property::with('homeType')->orderBy('created_at', 'desc')->get();
        $savedPropertyIds = Auth::check()
            ? SavedProperties::where('user_id', Auth::id())->pluck('property_id')
            : collect();
        return view('home', compact('properties', 'savedPropertyIds'));
    }

    /**
     * Lista propiedades filtradas por tipo de inmueble (home_type slug).
     * Rutas: /properties/condo, /properties/land, etc.
     */
    public function byHomeType(string $homeType)
    {
        $type = HomeType::where('home_type', $homeType)->firstOrFail();
        $properties = Property::with('homeType')
            ->where('home_type_id', $type->id)
            ->orderBy('created_at', 'desc')
            ->get();
        $savedPropertyIds = Auth::check()
            ? SavedProperties::where('user_id', Auth::id())->pluck('property_id')
            : collect();
        $filterLabel = $type->name;
        return view('home', compact('properties', 'savedPropertyIds', 'filterLabel'));
    }

    public function single($id)
    {
        $singleProperty = Property::with('homeType')->findOrFail($id);
        $relatedProperties = Property::where('city', $singleProperty->city)
            ->where('id', '!=', $singleProperty->id)
            ->take(3)
            ->orderBy('created_at', 'desc')
            ->get();
        $images = PropImage::where('property_id', $id)->orderBy('order')->get();
        // IDs guardados y si la propiedad actual está guardada
        $savedPropertyIds = Auth::check()
            ? SavedProperties::where('user_id', Auth::id())->pluck('property_id')
            : collect();
        $isSinglePropertySaved = Auth::check() && SavedProperties::where('user_id', Auth::id())
            ->where('property_id', $singleProperty->id)->exists();
        return view('properties.single_property', compact('singleProperty', 'relatedProperties', 'images', 'savedPropertyIds', 'isSinglePropertySaved'));
    }

    /**
     * Guarda o quita una propiedad de favoritos (toggle).
     *
     * Si ya está guardada, la elimina. Si no, la crea en saved_properties.
     * Requiere usuario autenticado (middleware auth en la ruta).
     */
    public function saveProperty(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
        ]);
        $propertyId = (int) $validated['property_id'];

        $existing = SavedProperties::where('user_id', Auth::id())
            ->where('property_id', $propertyId)
            ->first();

        if ($existing) {
            $existing->delete();
            return back()->with('success', 'Propiedad quitada de favoritos.');
        }

        SavedProperties::create([
            'user_id' => Auth::id(),
            'property_id' => $propertyId,
        ]);
        return back()->with('success', 'Propiedad guardada en favoritos.');
    }
} 
