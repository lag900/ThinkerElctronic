<?php

namespace App\Services;

use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Str;

class InvoiceNumberGenerator
{
    /**
     * Generate a unique invoice number.
     * Format: ORD-LHQJVO-202602200035 style
     * 
     * @return string
     */
    public static function generate(): string
    {
        $random = strtoupper(Str::random(6));
        $date = Carbon::now()->format('Ymd');
        $time = Carbon::now()->format('Hi');
        $seconds = Carbon::now()->format('s');
        
        // Structure: ORD-{RANDOM}-{DATE}{TIME}{SECONDS}
        return "ORD-{$random}-{$date}{$time}{$seconds}";
    }
}
