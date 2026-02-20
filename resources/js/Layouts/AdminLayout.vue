<script setup>
import { ref, onMounted } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import { cart } from '@/Stores/CartStore';
import { system } from '@/Stores/SystemStore';

const page = usePage();
const isSidebarOpen = ref(true);

const navItems = [
    { name: 'dashboard', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', route: 'admin.dashboard' },
    { name: 'products', icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', route: 'admin.products.index' },
    { name: 'categories', icon: 'M4 6h16M4 10h16M4 14h16M4 18h16', route: 'admin.categories.index' },
    { name: 'orders', icon: 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z', route: 'admin.orders.index' },
    { name: 'customers', icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z', route: 'admin.customers.index' },
    { name: 'lmsPoints', icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', route: 'lms-packages.index' },
    { name: 'privacyPolicy', icon: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', route: 'admin.content.pages.index', params: { slug: 'privacy' } },
    { name: 'termsOfService', icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', route: 'admin.content.pages.index', params: { slug: 'terms' } },
    { name: 'refundPolicy', icon: 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z', route: 'admin.content.pages.index', params: { slug: 'refund-policy' } },
    { name: 'contactInfo', icon: 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', route: 'admin.content.pages.index', params: { slug: 'contact' } },
    { name: 'analytics', icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', route: 'admin.analytics' },
    { name: 'users', icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z', route: 'admin.users.index' },
    { name: 'roles', icon: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', route: 'admin.roles.index' },
    { name: 'settings', icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z', route: 'admin.settings' },
];

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
            class="fixed inset-y-0 bg-white shadow-[20px_0_40px_rgba(0,0,0,0.02)] transition-all duration-500 z-50 overflow-y-auto"
            :class="[
                isSidebarOpen ? 'w-72' : 'w-24',
                cart.language === 'ar' ? 'right-0 border-l border-slate-100' : 'left-0 border-r border-slate-100'
            ]"
        >
            <div class="p-8 flex items-center justify-center">
                <Link href="/" class="flex flex-col items-center gap-2 overflow-hidden">
                    <img src="/images/logo.png" class="h-14 transition-all duration-500" :class="!isSidebarOpen && 'scale-75'">
                    <span v-if="isSidebarOpen" class="font-black text-xs tracking-[0.3em] text-[#222] uppercase mt-2">Thinker </span>
                </Link>
            </div>

            <nav class="mt-8 px-6 space-y-2">
                <Link 
                    v-for="item in navItems" 
                    :key="item.name"
                    :href="item.route ? route(item.route, item.params || {}) : '#'"
                    class="flex items-center gap-4 p-4 rounded-2xl transition-all duration-300 group"
                    :class="item.route && route().current(item.route, item.params || {}) ? 'bg-brand-50 text-brand-500 shadow-sm shadow-brand-500/10' : 'text-slate-400 hover:bg-slate-50 hover:text-[#222]'"
                >
                    <svg class="w-6 h-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" :d="item.icon" />
                    </svg>
                    <span v-if="isSidebarOpen" class="font-bold text-[13px] tracking-tight uppercase truncate">{{ cart.t(item.name) }}</span>
                </Link>
            </nav>

            <!-- User Profile (Stick to Bottom) -->
            <div class="absolute bottom-8 left-0 right-0 px-6">
                <div class="flex items-center gap-4 p-4 bg-slate-50/50 rounded-3xl border border-slate-100/50 hover:bg-white transition-all cursor-pointer group">
                    <div v-if="$page.props.auth.user" class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-brand-500 font-black shadow-sm group-hover:shadow-md transition-all">
                        {{ $page.props.auth.user.name.charAt(0) }}
                    </div>
                    <div v-if="isSidebarOpen" class="overflow-hidden">
                        <p class="text-[13px] font-black truncate text-[#222]">{{ $page.props.auth.user.name }}</p>
                        <p class="text-[9px] text-slate-400 font-black uppercase tracking-widest mt-0.5">{{ $page.props.auth.user.role || 'Admin Node' }}</p>
                    </div>
                </div>
            </div>
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
