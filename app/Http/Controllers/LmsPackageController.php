<?php

namespace App\Http\Controllers;

use App\Models\LmsPointPackage;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LmsPackageController extends Controller
{
    public function index()
    {
        $packages = LmsPointPackage::orderBy('order')->orderBy('points')->get();

        return Inertia::render('LmsPackages/Index', [
            'packages' => $packages,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'points' => ['required', 'integer', 'min:1'],
            'price' => ['required', 'string', 'max:100'],
            'payment_link' => ['nullable', 'string', 'max:500'],
        ]);

        $validated['order'] = LmsPointPackage::max('order') + 1;

        LmsPointPackage::create($validated);

        return redirect()->route('lms-packages.index');
    }

    public function update(Request $request, LmsPointPackage $lmsPackage)
    {
        $validated = $request->validate([
            'points' => ['required', 'integer', 'min:1'],
            'price' => ['required', 'string', 'max:100'],
            'payment_link' => ['nullable', 'string', 'max:500'],
        ]);

        $lmsPackage->update($validated);

        return redirect()->route('lms-packages.index');
    }

    public function destroy(LmsPointPackage $lmsPackage)
    {
        $lmsPackage->delete();

        return redirect()->route('lms-packages.index');
    }
}
