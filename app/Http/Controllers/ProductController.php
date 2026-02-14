<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;
use App\Models\Category;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index()
    {
        return Inertia::render('Welcome', [
            'products' => Product::with('category')->take(3)->get(),
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
}
