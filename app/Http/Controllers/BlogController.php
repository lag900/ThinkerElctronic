<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Inertia\Inertia;

class BlogController extends Controller
{
    public function index()
    {
        return Inertia::render('Blog/Index', [
            'posts' => Post::where('status', 'published')->orderBy('created_at', 'desc')->get()
        ]);
    }

    public function show(Post $post)
    {
        if ($post->status !== 'published') abort(404);
        
        return Inertia::render('Blog/Show', [
            'post' => $post
        ]);
    }
}
