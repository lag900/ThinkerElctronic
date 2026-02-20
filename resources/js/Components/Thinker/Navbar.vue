<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';
import { cart } from '@/Stores/CartStore';

const props = defineProps({
    transparent: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['open-cart']);
const isScrolled = ref(false);
const isMobileMenuOpen = ref(false);

const handleScroll = () => {
    isScrolled.value = window.scrollY > 50;
};

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
});

const navLinks = [
    { name: 'home', route: 'home' },
    { name: 'shop', route: 'shop' },
    { name: 'printing', route: 'home' },
    { name: 'solutions', route: 'home' },
];
</script>

<template>
    <nav 
        class="fixed top-0 inset-x-0 z-[100] transition-all duration-300 h-24 flex items-center"
        :class="[
            isScrolled ? 'bg-white/95 backdrop-blur-md shadow-sm border-b border-gray-100' : (transparent ? 'bg-transparent' : 'bg-white'),
            cart.language === 'ar' ? 'rtl' : 'ltr'
        ]"
    >
        <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 flex items-center justify-between">
            <!-- Brand -->
            <Link href="/" class="flex items-center gap-2 group">
                <img src="/images/logo.png" class="h-12 sm:h-14 w-auto transition-transform duration-300 group-hover:scale-105">
                <div class="hidden sm:flex flex-col">
                    <span class="text-xl font-black tracking-tighter text-black uppercase leading-none">Thinker</span>
                    <span class="text-[8px] font-bold text-[#ff2d55] uppercase mt-1 tracking-widest leading-none">Intelligence</span>
                </div>
            </Link>

            <!-- Navigation Links -->
            <div class="hidden lg:flex items-center gap-10">
                <Link 
                    v-for="link in navLinks" 
                    :key="link.name" 
                    :href="route(link.route)"
                    class="text-[11px] font-bold uppercase tracking-widest text-gray-500 hover:text-[#ff2d55] transition-colors"
                >
                    {{ cart.t(link.name) }}
                </Link>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-4 sm:gap-6">
                <!-- Language Switcher -->
                <button 
                    @click="cart.toggleLanguage"
                    class="text-[11px] font-bold text-black border-2 border-gray-100 rounded-xl px-3 py-2 hover:bg-gray-50 transition-colors"
                >
                    {{ cart.language === 'en' ? 'AR' : 'EN' }}
                </button>

                <!-- Cart Trigger -->
                <button 
                    @click="$emit('open-cart')"
                    class="relative p-2 text-black hover:text-[#ff2d55] transition-colors"
                >
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <span v-if="cart.count > 0" class="absolute top-0 right-0 w-5 h-5 bg-[#ff2d55] text-white text-[10px] font-bold rounded-full flex items-center justify-center border-2 border-white">
                        {{ cart.count }}
                    </span>
                </button>

                <!-- Auth Scenarios -->
                <template v-if="$page.props.auth.user">
                    <div class="flex items-center gap-4">
                        <!-- Admin Dashboard Node -->
                        <Link 
                            v-if="$page.props.auth.user.is_admin"
                            :href="route('admin.dashboard')"
                            class="hidden md:block text-[10px] font-black uppercase tracking-widest px-6 py-2.5 bg-[#222] text-white rounded-xl hover:bg-brand-500 transition-all shadow-lg shadow-black/5"
                        >
                            {{ cart.t('dashboard') }}
                        </Link>

                        <!-- Profile Matrix -->
                        <Link 
                            :href="route('profile.edit')"
                            class="w-10 h-10 bg-slate-50 border border-gray-100 rounded-xl flex items-center justify-center font-black text-xs hover:bg-white hover:shadow-sm transition-all"
                        >
                            {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                        </Link>

                        <!-- Disconnect Signal -->
                        <Link 
                            :href="route('logout')" 
                            method="post" 
                            as="button"
                            class="hidden sm:block text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-brand-500 transition-colors"
                        >
                            {{ cart.t('logout') }}
                        </Link>
                    </div>
                </template>
                <Link 
                    v-else 
                    :href="route('login')" 
                    class="hidden sm:block text-[10px] font-black uppercase tracking-widest text-black border-2 border-black rounded-xl px-6 py-2.5 hover:bg-black hover:text-white transition-all shadow-sm"
                >
                    {{ cart.t('login') }}
                </Link>

                <!-- Mobile Menu -->
                <button @click="isMobileMenuOpen = !isMobileMenuOpen" class="lg:hidden p-2 text-black">
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" /></svg>
                </button>
            </div>
        </div>

        <!-- Mobile Drawer -->
        <div v-if="isMobileMenuOpen" class="fixed inset-0 bg-white z-[200] flex flex-col p-8 lg:hidden animate-in fade-in duration-300">
            <div class="flex justify-between items-center mb-12">
                <img src="/images/logo.png" class="h-12">
                <button @click="isMobileMenuOpen = false" class="p-2 text-black">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <div class="space-y-6">
                <Link 
                    v-for="link in navLinks" 
                    :key="link.name" 
                    :href="route(link.route)"
                    @click="isMobileMenuOpen = false"
                    class="block text-4xl font-black uppercase tracking-tighter"
                >
                    {{ cart.t(link.name) }}
                </Link>
            </div>

            <!-- Mobile Auth Action Section -->
            <div class="mt-auto border-t border-slate-50 pt-10 space-y-4">
                <template v-if="$page.props.auth.user">
                    <Link v-if="$page.props.auth.user.is_admin" :href="route('admin.dashboard')" @click="isMobileMenuOpen = false" class="block text-2xl font-black uppercase tracking-tighter text-brand-500">
                        {{ cart.t('dashboard') }}
                    </Link>
                    <Link :href="route('profile.edit')" @click="isMobileMenuOpen = false" class="block text-2xl font-black uppercase tracking-tighter">
                        {{ cart.t('profile') }}
                    </Link>
                    <Link :href="route('logout')" method="post" as="button" @click="isMobileMenuOpen = false" class="block text-2xl font-black uppercase tracking-tighter text-gray-300">
                        {{ cart.t('logout') }}
                    </Link>
                </template>
                <Link v-else :href="route('login')" @click="isMobileMenuOpen = false" class="block text-2xl font-black uppercase tracking-tighter">
                    {{ cart.t('login') }}
                </Link>
            </div>
        </div>
    </nav>
</template>
