<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QrLink;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class QrController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Qr/Index', [
            'links' => QrLink::orderBy('sort_order')->get(),
            'logo' => Setting::get('qr_logo'),
            'stats' => [
                'total_clicks' => QrLink::sum('clicks'),
                'active_links' => QrLink::where('is_active', true)->count(),
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'platform' => 'nullable|string',
            'url' => 'required|string',
            'color' => 'nullable|string',
            'sort_order' => 'integer',
            'custom_icon' => 'nullable|sometimes|image|max:1024'
        ]);

        if ($request->hasFile('custom_icon')) {
            $validated['custom_icon'] = $request->file('custom_icon')->store('qr/icons', 'public');
        }

        QrLink::create($validated);

        return back()->with('success', 'Transmission node deployed');
    }

    public function update(Request $request, QrLink $qr)
    {
        // Inertia PUT with files is tricky sometimes, so we can use POST with _method=PUT or just validate here
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'platform' => 'nullable|string',
            'url' => 'required|string',
            'color' => 'nullable|string',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
            'custom_icon' => 'nullable|sometimes'
        ]);

        if ($request->hasFile('custom_icon')) {
            $request->validate(['custom_icon' => 'image|max:1024']);
            if ($qr->custom_icon) {
                Storage::disk('public')->delete($qr->custom_icon);
            }
            $validated['custom_icon'] = $request->file('custom_icon')->store('qr/icons', 'public');
        } else {
            unset($validated['custom_icon']);
        }

        $qr->update($validated);

        return back()->with('success', 'Node reconfigured');
    }

    public function destroy(QrLink $qr)
    {
        if ($qr->custom_icon) {
            Storage::disk('public')->delete($qr->custom_icon);
        }
        $qr->delete();
        return back()->with('success', 'Vector decommissioned');
    }

    public function updateLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $oldLogo = Setting::get('qr_logo');
            if ($oldLogo) {
                Storage::disk('public')->delete($oldLogo);
            }

            $path = $request->file('logo')->store('qr', 'public');
            Setting::set('qr_logo', $path);
        }

        return back()->with('success', 'Brain Identity Locked');
    }
}
