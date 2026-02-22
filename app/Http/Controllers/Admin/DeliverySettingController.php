<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliverySetting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DeliverySettingController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Delivery/Settings', [
            'settings' => DeliverySetting::first() ?: DeliverySetting::create(['free_delivery_over' => null, 'express_enabled' => true])
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'free_delivery_over' => 'nullable|numeric|min:0',
            'express_enabled' => 'boolean',
        ]);

        $settings = DeliverySetting::first() ?: new DeliverySetting();
        $settings->fill($validated);
        $settings->save();

        return back()->with('success', 'Settings updated.');
    }
}
