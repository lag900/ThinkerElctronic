<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Governorate;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GovernorateController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Delivery/Governorates', [
            'governorates' => Governorate::withCount('areas')->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'default_delivery_price' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        Governorate::create($validated);
        return back()->with('success', 'Governorate created.');
    }

    public function update(Request $request, Governorate $governorate)
    {
        $validated = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'default_delivery_price' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $governorate->update($validated);
        return back()->with('success', 'Governorate updated.');
    }

    public function destroy(Governorate $governorate)
    {
        $governorate->delete();
        return back()->with('success', 'Governorate deleted.');
    }
}
