<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CmsPageController extends Controller
{
    public function index($slug)
    {
        $page = Page::firstOrCreate(['slug' => $slug], [
            'title_en' => ucfirst(str_replace('-', ' ', $slug)),
            'title_ar' => ucfirst(str_replace('-', ' ', $slug)),
        ]);

        return Inertia::render('Admin/Cms/Edit', [
            'page' => $page,
        ]);
    }

    public function update(Request $request, Page $page)
    {
        $validated = $request->validate([
            'title_en' => 'required|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'content_en' => 'nullable|string',
            'content_ar' => 'nullable|string',
        ]);

        $page->update($validated);

        return back()->with('success', 'Page updated successfully.');
    }

    /**
     * Specifically handle Contact Info which might have more fields if needed, 
     * but for now we use the general content field for the description/map etc.
     * Or we could have a specific ContactController if it gets complex.
     */
}
