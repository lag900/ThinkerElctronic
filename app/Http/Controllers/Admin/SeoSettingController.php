<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SeoSettingController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/SeoSettings', [
            'settings' => [
                'google_verification_code' => Setting::get('google_verification_code', ''),
                'global_meta_title' => Setting::get('global_meta_title', 'Thinker Technology Store'),
                'global_meta_description' => Setting::get('global_meta_description', 'Best laptops and tech in Egypt'),
                'global_meta_keywords' => Setting::get('global_meta_keywords', 'thinker, laptops, egypt, tech'),
            ]
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'google_verification_code' => 'nullable|string',
            'global_meta_title' => 'required|string',
            'global_meta_description' => 'required|string',
            'global_meta_keywords' => 'nullable|string',
        ]);

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        return redirect()->back()->with('success', 'SEO settings updated.');
    }
}
