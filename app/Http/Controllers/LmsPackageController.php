<?php

namespace App\Http\Controllers;

use App\Models\LmsPointPackage;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LmsPackageController extends Controller
{
    public function index()
    {
        $packages = LmsPointPackage::orderBy('sort_order')->orderBy('points')->get();

        return Inertia::render('Admin/LmsPackages/Index', [
            'packages' => $packages,
        ]);
    }

    public function frontend()
    {
        $packages = LmsPointPackage::where('active', true)->orderBy('sort_order')->orderBy('points')->get();

        return Inertia::render('LmsPoints', [
            'packages' => $packages,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'points' => ['required', 'integer', 'min:1'],
            'price' => ['required', 'string', 'max:100'],
            'payment_link' => ['nullable', 'string', 'max:500'],
            'active' => ['boolean'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        if (!isset($validated['sort_order'])) {
            $validated['sort_order'] = LmsPointPackage::max('sort_order') + 1;
        }

        LmsPointPackage::create($validated);

        return redirect()->route('lms-packages.index')->with('success', 'Package created successfully.');
    }

    public function update(Request $request, LmsPointPackage $lmsPackage)
    {
        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'points' => ['required', 'integer', 'min:1'],
            'price' => ['required', 'string', 'max:100'],
            'payment_link' => ['nullable', 'string', 'max:500'],
            'active' => ['boolean'],
            'sort_order' => ['integer'],
        ]);

        $lmsPackage->update($validated);

        return redirect()->route('lms-packages.index')->with('success', 'Package updated successfully.');
    }

    public function destroy(LmsPointPackage $lmsPackage)
    {
        $lmsPackage->delete();

        return redirect()->route('lms-packages.index')->with('success', 'Package deleted successfully.');
    }
}
