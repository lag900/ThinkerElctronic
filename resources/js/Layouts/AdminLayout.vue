<script setup>
import { ref, onMounted } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import { cart } from '@/Stores/CartStore';
import { system } from '@/Stores/SystemStore';

const page = usePage();
const isSidebarOpen = ref(true);

const changeLang = (lang) => {
    cart.language = lang;
    router.get(route('language.switch', { locale: lang }), {}, {
        preserveState: false,
        onSuccess: () => {
            cart.saveToStorage();
        }
    });
};

onMounted(() => {
    document.documentElement.dir = cart.language === 'ar' ? 'rtl' : 'ltr';
    document.documentElement.lang = cart.language;
});
</script>

<template>
    <div class="min-h-screen bg-[#FDFDFD] font-sans flex text-[#222]" :dir="cart.language === 'ar' ? 'rtl' : 'ltr'">
        <!-- Sidebar -->
        <aside 
            class="fixed inset-y-0 bg-white shadow-[20px_0_40px_rgba(0,0,0,0.02)] transition-all duration-500 z-50 flex flex-col overflow-y-auto"
            :class="[
                isSidebarOpen ? 'w-72' : 'w-24',
                cart.language === 'ar' ? 'right-0 border-l border-slate-100' : 'left-0 border-r border-slate-100'
            ]"
        >
            <div class="flex-none p-8 flex items-center justify-center">
                <Link href="/" class="flex flex-col items-center gap-2 overflow-hidden">
                    <img src="/images/logo.png" class="h-14 transition-all duration-500" :class="!isSidebarOpen && 'scale-75'">
                    <span v-if="isSidebarOpen" class="font-black text-xs tracking-[0.3em] text-[#222] uppercase mt-2">Thinker </span>
                </Link>
            </div>

            <div v-if="$page.props.auth.user" class="flex-none px-6 mb-6">
                <div class="flex items-center gap-4 p-4 bg-slate-50/50 rounded-3xl border border-slate-100/50 hover:bg-white transition-all cursor-pointer group">
                    <div class="w-12 h-12 shrink-0 bg-white rounded-2xl flex items-center justify-center text-brand-500 font-black shadow-sm group-hover:shadow-md transition-all">
                        {{ $page.props.auth.user.name.charAt(0) }}
                    </div>
                    <div v-if="isSidebarOpen" class="overflow-hidden">
                        <p class="text-[13px] font-black truncate text-[#222]">{{ $page.props.auth.user.name }}</p>
                        <p class="text-[9px] text-slate-400 font-black uppercase tracking-widest mt-0.5">{{ $page.props.auth.user.role || 'Admin Node' }}</p>
                    </div>
                </div>
            </div>

            <nav class="flex-1 px-4 space-y-8 pb-8 mt-2 overflow-y-auto custom-scrollbar">
                <!-- Main Core -->
                <div class="space-y-1">
                    <p v-if="isSidebarOpen" class="px-4 text-[10px] font-black uppercase tracking-widest text-slate-400 mb-3">Core Engine</p>
                    <Link 
                        :href="route('admin.dashboard')"
                        class="flex items-center gap-3 px-4 py-3 rounded-2xl transition-all duration-300 group relative overflow-hidden"
                        :class="route().current('admin.dashboard') ? 'bg-brand-50 text-brand-500 shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-brand-500'"
                    >
                        <div v-if="route().current('admin.dashboard')" class="absolute inset-y-0 w-[3px] bg-brand-500" :class="cart.language === 'ar' ? 'right-0' : 'left-0'"></div>
                        <svg class="w-5 h-5 shrink-0 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span v-if="isSidebarOpen" class="font-bold text-xs tracking-tight uppercase truncate">Dashboard</span>
                    </Link>
                    <Link 
                        :href="route('admin.pos.index')"
                        class="flex items-center gap-3 px-4 py-3 rounded-2xl transition-all duration-300 group relative overflow-hidden"
                        :class="route().current('admin.pos.index') ? 'bg-brand-50 text-brand-500 shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-brand-500'"
                    >
                        <div v-if="route().current('admin.pos.index')" class="absolute inset-y-0 w-[3px] bg-brand-500" :class="cart.language === 'ar' ? 'right-0' : 'left-0'"></div>
                        <svg class="w-5 h-5 shrink-0 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        <span v-if="isSidebarOpen" class="font-bold text-xs tracking-tight uppercase truncate">POS / Sales</span>
                    </Link>
                    <Link 
                        :href="route('admin.products.index')"
                        class="flex items-center gap-3 px-4 py-3 rounded-2xl transition-all duration-300 group relative overflow-hidden"
                        :class="route().current('admin.products.*') ? 'bg-brand-50 text-brand-500 shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-brand-500'"
                    >
                        <div v-if="route().current('admin.products.*')" class="absolute inset-y-0 w-[3px] bg-brand-500" :class="cart.language === 'ar' ? 'right-0' : 'left-0'"></div>
                        <svg class="w-5 h-5 shrink-0 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        <span v-if="isSidebarOpen" class="font-bold text-xs tracking-tight uppercase truncate">Products</span>
                    </Link>
                    <Link 
                        :href="route('admin.categories.index')"
                        class="flex items-center gap-3 px-4 py-3 rounded-2xl transition-all duration-300 group relative overflow-hidden"
                        :class="route().current('admin.categories.*') ? 'bg-brand-50 text-brand-500 shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-brand-500'"
                    >
                        <div v-if="route().current('admin.categories.*')" class="absolute inset-y-0 w-[3px] bg-brand-500" :class="cart.language === 'ar' ? 'right-0' : 'left-0'"></div>
                        <svg class="w-5 h-5 shrink-0 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                        <span v-if="isSidebarOpen" class="font-bold text-xs tracking-tight uppercase truncate">Categories</span>
                    </Link>
                    <Link 
                        :href="route('admin.orders.index')"
                        class="flex items-center gap-3 px-4 py-3 rounded-2xl transition-all duration-300 group relative overflow-hidden"
                        :class="route().current('admin.orders.*') ? 'bg-brand-50 text-brand-500 shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-brand-500'"
                    >
                        <div v-if="route().current('admin.orders.*')" class="absolute inset-y-0 w-[3px] bg-brand-500" :class="cart.language === 'ar' ? 'right-0' : 'left-0'"></div>
                        <svg class="w-5 h-5 shrink-0 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        <span v-if="isSidebarOpen" class="font-bold text-xs tracking-tight uppercase truncate">Orders</span>
                    </Link>
                    <Link 
                        :href="route('admin.customers.index')"
                        class="flex items-center gap-3 px-4 py-3 rounded-2xl transition-all duration-300 group relative overflow-hidden"
                        :class="route().current('admin.customers.*') ? 'bg-brand-50 text-brand-500 shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-brand-500'"
                    >
                        <div v-if="route().current('admin.customers.*')" class="absolute inset-y-0 w-[3px] bg-brand-500" :class="cart.language === 'ar' ? 'right-0' : 'left-0'"></div>
                        <svg class="w-5 h-5 shrink-0 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <span v-if="isSidebarOpen" class="font-bold text-xs tracking-tight uppercase truncate">Customers</span>
                    </Link>
                    <Link 
                        :href="route('admin.expenses.index')"
                        class="flex items-center gap-3 px-4 py-3 rounded-2xl transition-all duration-300 group relative overflow-hidden"
                        :class="route().current('admin.expenses.*') ? 'bg-brand-50 text-brand-500 shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-brand-500'"
                    >
                        <div v-if="route().current('admin.expenses.*')" class="absolute inset-y-0 w-[3px] bg-brand-500" :class="cart.language === 'ar' ? 'right-0' : 'left-0'"></div>
                        <svg class="w-5 h-5 shrink-0 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span v-if="isSidebarOpen" class="font-bold text-xs tracking-tight uppercase truncate">Expenses</span>
                    </Link>
                </div>
                <!-- Delivery Control System -->
                <div class="space-y-1">
                    <p v-if="isSidebarOpen" class="px-4 text-[10px] font-black uppercase tracking-widest text-slate-400 mb-3">Delivery Control</p>
                    <Link 
                        :href="route('admin.governorates.index')"
                        class="flex items-center gap-3 px-4 py-3 rounded-2xl transition-all duration-300 group relative overflow-hidden"
                        :class="route().current('admin.governorates.*') ? 'bg-brand-50 text-brand-500 shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-brand-500'"
                    >
                        <div v-if="route().current('admin.governorates.*')" class="absolute inset-y-0 w-[3px] bg-brand-500" :class="cart.language === 'ar' ? 'right-0' : 'left-0'"></div>
                        <svg class="w-5 h-5 shrink-0 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                        <span v-if="isSidebarOpen" class="font-bold text-xs tracking-tight uppercase truncate">Governorates</span>
                    </Link>
                    <Link 
                        :href="route('admin.areas.index')"
                        class="flex items-center gap-3 px-4 py-3 rounded-2xl transition-all duration-300 group relative overflow-hidden"
                        :class="route().current('admin.areas.*') ? 'bg-brand-50 text-brand-500 shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-brand-500'"
                    >
                        <div v-if="route().current('admin.areas.*')" class="absolute inset-y-0 w-[3px] bg-brand-500" :class="cart.language === 'ar' ? 'right-0' : 'left-0'"></div>
                        <svg class="w-5 h-5 shrink-0 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        <span v-if="isSidebarOpen" class="font-bold text-xs tracking-tight uppercase truncate">Areas & Pricing</span>
                    </Link>
                </div>


                <!-- Secondary Intelligence -->
                <div class="space-y-1">
                    <p v-if="isSidebarOpen" class="px-4 text-[10px] font-black uppercase tracking-widest text-slate-400 mb-3">Intelligence</p>
                    <Link 
                        :href="route('admin.analytics')"
                        class="flex items-center gap-3 px-4 py-3 rounded-2xl transition-all duration-300 group relative overflow-hidden"
                        :class="route().current('admin.analytics') ? 'bg-brand-50 text-brand-500 shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-brand-500'"
                    >
                        <div v-if="route().current('admin.analytics')" class="absolute inset-y-0 w-[3px] bg-brand-500" :class="cart.language === 'ar' ? 'right-0' : 'left-0'"></div>
                        <svg class="w-5 h-5 shrink-0 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        <span v-if="isSidebarOpen" class="font-bold text-xs tracking-tight uppercase truncate">Analytics</span>
                    </Link>
                    <Link 
                        :href="route('admin.sales-cash-control')"
                        class="flex items-center gap-3 px-4 py-3 rounded-2xl transition-all duration-300 group relative overflow-hidden"
                        :class="route().current('admin.sales-cash-control') ? 'bg-brand-50 text-brand-500 shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-brand-500'"
                    >
                        <div v-if="route().current('admin.sales-cash-control')" class="absolute inset-y-0 w-[3px] bg-brand-500" :class="cart.language === 'ar' ? 'right-0' : 'left-0'"></div>
                        <svg class="w-5 h-5 shrink-0 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span v-if="isSidebarOpen" class="font-bold text-xs tracking-tight uppercase truncate">Cash Control</span>
                    </Link>
                    <Link 
                        :href="route('admin.qr.index')"
                        class="flex items-center gap-3 px-4 py-3 rounded-2xl transition-all duration-300 group relative overflow-hidden"
                        :class="route().current('admin.qr.*') ? 'bg-brand-50 text-brand-500 shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-brand-500'"
                    >
                        <div v-if="route().current('admin.qr.*')" class="absolute inset-y-0 w-[3px] bg-brand-500" :class="cart.language === 'ar' ? 'right-0' : 'left-0'"></div>
                        <svg class="w-5 h-5 shrink-0 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M4 20h4m12-4h.01M4 16h4m12-12h.01M4 4h4m12 0h.01M4 8h4m12 4h.01M4 16h4"/></svg>
                        <span v-if="isSidebarOpen" class="font-bold text-xs tracking-tight uppercase truncate">QR System</span>
                    </Link>
                    <Link 
                        :href="route('lms-packages.index')"
                        class="flex items-center gap-3 px-4 py-3 rounded-2xl transition-all duration-300 group relative overflow-hidden"
                        :class="route().current('lms-packages.*') ? 'bg-brand-50 text-brand-500 shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-brand-500'"
                    >
                        <div v-if="route().current('lms-packages.*')" class="absolute inset-y-0 w-[3px] bg-brand-500" :class="cart.language === 'ar' ? 'right-0' : 'left-0'"></div>
                        <svg class="w-5 h-5 shrink-0 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span v-if="isSidebarOpen" class="font-bold text-xs tracking-tight uppercase truncate">LMS Points</span>
                    </Link>
                </div>

                <!-- CMS & Legal Options -->
                <div class="space-y-1">
                    <p v-if="isSidebarOpen" class="px-4 text-[10px] font-black uppercase tracking-widest text-slate-400 mb-3">Content & Legal</p>
                    <Link 
                        :href="route('admin.home-content.index')"
                        class="flex items-center gap-3 px-4 py-3 rounded-2xl transition-all duration-300 group relative overflow-hidden"
                        :class="route().current('admin.home-content.*') ? 'bg-brand-50 text-brand-500 shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-brand-500'"
                    >
                        <div v-if="route().current('admin.home-content.*')" class="absolute inset-y-0 w-[3px] bg-brand-500" :class="cart.language === 'ar' ? 'right-0' : 'left-0'"></div>
                        <svg class="w-5 h-5 shrink-0 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/></svg>
                        <span v-if="isSidebarOpen" class="font-bold text-xs tracking-tight uppercase truncate">Home Content</span>
                    </Link>
                    
                    <Link 
                        :href="route('admin.posts.index')"
                        class="flex items-center gap-3 px-4 py-3 rounded-2xl transition-all duration-300 group relative overflow-hidden"
                        :class="route().current('admin.posts.*') ? 'bg-brand-50 text-brand-500 shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-brand-500'"
                    >
                        <div v-if="route().current('admin.posts.*')" class="absolute inset-y-0 w-[3px] bg-brand-500" :class="cart.language === 'ar' ? 'right-0' : 'left-0'"></div>
                        <svg class="w-5 h-5 shrink-0 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                        <span v-if="isSidebarOpen" class="font-bold text-xs tracking-tight uppercase truncate">Blog / News</span>
                    </Link>
                    <Link 
                        :href="route('admin.seo.settings')"
                        class="flex items-center gap-3 px-4 py-3 rounded-2xl transition-all duration-300 group relative overflow-hidden"
                        :class="route().current('admin.seo.settings') ? 'bg-brand-50 text-brand-500 shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-brand-500'"
                    >
                        <div v-if="route().current('admin.seo.settings')" class="absolute inset-y-0 w-[3px] bg-brand-500" :class="cart.language === 'ar' ? 'right-0' : 'left-0'"></div>
                        <svg class="w-5 h-5 shrink-0 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        <span v-if="isSidebarOpen" class="font-bold text-xs tracking-tight uppercase truncate">SEO Optimization</span>
                    </Link>
                    
                    <!-- Collapsible Legal Panel -->
                    <details class="group [&_summary::-webkit-details-marker]:hidden">
                        <summary class="flex items-center gap-3 px-4 py-3 rounded-2xl transition-all duration-300 cursor-pointer text-slate-500 hover:bg-slate-50 hover:text-[#222] list-none select-none">
                            <svg class="w-5 h-5 shrink-0 transition-transform group-hover:scale-110 group-open:text-brand-500 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            <span v-if="isSidebarOpen" class="font-bold text-xs tracking-tight uppercase truncate flex-1 group-open:text-brand-500">Legal & Policies</span>
                            <span v-if="isSidebarOpen" class="shrink-0 transition-transform duration-300 group-open:rotate-180 text-brand-500">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg>
                            </span>
                        </summary>
                        <div v-if="isSidebarOpen" class="pl-12 pr-4 pt-2 space-y-1 mb-2 overflow-hidden animate-in fade-in slide-in-from-top-2 duration-300">
                            <Link :href="route('admin.content.pages.index', {slug: 'privacy'})" class="block py-2 text-[11px] font-bold uppercase tracking-widest text-slate-400 hover:text-brand-500 transition-colors">Privacy</Link>
                            <Link :href="route('admin.content.pages.index', {slug: 'terms'})" class="block py-2 text-[11px] font-bold uppercase tracking-widest text-slate-400 hover:text-brand-500 transition-colors">Terms</Link>
                            <Link :href="route('admin.content.pages.index', {slug: 'refund-policy'})" class="block py-2 text-[11px] font-bold uppercase tracking-widest text-slate-400 hover:text-brand-500 transition-colors">Refund Policy</Link>
                            <Link :href="route('admin.content.pages.index', {slug: 'contact'})" class="block py-2 text-[11px] font-bold uppercase tracking-widest text-slate-400 hover:text-brand-500 transition-colors">Contact Info</Link>
                        </div>
                    </details>
                </div>

                <!-- Systems Matrix -->
                <div class="space-y-1 border-t border-slate-100/60 pt-6 mt-6">
                    <p v-if="isSidebarOpen" class="px-4 text-[10px] font-black uppercase tracking-widest text-slate-400 mb-3">System Matrix</p>
                    <Link 
                        :href="route('admin.users.index')"
                        class="flex items-center gap-3 px-4 py-3 rounded-2xl transition-all duration-300 group relative overflow-hidden"
                        :class="route().current('admin.users.*') ? 'bg-brand-50 text-brand-500 shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-brand-500'"
                    >
                        <div v-if="route().current('admin.users.*')" class="absolute inset-y-0 w-[3px] bg-brand-500" :class="cart.language === 'ar' ? 'right-0' : 'left-0'"></div>
                        <svg class="w-5 h-5 shrink-0 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <span v-if="isSidebarOpen" class="font-bold text-xs tracking-tight uppercase truncate">Users Manager</span>
                    </Link>
                    <Link 
                        :href="route('admin.roles.index')"
                        class="flex items-center gap-3 px-4 py-3 rounded-2xl transition-all duration-300 group relative overflow-hidden"
                        :class="route().current('admin.roles.*') ? 'bg-brand-50 text-brand-500 shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-brand-500'"
                    >
                        <div v-if="route().current('admin.roles.*')" class="absolute inset-y-0 w-[3px] bg-brand-500" :class="cart.language === 'ar' ? 'right-0' : 'left-0'"></div>
                        <svg class="w-5 h-5 shrink-0 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span v-if="isSidebarOpen" class="font-bold text-xs tracking-tight uppercase truncate">Access Roles</span>
                    </Link>

                </div>
            </nav>
        </aside>

        <!-- Main Content Area -->
        <main 
            class="flex-1 transition-all duration-500 min-w-0"
            :class="isSidebarOpen ? (cart.language === 'ar' ? 'mr-72' : 'ml-72') : (cart.language === 'ar' ? 'mr-24' : 'ml-24')"
        >
            <!-- Top Navbar -->
            <header class="sticky top-0 z-40 bg-white/90 backdrop-blur-xl border-b border-slate-100/50 px-10 h-24 flex items-center justify-between">
                <div class="flex items-center gap-6">
                    <button @click="isSidebarOpen = !isSidebarOpen" class="p-3 bg-slate-50 hover:bg-white rounded-2xl text-slate-400 hover:text-brand-500 transition-all shadow-sm border border-slate-100/50">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" :class="!isSidebarOpen && 'rotate-180'"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16" /></svg>
                    </button>
                    <h2 class="text-xs font-black tracking-[0.2em] text-[#222] uppercase">
                        <slot name="header">{{ cart.t('welcome') }}</slot>
                    </h2>
                </div>

                <div class="flex items-center gap-8">
                    <!-- Language Selection -->
                    <div class="flex items-center bg-slate-50 border border-slate-100/50 rounded-2xl p-1.5">
                        <button 
                            @click="changeLang('en')"
                            class="px-5 py-2 text-[10px] font-black uppercase tracking-widest rounded-xl transition-all"
                            :class="cart.language === 'en' ? 'bg-white text-brand-500 shadow-sm' : 'text-slate-400 hover:text-slate-900'"
                        >EN</button>
                        <button 
                            @click="changeLang('ar')"
                            class="px-5 py-2 text-[10px] font-black uppercase tracking-widest rounded-xl transition-all"
                            :class="cart.language === 'ar' ? 'bg-white text-brand-500 shadow-sm' : 'text-slate-400 hover:text-slate-900'"
                        >AR</button>
                    </div>

                    <Link :href="route('shop')" target="_blank" class="text-[10px] font-black uppercase tracking-widest px-6 py-3 bg-brand-500 text-white rounded-2xl hover:bg-brand-600 transition-all shadow-lg shadow-brand-500/20 active:scale-95">
                        {{ cart.t('viewStore') }}
                    </Link>

                    <Link :href="route('logout')" method="post" as="button" class="text-[10px] font-black uppercase tracking-widest text-[#222] hover:text-brand-500 transition-all font-bold">
                        {{ cart.t('logout') }}
                    </Link>
                </div>
            </header>

            <!-- Page Content -->
            <div class="p-12">
                <slot />
            </div>
        </main>

        <!-- Global Enterprise Toast Notification System -->
        <div class="fixed bottom-10 right-10 z-[100] space-y-4 max-w-sm pointer-events-none" :dir="cart.language === 'ar' ? 'rtl' : 'ltr'">
            <TransitionGroup name="toast">
                <div 
                    v-for="n in system.notifications" 
                    :key="n.id"
                    class="p-6 rounded-[32px] shadow-[0_20px_50px_rgba(0,0,0,0.1)] border flex items-center gap-4 pointer-events-auto backdrop-blur-xl animate-slide-in"
                    :class="n.type === 'error' ? 'bg-red-50/90 border-red-100 text-red-600' : 'bg-white/90 border-slate-100 text-[#222]'"
                >
                    <div class="w-10 h-10 rounded-2xl flex items-center justify-center shrink-0 shadow-sm" :class="n.type === 'error' ? 'bg-red-500 text-white' : 'bg-brand-500 text-white'">
                        <svg v-if="n.type !== 'error'" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                        <svg v-else class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </div>
                    <p class="text-sm font-black tracking-tight leading-tight">{{ n.message }}</p>
                </div>
            </TransitionGroup>
        </div>

        <!-- Global Async Global Processing Loader (Hidden Logic) -->
        <div v-if="system.isLoading" class="fixed top-0 left-0 right-0 h-1 bg-brand-500/20 z-[200]">
            <div class="h-full bg-brand-500 animate-[loading_2s_infinite_linear] origin-left shadow-[0_0_10px_#ff2d55]"></div>
        </div>
    </div>
</template>

<style>
@keyframes loading {
    0% { transform: scaleX(0); }
    50% { transform: scaleX(0.5); }
    100% { transform: scaleX(1); }
}

.toast-enter-active, .toast-leave-active {
    transition: all 0.5s cubic-bezier(0.19, 1, 0.22, 1);
}
.toast-enter-from { opacity: 0; transform: translateY(20px) scale(0.9); }
.toast-leave-to { opacity: 0; transform: translateX(50px) scale(0.9); }

@keyframes slide-in {
    from { transform: translateY(20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}
</style>
