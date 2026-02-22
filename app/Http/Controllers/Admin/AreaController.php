<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Governorate;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AreaController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Delivery/Areas', [
            'areas' => Area::with('governorate')->get(),
            'governorates' => Governorate::all()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'governorate_id' => 'required|exists:governorates,id',
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'delivery_price' => 'required|numeric|min:0',
            'express_price' => 'nullable|numeric|min:0',
            'delivery_days' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        Area::create($validated);
        return back()->with('success', 'Area created.');
    }

    public function update(Request $request, Area $area)
    {
        $validated = $request->validate([
            'governorate_id' => 'required|exists:governorates,id',
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'delivery_price' => 'required|numeric|min:0',
            'express_price' => 'nullable|numeric|min:0',
            'delivery_days' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $area->update($validated);
        return back()->with('success', 'Area updated.');
    }

    public function destroy(Area $area)
    {
        $area->delete();
        return back()->with('success', 'Area deleted.');
    }
}
