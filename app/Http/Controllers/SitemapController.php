<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index()
    {
        $products = Product::where('is_active', true)->get();
        $categories = Category::where('is_active', true)->get();
        $posts = Post::where('status', 'published')->get();

        $content = view('sitemap', [
            'products' => $products,
            'categories' => $categories,
            'posts' => $posts,
        ]);

        return response($content, 200)
            ->header('Content-Type', 'text/xml');
    }

    public function robots()
    {
        $sitemapUrl = url('sitemap.xml');
        $content = "User-agent: *\nAllow: /\n\nSitemap: {$sitemapUrl}";
        
        return response($content, 200)
            ->header('Content-Type', 'text/plain');
    }
}
