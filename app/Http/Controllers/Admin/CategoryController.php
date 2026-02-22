<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Categories', [
            'categories' => Category::with('parent')->withCount('products')->orderBy('sort_order')->orderBy('created_at', 'desc')->get(),
            'parentCategories' => Category::whereNull('parent_id')->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image_file' => 'nullable|image|max:2048',
            'show_on_homepage' => 'boolean',
            'parent_id' => 'nullable|exists:categories,id',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
        ]);

        $data = $request->only('name', 'show_on_homepage', 'parent_id', 'sort_order', 'is_active', 'slug', 'meta_title', 'meta_description', 'meta_keywords');
        
        if (empty($data['slug'])) {
            $data['slug'] = \Illuminate\Support\Str::slug($data['name']);
        }

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('categories', 'public');
            $data['image'] = Storage::url($path);
        }

        Category::create($data);

        return redirect()->back()->with('success', 'Category created successfully.');
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image_file' => 'nullable|image|max:2048',
            'show_on_homepage' => 'boolean',
            'parent_id' => 'nullable|exists:categories,id',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
            'slug' => 'nullable|string|max:255|unique:categories,slug,' . $category->id,
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
        ]);

        $data = $request->only('name', 'show_on_homepage', 'parent_id', 'sort_order', 'is_active', 'slug', 'meta_title', 'meta_description', 'meta_keywords');

        if (empty($data['slug'])) {
            $data['slug'] = \Illuminate\Support\Str::slug($data['name']);
        }

        if ($request->hasFile('image_file')) {
            if ($category->image) {
                $oldPath = str_replace('/storage/', '', $category->image);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('image_file')->store('categories', 'public');
            $data['image'] = Storage::url($path);
        }

        $category->update($data);

        return redirect()->back()->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        if ($category->image) {
            $oldPath = str_replace('/storage/', '', $category->image);
            Storage::disk('public')->delete($oldPath);
        }
        $category->delete();

        return redirect()->back()->with('success', 'Category deleted successfully.');
    }
}
