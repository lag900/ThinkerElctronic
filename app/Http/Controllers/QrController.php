<?php

namespace App\Http\Controllers;

use App\Models\QrLink;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class QrController extends Controller
{
    public function index()
    {
        $links = QrLink::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(function($link) {
                if ($link->custom_icon) {
                    $link->icon_url = asset('storage/' . $link->custom_icon);
                }
                return $link;
            });

        $logo = Setting::get('qr_logo');

        return Inertia::render('Public/Qr', [
            'links' => $links,
            'logo' => $logo ? asset('storage/' . $logo) : asset('images/logo.png'),
        ]);
    }

    public function click(QrLink $link)
    {
        $link->increment('clicks');
        $link->update(['last_clicked_at' => Carbon::now()]);
        return redirect()->away($link->url);
    }
}
