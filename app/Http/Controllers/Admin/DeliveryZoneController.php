<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryZone;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DeliveryZoneController extends Controller
{
    public function index(Request $request)
    {
        $query = DeliveryZone::query();

        if ($request->search) {
            $query->where('city', 'like', "%{$request->search}%");
        }

        if ($request->sort) {
            $query->orderBy('price', $request->sort === 'desc' ? 'desc' : 'asc');
        } else {
            $query->orderBy('id', 'desc');
        }

        return Inertia::render('Admin/Delivery/Index', [
            'zones' => $query->paginate(20)->withQueryString(),
            'filters' => $request->only(['search', 'sort'])
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'city' => 'required|string|unique:delivery_zones,city|max:255',
            'price' => 'required|numeric|min:0',
            'estimated_days' => 'required|string|max:50',
            'is_active' => 'boolean',
            'is_free' => 'boolean',
            'express_price' => 'nullable|numeric|min:0',
            'free_delivery_threshold' => 'nullable|numeric|min:0',
        ]);

        DeliveryZone::create($validated);

        return redirect()->back()->with('success', 'Delivery zone added successfully.');
    }

    public function update(Request $request, DeliveryZone $deliveryZone)
    {
        $validated = $request->validate([
            'city' => 'required|string|max:255|unique:delivery_zones,city,' . $deliveryZone->id,
            'price' => 'required|numeric|min:0',
            'estimated_days' => 'required|string|max:50',
            'is_active' => 'boolean',
            'is_free' => 'boolean',
            'express_price' => 'nullable|numeric|min:0',
            'free_delivery_threshold' => 'nullable|numeric|min:0',
        ]);

        $deliveryZone->update($validated);

        return redirect()->back()->with('success', 'Delivery zone updated successfully.');
    }

    public function toggle(DeliveryZone $deliveryZone)
    {
        $deliveryZone->update(['is_active' => !$deliveryZone->is_active]);
        
        return redirect()->back()->with('success', 'Status toggled successfully.');
    }

    public function destroy(DeliveryZone $deliveryZone)
    {
        $deliveryZone->delete();

        return redirect()->back()->with('success', 'Delivery zone deleted.');
    }
}
