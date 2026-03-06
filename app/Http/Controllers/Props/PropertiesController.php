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
    /**
     * Lista propiedades en home. Aplica filtros del formulario de búsqueda
     * (list-types, offer-types, select-city) si se envían por GET.
     */
    public function index(Request $request)
    {
        $query = Property::with('homeType')->active()->orderBy('created_at', 'desc');

        // Filtro por tipo de inmueble (condo, house, land, etc.)
        if ($request->filled('list-types')) {
            $homeType = HomeType::where('home_type', $request->input('list-types'))->first();
            if ($homeType) {
                $query->where('home_type_id', $homeType->id);
            }
        }

        // Filtro por tipo de oferta (sale, rent, lease)
        if ($request->filled('offer-types')) {
            $query->where('offer_type', $request->input('offer-types'));
        }

        // Filtro por ciudad
        if ($request->filled('select-city')) {
            $query->where('city', $request->input('select-city'));
        }

        $properties = $request->hasAny(['list-types', 'offer-types', 'select-city'])
            ? $query->get()
            : $query->take(9)->get();

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
        $query = Property::query()->active()->orderBy('created_at', 'desc');

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
        $properties = Property::with('homeType')->active()->orderBy('created_at', 'desc')->get();
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
            ->active()
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
        $relatedProperties = Property::active()
            ->where('city', $singleProperty->city)
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

    /**
     * Lista propiedades ordenadas por precio ascendente (menor a mayor).
     * Ruta: /properties/price-asc
     */
    public function priceAsc()
    {
        $properties = Property::with('homeType')->active()->orderBy('price', 'asc')->get();
        $savedPropertyIds = Auth::check()
            ? SavedProperties::where('user_id', Auth::id())->pluck('property_id')
            : collect();
        $filterLabel = 'Price: Low to High';

        return view('home', compact('properties', 'savedPropertyIds', 'filterLabel'));
    }

    /**
     * Lista propiedades ordenadas por precio descendente (mayor a menor).
     * Ruta: /properties/price-desc
     */
    public function priceDesc()
    {
        $properties = Property::with('homeType')->active()->orderBy('price', 'desc')->get();
        $savedPropertyIds = Auth::check()
            ? SavedProperties::where('user_id', Auth::id())->pluck('property_id')
            : collect();
        $filterLabel = 'Price: High to Low';

        return view('home', compact('properties', 'savedPropertyIds', 'filterLabel'));
    }
} 
