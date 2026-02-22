<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;
use App\Models\Category;
use App\Models\LmsPointPackage;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $homeContent = \App\Models\Setting::where('key', 'home_content')->first();
        $homeData = $homeContent ? json_decode($homeContent->value, true) : null;

        return Inertia::render('Welcome', [
            'products' => Product::with('category')->take(4)->get(),
            'categories' => Category::where('is_active', true)
                ->where('show_on_homepage', true)
                ->whereNull('parent_id')
                ->with(['children' => function($q) {
                    $q->where('is_active', true)->orderBy('sort_order');
                }])
                ->withCount('products')
                ->orderBy('sort_order')
                ->orderBy('created_at', 'desc')
                ->get(),
            'homeData' => $homeData,
        ]);
    }

    public function shop(Request $request)
    {
        $query = Product::with('category');

        if ($request->has('category')) {
            $cat = $request->category;
            $query->whereHas('category', function($q) use ($cat) {
                $q->where('id', $cat)->orWhere('slug', $cat);
            });
        }

        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        return Inertia::render('Shop', [
            'products' => $query->get(),
            'categories' => Category::all(),
            'filters' => $request->only(['category', 'min_price', 'max_price']),
        ]);
    }

    public function show(Product $product)
    {
        return Inertia::render('Product/Show', [
            'product' => $product->load('category', 'specs', 'images', 'codes'),
            'relatedProducts' => Product::with('category')
                ->where('category_id', $product->category_id)
                ->where('id', '!=', $product->id)
                ->take(4)
                ->get(),
        ]);
    }

    public function liveSearch(Request $request)
    {
        $query = $request->input('q');
        
        if (empty($query)) {
            return response()->json([]);
        }

        $products = Product::with('category')
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('name_ar', 'like', "%{$query}%")
                  ->orWhere('sku', 'like', "%{$query}%");
            })
            ->take(8)
            ->get()
            ->map(function ($product) {
                $isAr = session()->get('locale') === 'ar';
                return [
                    'id' => $product->id,
                    'name' => $isAr ? ($product->name_ar ?: $product->name) : $product->name,
                    'price' => $product->price,
                    'image' => $product->image_url ?: '/images/placeholder.png',
                    'stock' => $product->stock_quantity,
                    'category' => $product->category ? ($isAr ? ($product->category->name_ar ?: $product->category->name) : $product->category->name) : null,
                ];
            });

        return response()->json($products);
    }
}
