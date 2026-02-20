<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Inertia\Inertia;

class PageController extends Controller
{
    public function show($slug)
    {
        $page = Page::where('slug', $slug)->firstOrFail();
        
        $locale = session('locale', config('app.locale'));
        
        return Inertia::render(ucfirst(str_replace(' ', '', ucwords(str_replace('-', ' ', $slug)))), [
            'page' => $page,
        ]);
    }
    
    public function privacy() { return $this->show('privacy'); }
    public function terms() { return $this->show('terms'); }
    public function refundPolicy() { return $this->show('refund-policy'); }
    public function contact() { return $this->show('contact'); }
}
