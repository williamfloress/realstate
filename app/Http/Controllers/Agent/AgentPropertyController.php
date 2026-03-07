<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Acabado;
use App\Models\Prop\HomeType;
use App\Models\Sector;
use App\Models\Prop\PropImage;
use App\Models\Prop\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AgentPropertyController extends Controller
{
    /** Propiedades del agente (solo las suyas) - para editar/eliminar */
    private function agentProperties()
    {
        return Property::with('homeType')->where('agent_id', auth()->id())->orderBy('created_at', 'desc');
    }

    /** Todas las propiedades habilitadas (activas) - para visualización del agente */
    private function allEnabledProperties()
    {
        return Property::with(['homeType', 'sector', 'agent'])
            ->where('status', Property::STATUS_ACTIVE)
            ->orderBy('created_at', 'desc');
    }

    public function index(Request $request)
    {
        $filter = $request->get('filter', 'mias');

        $countMias = Property::where('agent_id', auth()->id())->count();
        $countTodas = Property::where('status', Property::STATUS_ACTIVE)->count();

        $query = $filter === 'todas'
            ? $this->allEnabledProperties()
            : $this->agentProperties();

        $properties = $query->paginate(10)->withQueryString();

        return view('agent.properties.index', compact('properties', 'filter', 'countMias', 'countTodas'));
    }

    public function create()
    {
        $homeTypes = HomeType::orderBy('order')->orderBy('name')->get();
        $sectores = Sector::orderBy('nombre')->get();
        $acabadosPiso = Acabado::piso()->orderBy('nombre')->get();
        $acabadosCocina = Acabado::cocina()->orderBy('nombre')->get();
        $acabadosBano = Acabado::bano()->orderBy('nombre')->get();
        return view('agent.properties.create', compact('homeTypes', 'sectores', 'acabadosPiso', 'acabadosCocina', 'acabadosBano'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'zip' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:2'],
            'offer_type' => ['required', 'in:sale,rent,lease'],
            'beds' => ['nullable', 'integer', 'min:0'],
            'baths' => ['nullable', 'integer', 'min:0'],
            'sqft' => ['nullable', 'integer', 'min:0'],
            'area_construccion_m2' => ['nullable', 'numeric', 'min:0'],
            'parqueos' => ['nullable', 'integer', 'min:0'],
            'home_type_id' => ['nullable', 'exists:home_types,id'],
            'sector_id' => ['nullable', 'exists:sectores,id'],
            'acabado_piso_id' => ['nullable', 'exists:acabados,id'],
            'acabado_cocina_id' => ['nullable', 'exists:acabados,id'],
            'acabado_bano_id' => ['nullable', 'exists:acabados,id'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'year_built' => ['nullable', 'integer', 'min:1800', 'max:' . (date('Y') + 1)],
            'status' => ['nullable', 'in:draft,active,paused,closed,sold,rented,reserved'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpeg,jpg,png,gif,webp', 'max:5120'],
        ], [
            'images.*.image' => 'Cada archivo debe ser una imagen válida.',
            'images.*.max' => 'Cada imagen no puede superar 5 MB.',
        ]);

        $slug = Str::slug($validated['title']);
        $baseSlug = $slug;
        $counter = 1;
        while (Property::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        $property = Property::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'currency' => 'USD',
            'address' => $validated['address'] ?? null,
            'city' => $validated['city'] ?? null,
            'state' => $validated['state'] ?? null,
            'zip' => $validated['zip'] ?? null,
            'country' => $validated['country'] ?? null,
            'offer_type' => $validated['offer_type'],
            'beds' => $validated['beds'] ?? null,
            'baths' => $validated['baths'] ?? null,
            'sqft' => $validated['sqft'] ?? null,
            'area_construccion_m2' => $validated['area_construccion_m2'] ?? $validated['sqft'] ?? null,
            'parqueos' => $validated['parqueos'] ?? null,
            'home_type_id' => $validated['home_type_id'] ?? null,
            'sector_id' => $validated['sector_id'] ?? null,
            'acabado_piso_id' => $validated['acabado_piso_id'] ?? null,
            'acabado_cocina_id' => $validated['acabado_cocina_id'] ?? null,
            'acabado_bano_id' => $validated['acabado_bano_id'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'year_built' => $validated['year_built'] ?? null,
            'status' => $validated['status'] ?? Property::STATUS_DRAFT,
            'agent_id' => auth()->id(),
        ]);

        $firstImagePath = null;
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $order => $file) {
                $path = $file->store('properties', 'public');
                PropImage::create([
                    'property_id' => $property->id,
                    'path' => $path,
                    'order' => $order,
                ]);
                if ($firstImagePath === null) {
                    $firstImagePath = $path;
                }
            }
            if ($firstImagePath) {
                $property->update(['image' => $firstImagePath]);
            }
        }

        return redirect()->route('agent.properties.index')->with('success', 'Propiedad creada correctamente.');
    }

    public function edit(Property $property)
    {
        if ($property->agent_id !== auth()->id()) {
            abort(403);
        }
        $property->load('images');
        $homeTypes = HomeType::orderBy('order')->orderBy('name')->get();
        $sectores = Sector::orderBy('nombre')->get();
        $acabadosPiso = Acabado::piso()->orderBy('nombre')->get();
        $acabadosCocina = Acabado::cocina()->orderBy('nombre')->get();
        $acabadosBano = Acabado::bano()->orderBy('nombre')->get();
        return view('agent.properties.edit', compact('property', 'homeTypes', 'sectores', 'acabadosPiso', 'acabadosCocina', 'acabadosBano'));
    }

    public function update(Request $request, Property $property)
    {
        if ($property->agent_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'zip' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:2'],
            'offer_type' => ['required', 'in:sale,rent,lease'],
            'beds' => ['nullable', 'integer', 'min:0'],
            'baths' => ['nullable', 'integer', 'min:0'],
            'sqft' => ['nullable', 'integer', 'min:0'],
            'area_construccion_m2' => ['nullable', 'numeric', 'min:0'],
            'parqueos' => ['nullable', 'integer', 'min:0'],
            'home_type_id' => ['nullable', 'exists:home_types,id'],
            'sector_id' => ['nullable', 'exists:sectores,id'],
            'acabado_piso_id' => ['nullable', 'exists:acabados,id'],
            'acabado_cocina_id' => ['nullable', 'exists:acabados,id'],
            'acabado_bano_id' => ['nullable', 'exists:acabados,id'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'year_built' => ['nullable', 'integer', 'min:1800', 'max:' . (date('Y') + 1)],
            'status' => ['nullable', 'in:draft,active,paused,closed,sold,rented,reserved'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpeg,jpg,png,gif,webp', 'max:5120'],
        ]);

        $newStatus = $validated['status'] ?? $property->status;
        $updateData = [
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'address' => $validated['address'] ?? null,
            'city' => $validated['city'] ?? null,
            'state' => $validated['state'] ?? null,
            'zip' => $validated['zip'] ?? null,
            'country' => $validated['country'] ?? null,
            'offer_type' => $validated['offer_type'],
            'beds' => $validated['beds'] ?? null,
            'baths' => $validated['baths'] ?? null,
            'sqft' => $validated['sqft'] ?? null,
            'area_construccion_m2' => $validated['area_construccion_m2'] ?? $validated['sqft'] ?? null,
            'parqueos' => $validated['parqueos'] ?? null,
            'home_type_id' => $validated['home_type_id'] ?? null,
            'sector_id' => $validated['sector_id'] ?? null,
            'acabado_piso_id' => $validated['acabado_piso_id'] ?? null,
            'acabado_cocina_id' => $validated['acabado_cocina_id'] ?? null,
            'acabado_bano_id' => $validated['acabado_bano_id'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'year_built' => $validated['year_built'] ?? null,
            'status' => $newStatus,
        ];
        if ($newStatus === 'reserved') {
            $updateData['reserved_at'] = now();
        } elseif (in_array($newStatus, ['sold', 'rented'])) {
            $updateData['closed_at'] = now();
            $updateData['reserved_at'] = null;
        } elseif ($newStatus === 'active') {
            $updateData['closed_at'] = null;
            $updateData['reserved_at'] = null;
        }
        $property->update($updateData);

        if ($request->hasFile('images')) {
            $maxOrder = $property->images()->max('order') ?? -1;
            foreach ($request->file('images') as $file) {
                $maxOrder++;
                $path = $file->store('properties', 'public');
                PropImage::create([
                    'property_id' => $property->id,
                    'path' => $path,
                    'order' => $maxOrder,
                ]);
            }
            if (!$property->image) {
                $firstNew = $property->images()->orderBy('order')->first();
                if ($firstNew) {
                    $property->update(['image' => $firstNew->path]);
                }
            }
        }

        return redirect()->route('agent.properties.index')->with('success', 'Propiedad actualizada correctamente.');
    }

    public function updateStatus(Request $request, Property $property)
    {
        if ($property->agent_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => ['required', 'in:draft,active,paused,closed,sold,rented,reserved'],
        ]);

        $update = ['status' => $validated['status']];
        if (in_array($validated['status'], ['sold', 'rented'])) {
            $update['closed_at'] = now();
            $update['reserved_at'] = null;
        } elseif ($validated['status'] === 'reserved') {
            $update['reserved_at'] = now();
        } elseif ($validated['status'] === 'active') {
            $update['closed_at'] = null;
            $update['reserved_at'] = null;
        }
        $property->update($update);

        return redirect()->route('agent.properties.index')->with('success', 'Estado actualizado.');
    }

    public function destroy(Property $property)
    {
        if ($property->agent_id !== auth()->id()) {
            abort(403);
        }
        $property->delete();
        return redirect()->route('agent.properties.index')->with('success', 'Propiedad eliminada.');
    }
}
