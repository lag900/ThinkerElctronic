<script setup>
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { cart } from '@/Stores/CartStore';

const isSidebarOpen = ref(true);
</script>

<template>
    <div class="min-h-screen bg-slate-50 font-sans flex text-slate-900">
        <!-- Sidebar -->
        <aside 
            class="fixed inset-y-0 left-0 bg-white border-r border-slate-200 transition-all duration-300 z-50 overflow-y-auto"
            :class="isSidebarOpen ? 'w-64' : 'w-20'"
        >
            <div class="p-8 flex items-center justify-between">
                <Link href="/" class="flex items-center gap-3 overflow-hidden">
                    <div class="w-10 h-10 bg-brand-500 rounded-xl flex items-center justify-center shrink-0 shadow-lg shadow-brand-500/20">
                        <span class="text-white font-black italic">T</span>
                    </div>
                    <span v-if="isSidebarOpen" class="font-black text-xl tracking-tighter">THINKER</span>
                </Link>
            </div>

            <nav class="mt-10 px-6 space-y-4">
                <Link 
                    v-for="item in [
                        { name: 'My Profile', icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', route: 'dashboard' },
                        { name: 'Order History', icon: 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z', route: 'customer.orders' },
                        { name: 'My Wishlist', icon: 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z', route: 'customer.wishlist' },
                        { name: 'My Addresses', icon: 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z', route: 'customer.addresses' },
                    ]" 
                    :key="item.name"
                    :href="route(item.route)"
                    class="flex items-center gap-4 p-4 rounded-2xl transition-all group"
                    :class="route().current(item.route) ? 'bg-brand-50 text-brand-500 shadow-sm' : 'text-slate-400 hover:bg-slate-50 hover:text-slate-900'"
                >
                    <svg class="w-6 h-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" :d="item.icon" />
                    </svg>
                    <span v-if="isSidebarOpen" class="font-bold text-sm tracking-tight capitalize">{{ item.name }}</span>
                </Link>
            </nav>

            <!-- Quick Cart Access -->
            <div v-if="isSidebarOpen" class="mt-10 px-8">
                <div class="bg-brand-500 rounded-[32px] p-6 text-white shadow-xl shadow-brand-500/20">
                    <p class="text-[10px] font-black uppercase tracking-widest opacity-60 mb-2">Active Cart</p>
                    <h4 class="text-2xl font-black tracking-tight mb-6">{{ cart.count }} Items</h4>
                    <Link href="/checkout" class="block w-full py-3 bg-white text-brand-500 rounded-xl text-center text-[10px] font-black uppercase tracking-widest hover:bg-brand-50 transition-all font-sans">
                        Checkout Now
                    </Link>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main 
            class="flex-1 transition-all duration-300"
            :class="isSidebarOpen ? 'ml-64' : 'ml-20'"
        >
            <!-- Top Navbar -->
            <header class="sticky top-0 z-40 bg-white/80 backdrop-blur-md border-b border-slate-200 px-10 h-24 flex items-center justify-between">
                <div>
                    <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Human Protocol</h3>
                    <h2 class="text-xl font-black tracking-tight text-slate-900 uppercase">
                        Welcome, {{ $page.props.auth.user.name.split(' ')[0] }}
                    </h2>
                </div>

                <div class="flex items-center gap-6">
                    <button @click="cart.toggleLanguage()" class="hidden md:block text-[10px] font-black uppercase tracking-widest px-5 py-3 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 transition-all">
                        {{ cart.language === 'en' ? 'Arabic' : 'English' }}
                    </button>

                    <Link :href="route('logout')" method="post" as="button" class="w-12 h-12 flex items-center justify-center bg-slate-100 rounded-2xl text-slate-400 hover:text-red-500 transition-all">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-6 0v-1m6 0H9" /></svg>
                    </Link>
                </div>
            </header>

            <!-- Page Content -->
            <div class="p-10 max-w-6xl mx-auto">
                <slot />
            </div>
        </main>
    </div>
</template>
