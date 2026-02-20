<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Product/Index', [
            'products' => Product::with('category')->latest()->paginate(10)
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Product/Create', [
            'categories' => Category::all()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'nullable|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'youtube_url' => 'nullable|string|url',
            'main_image' => 'required|image|max:2048',
            'gallery_images.*' => 'nullable|image|max:2048',
            'specs' => 'nullable|array',
            'specs.*.label' => 'required|string',
            'specs.*.value' => 'required|string',
            'codes' => 'nullable|array',
            'codes.*.code' => 'required|string',
            'codes.*.language' => 'required|string',
            'codes.*.title' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated, $request) {
            // Main Image
            $imagePath = $request->file('main_image')->store('products', 'public');

            $product = Product::create([
                'name' => $validated['name'],
                'name_ar' => $validated['name_ar'],
                'sku' => $validated['sku'] ?? null,
                'description' => $validated['description_en'], // Fallback
                'description_en' => $validated['description_en'],
                'description_ar' => $validated['description_ar'],
                'price' => $validated['price'],
                'cost_price' => $validated['cost_price'] ?? 0,
                'stock_quantity' => $validated['stock_quantity'] ?? 0,
                'category_id' => $validated['category_id'],
                'image_url' => '/storage/' . $imagePath,
                'youtube_url' => $validated['youtube_url'],
            ]);

            // Specs
            if (!empty($validated['specs'])) {
                foreach ($validated['specs'] as $index => $spec) {
                    $product->specs()->create([
                        'label' => $spec['label'],
                        'value' => $spec['value'],
                        'sort' => $index
                    ]);
                }
            }

            // Codes
            if (!empty($validated['codes'])) {
                foreach ($validated['codes'] as $code) {
                    $product->codes()->create([
                        'title' => $code['title'] ?? null,
                        'code' => $code['code'],
                        'language' => $code['language']
                    ]);
                }
            }

            // Gallery Images
            if ($request->hasFile('gallery_images')) {
                foreach ($request->file('gallery_images') as $image) {
                    $path = $image->store('product_gallery', 'public');
                    $product->images()->create([
                        'image_path' => '/storage/' . $path
                    ]);
                }
            }
        });

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        return Inertia::render('Admin/Product/Edit', [
            'product' => $product->load('specs', 'codes', 'images'),
            'categories' => Category::all()
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'sku' => 'nullable|string|max:100|unique:products,sku,' . $product->id,
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'nullable|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'youtube_url' => 'nullable|string|url',
            'main_image' => 'nullable|image|max:2048',
            'gallery_images.*' => 'nullable|image|max:2048',
            'specs' => 'nullable|array',
            'codes' => 'nullable|array',
        ]);

        DB::transaction(function () use ($validated, $request, $product) {
            $updateData = [
                'name' => $validated['name'],
                'name_ar' => $validated['name_ar'],
                'sku' => $validated['sku'] ?? $product->sku,
                'description' => $validated['description_en'],
                'description_en' => $validated['description_en'],
                'description_ar' => $validated['description_ar'],
                'price' => $validated['price'],
                'cost_price' => $validated['cost_price'] ?? 0,
                'stock_quantity' => $validated['stock_quantity'] ?? 0,
                'category_id' => $validated['category_id'],
                'youtube_url' => $validated['youtube_url'],
            ];

            if ($request->hasFile('main_image')) {
                $imagePath = $request->file('main_image')->store('products', 'public');
                $updateData['image_url'] = '/storage/' . $imagePath;
            }

            $product->update($updateData);

            // Sync Specs (delete old, create new - simple approach)
            $product->specs()->delete();
            if (!empty($request->specs)) {
                foreach ($request->specs as $index => $spec) {
                    $product->specs()->create([
                        'label' => $spec['label'],
                        'value' => $spec['value'],
                        'sort' => $index
                    ]);
                }
            }

            // Sync Codes
            $product->codes()->delete();
            if (!empty($request->codes)) {
                foreach ($request->codes as $code) {
                    $product->codes()->create([
                        'title' => $code['title'] ?? null,
                        'code' => $code['code'],
                        'language' => $code['language']
                    ]);
                }
            }

            // Gallery Images (Add new ones)
            if ($request->hasFile('gallery_images')) {
                foreach ($request->file('gallery_images') as $image) {
                    $path = $image->store('product_gallery', 'public');
                    $product->images()->create([
                        'image_path' => '/storage/' . $path
                    ]);
                }
            }
        });

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->back();
    }
    
    // Helper to delete gallery image
    public function deleteImage($imageId)
    {
        $image = \App\Models\ProductImage::findOrFail($imageId);
        // Storage::disk('public')->delete(str_replace('/storage/', '', $image->image_path));
        $image->delete();
        return redirect()->back();
    }
}
