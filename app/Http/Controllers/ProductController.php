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
        $lmsPackages = LmsPointPackage::orderBy('sort_order')->orderBy('points')->get()
            ->map(fn ($pkg) => [
                'id' => $pkg->id,
                'points' => $pkg->points,
                'price' => $pkg->price,
                'paymentLink' => $pkg->payment_link,
            ]);

        return Inertia::render('Welcome', [
            'products' => Product::with('category')->take(3)->get(),
            'categories' => Category::withCount('products')->where('show_on_homepage', true)->get(),
            'lmsPackages' => $lmsPackages,
        ]);
    }

    public function shop(Request $request)
    {
        $query = Product::with('category');

        if ($request->has('category')) {
            $query->where('category_id', $request->category);
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
}
