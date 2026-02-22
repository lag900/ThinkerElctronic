<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class HomeContentController extends Controller
{
    public function index()
    {
        $content = Setting::where('key', 'home_content')->first();
        $data = $content ? json_decode($content->value, true) : $this->getDefaultContent();

        return Inertia::render('Admin/HomeContent/Index', [
            'content' => $data
        ]);
    }

    public function update(Request $request)
    {
        $newData = $request->input('content');

        // Handle File Uploads for specific sections
        if ($request->hasFile('files')) {
            $files = $request->file('files');
            foreach ($files as $path => $file) {
                // $path format: "hero.image", "slider.0.image", etc.
                $uploadedPath = $file->store('home', 'public');
                $url = '/storage/' . $uploadedPath;
                
                // Update newData based on path
                $this->setNestedValue($newData, $path, $url);
            }
        }

        Setting::updateOrCreate(
            ['key' => 'home_content'],
            ['value' => json_encode($newData)]
        );

        return back()->with('success', 'Homepage content updated successfully.');
    }

    private function setNestedValue(&$arr, $path, $url)
    {
        $keys = explode('.', $path);
        
        $current = &$arr;
        foreach ($keys as $i => $key) {
            if ($i === count($keys) - 1) {
                $current[$key] = $url;
            } else {
                if (!isset($current[$key]) || !is_array($current[$key])) {
                    $current[$key] = [];
                }
                $current = &$current[$key];
            }
        }
    }

    private function getDefaultContent()
    {
        return [
            'hero' => [
                'title' => 'Smart Thinking. Smart Systems.',
                'subtitle' => 'A world of technical and engineering solutions tailored specifically to your needs.',
                'button_text' => 'Shop Now',
                'image' => null,
            ],
            'slider' => [],
            'custom_section' => [
                'title' => 'Premium Hardware Architecture',
                'text' => 'We design systems that integrate seamlessly with your neural and digital ecosystem.',
                'button_text' => 'Explore More',
                'image' => null,
                'link' => null,
            ],
            'features' => [
                ['title' => 'Speed', 'text' => 'Optimized for high-frequency operations.', 'icon' => 'zap'],
                ['title' => 'Security', 'text' => 'Encrypted data transmission protocols.', 'icon' => 'shield'],
                ['title' => 'Scale', 'text' => 'Built to grow with your infrastructure.', 'icon' => 'cpu'],
            ]
        ];
    }
}
