<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>شكراً لطلبك - Thinker Electronics</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700;900&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#fff1f2',
                            500: '#ff2d55',
                            600: '#e11d48',
                        },
                        ink: {
                            main: '#0a0a0a',
                            muted: '#a3a3a3',
                        },
                        surface: {
                            50: '#f8f9fa',
                        }
                    },
                    fontFamily: {
                        cairo: ['Cairo', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Cairo', sans-serif; }
    </style>
    <script>
        // Clear cart from local storage after successful redirect
        localStorage.removeItem('thinker_cart');
    </script>
</head>
<body class="bg-surface-50 antialiased text-ink-main">

    <div class="min-h-screen flex flex-col items-center justify-center p-6">
        
        <!-- Logo -->
        <div class="mb-12">
            <img src="/images/logo.png" alt="Thinker" class="h-16 w-auto">
        </div>

        <!-- Success Message -->
        <div class="max-w-4xl w-full bg-white rounded-[3rem] shadow-2xl border border-gray-100 overflow-hidden transform transition-all">
            
            <div class="p-10 md:p-16 text-center border-b border-gray-50">
                <div class="inline-flex items-center justify-center w-24 h-24 bg-brand-50 rounded-full mb-8">
                    <svg class="w-12 h-12 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h1 class="text-4xl md:text-5xl font-black mb-4 tracking-tight">تم استلام طلبك بنجاح!</h1>
                <p class="text-xl text-ink-muted font-bold opacity-60">شكراً لثقتك بـ Thinker Electronics. طلبك رقم <span class="text-brand-500">#{{ $order->order_number }}</span> تحت المراجعة الآن.</p>
            </div>

            <!-- Order Details Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 divide-x divide-x-reverse divide-gray-50 bg-gray-50/30">
                <div class="p-8 text-center">
                    <span class="block text-[10px] font-black uppercase tracking-widest text-ink-muted mb-2">رقم الطلب</span>
                    <span class="text-base font-black">#{{ $order->order_number }}</span>
                </div>
                <div class="p-8 text-center">
                    <span class="block text-[10px] font-black uppercase tracking-widest text-ink-muted mb-2">التاريخ</span>
                    <span class="text-base font-black">{{ $order->created_at->format('d M, Y') }}</span>
                </div>
                <div class="p-8 text-center">
                    <span class="block text-[10px] font-black uppercase tracking-widest text-ink-muted mb-2">طريقة الدفع</span>
                    <span class="text-base font-black uppercase">{{ $order->payment_method }}</span>
                </div>
                <div class="p-8 text-center">
                    <span class="block text-[10px] font-black uppercase tracking-widest text-ink-muted mb-2">الإجمالي</span>
                    <span class="text-base font-black text-brand-500">{{ number_format($order->total, 2) }} EGP</span>
                </div>
            </div>

            <!-- Products Table -->
            <div class="p-10 md:p-16">
                <h2 class="text-2xl font-black mb-8 border-r-4 border-brand-500 pr-4">تفاصيل المنتجات</h2>
                <div class="space-y-6">
                    @foreach($order->items as $item)
                    <div class="flex items-center gap-6 group p-4 hover:bg-surface-50 rounded-3xl transition-colors">
                        <div class="w-20 h-20 bg-surface-50 rounded-2xl overflow-hidden border border-gray-100 shrink-0">
                            <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-black leading-tight mb-1">{{ $item->product->name }}</h3>
                            <p class="text-xs font-bold text-ink-muted uppercase tracking-widest">كمية: {{ $item->quantity }}</p>
                        </div>
                        <div class="text-left">
                            <p class="text-lg font-black text-ink-main">{{ number_format($item->price * $item->quantity, 2) }} EGP</p>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Footer Actions -->
                <div class="mt-16 pt-10 border-t border-gray-50 flex flex-col md:flex-row items-center justify-between gap-8">
                    <a href="{{ route('order.invoice', $order->order_number) }}" target="_blank"
                       class="w-full md:w-auto inline-flex items-center justify-center gap-4 bg-ink-main text-white px-10 py-6 rounded-3xl font-black text-sm uppercase tracking-widest hover:bg-brand-500 hover:shadow-2xl transition-all active:scale-95 group">
                        <svg class="w-5 h-5 group-hover:animate-bounce" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        <span>تحميل الفاتورة (PDF)</span>
                    </a>

                    <a href="{{ route('home') }}" class="text-sm font-black uppercase tracking-widest text-ink-muted hover:text-brand-500 transition-colors">
                        العودة للمتجر
                    </a>
                </div>
            </div>
        </div>

        <p class="mt-12 text-center text-xs font-bold text-ink-muted opacity-40 uppercase tracking-[0.4em]">Thinker Electronics - Built for Innovation</p>
    </div>

</body>
</html>
