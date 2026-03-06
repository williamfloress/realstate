<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use App\Models\Prop\HomeType;
use App\Models\Prop\PropImage;
use App\Models\Prop\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminPropertyController extends Controller
{
    /**
     * Display a listing of properties.
     */
    public function index()
    {
        $properties = Property::with('homeType')->orderBy('created_at', 'desc')->paginate(10);

        return view('admins.properties.index', compact('properties'));
    }

    /**
     * Show the form for creating a new property.
     */
    public function create()
    {
        $homeTypes = HomeType::orderBy('order')->orderBy('name')->get();

        return view('admins.properties.create', compact('homeTypes'));
    }

    /**
     * Store a newly created property.
     */
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
            'home_type_id' => ['nullable', 'exists:home_types,id'],
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
            'home_type_id' => $validated['home_type_id'] ?? null,
            'year_built' => $validated['year_built'] ?? null,
            'status' => $validated['status'] ?? Property::STATUS_DRAFT,
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

        return redirect()->route('admin.properties.index')
            ->with('success', 'Propiedad creada correctamente.');
    }

    /**
     * Show the form for editing a property.
     */
    public function edit(Property $property)
    {
        $property->load('images');
        $homeTypes = HomeType::orderBy('order')->orderBy('name')->get();

        return view('admins.properties.edit', compact('property', 'homeTypes'));
    }

    /**
     * Update the specified property.
     */
    public function update(Request $request, Property $property)
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
            'home_type_id' => ['nullable', 'exists:home_types,id'],
            'year_built' => ['nullable', 'integer', 'min:1800', 'max:' . (date('Y') + 1)],
            'status' => ['nullable', 'in:draft,active,paused,closed,sold,rented,reserved'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpeg,jpg,png,gif,webp', 'max:5120'],
        ], [
            'images.*.image' => 'Cada archivo debe ser una imagen válida.',
            'images.*.max' => 'Cada imagen no puede superar 5 MB.',
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
            'home_type_id' => $validated['home_type_id'] ?? null,
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

        return redirect()->route('admin.properties.index')
            ->with('success', 'Propiedad actualizada correctamente.');
    }

    /**
     * Update property status (quick action).
     */
    public function updateStatus(Request $request, Property $property)
    {
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

        $labels = [
            'draft' => 'Borrador',
            'active' => 'Activa',
            'paused' => 'Pausada',
            'closed' => 'Cerrada',
            'sold' => 'Vendida',
            'rented' => 'Rentada',
            'reserved' => 'Reservado',
        ];

        return redirect()->route('admin.properties.index')
            ->with('success', 'Estado actualizado a: ' . $labels[$validated['status']]);
    }

    /**
     * Remove the specified property.
     */
    public function destroy(Property $property)
    {
        $property->delete();

        return redirect()->route('admin.properties.index')
            ->with('success', 'Propiedad eliminada correctamente.');
    }
}
