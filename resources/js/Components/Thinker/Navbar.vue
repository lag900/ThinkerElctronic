<script setup>
import { Link, usePage, router } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { cart } from '@/Stores/CartStore';

const props = defineProps({
    transparent: {
        type: Boolean,
        default: false
    },
    theme: {
        type: String,
        default: 'light'
    }
});

const isDark = computed(() => props.theme === 'dark' && !isScrolled.value && props.transparent);

const emit = defineEmits(['open-cart']);
const isScrolled = ref(false);
const isMobileMenuOpen = ref(false);
const globalSearch = ref('');
const liveResults = ref([]);
const isSearching = ref(false);
const showDropdown = ref(false);
const selectedIndex = ref(-1);
let searchTimeout = null;

const fetchLiveSearch = async () => {
    if (!globalSearch.value.trim()) {
        liveResults.value = [];
        showDropdown.value = false;
        return;
    }
    
    isSearching.value = true;
    showDropdown.value = true;
    
    try {
        const response = await fetch(route('live-search') + '?q=' + encodeURIComponent(globalSearch.value));
        const data = await response.json();
        liveResults.value = data;
        selectedIndex.value = -1;
    } catch (e) {
        console.error(e);
    } finally {
        isSearching.value = false;
    }
};

const handleInput = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        fetchLiveSearch();
    }, 300);
};

const selectResult = (product) => {
    globalSearch.value = '';
    showDropdown.value = false;
    router.visit(route('product.show', product.id));
};

const onKeydown = (e) => {
    if (!showDropdown.value || liveResults.value.length === 0) {
        if (e.key === 'Enter') handleGlobalSearch();
        return;
    }
    
    if (e.key === 'ArrowDown') {
        e.preventDefault();
        selectedIndex.value = selectedIndex.value < liveResults.value.length - 1 ? selectedIndex.value + 1 : 0;
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        selectedIndex.value = selectedIndex.value > 0 ? selectedIndex.value - 1 : liveResults.value.length - 1;
    } else if (e.key === 'Enter') {
        e.preventDefault();
        if (selectedIndex.value >= 0) {
            selectResult(liveResults.value[selectedIndex.value]);
        } else {
            handleGlobalSearch();
        }
    } else if (e.key === 'Escape') {
        showDropdown.value = false;
        selectedIndex.value = -1;
    }
};

const formatCurrency = (val) => {
    return new Intl.NumberFormat(cart.language === 'ar' ? 'ar-EG' : 'en-US', { style: 'currency', currency: 'EGP' }).format(val);
};

const closeDropdown = () => {
    setTimeout(() => {
        showDropdown.value = false;
    }, 200);
};

const handleGlobalSearch = () => {
    if (!globalSearch.value.trim()) return;
    showDropdown.value = false;
    router.get(route('shop'), { q: globalSearch.value });
};

const handleScroll = () => {
    isScrolled.value = window.scrollY > 50;
};

const navRef = ref(null);
const headerHeight = ref(0);

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
    
    // Dynamic height detection to prevent layout overlap
    if (navRef.value) {
        const resizeObserver = new ResizeObserver((entries) => {
            for (let entry of entries) {
                headerHeight.value = entry.borderBoxSize ? entry.borderBoxSize[0].blockSize : entry.contentRect.height;
            }
        });
        resizeObserver.observe(navRef.value);
        
        // Cleanup on component unmount
        onUnmounted(() => {
            resizeObserver.disconnect();
        });
    }
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
    <!-- Dynamic Spacer prevents fixed header content overlap (Always 90px matching fixed header) -->
    <div class="h-[90px] w-full shrink-0" :class="{ 'hidden': transparent }"></div>

    <nav 
        ref="navRef"
        class="fixed top-0 inset-x-0 z-[100] transition-colors duration-500 h-[90px] flex items-center"
        :class="[
            isScrolled ? 'bg-white/95 backdrop-blur-2xl shadow-[0_4px_24px_rgba(0,0,0,0.06)] border-b border-gray-100' : (transparent ? 'bg-transparent' : 'bg-white border-b border-gray-100'),
            cart.language === 'ar' ? 'rtl' : 'ltr'
        ]"
    >
        <div class="max-w-[1400px] mx-auto w-full px-4 sm:px-8">
            <div class="flex items-center justify-between gap-4 lg:gap-8 h-full">
                <!-- Brand -->
                <Link href="/" class="flex items-center gap-3 group shrink-0 relative overflow-hidden">
                    <div class="relative w-10 h-10 sm:w-12 sm:h-12 flex items-center justify-center transition-transform duration-500 group-hover:scale-110">
                        <div class="absolute inset-0 bg-brand-500/10 rounded-2xl blur-lg opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <img src="/images/logo.png" class="h-8 sm:h-10 w-auto relative z-10">
                    </div>
                    <div class="hidden sm:flex flex-col">
                        <span class="text-xl font-black tracking-tighter uppercase leading-none transition-colors" :class="isDark ? 'text-white' : 'text-black'">Thinker</span>
                        <span class="text-[8px] font-black text-brand-500 uppercase mt-1 tracking-[0.3em] leading-none">Intelligence</span>
                    </div>
                </Link>

                <!-- Centered Hero Search Bar -->
                <div class="hidden md:flex flex-1 justify-center w-full max-w-[720px] mx-auto relative group">
                    <div class="relative w-full">
                        <input 
                            v-model="globalSearch"
                            @input="handleInput"
                            @keydown="onKeydown"
                            @focus="fetchLiveSearch"
                            @blur="closeDropdown"
                            type="text" 
                            class="w-full h-[56px] rounded-[14px] text-[14px] font-medium tracking-[0.3px] border border-transparent focus:border-[#ff2d55] focus:ring-0 focus:shadow-[0_0_0_3px_rgba(255,45,85,0.12)] outline-none transition-all placeholder:text-[13px] placeholder:text-[#9aa0a6] placeholder:font-normal"
                            :class="isDark && !isScrolled ? 'bg-white/10 text-white' : 'bg-gray-100 hover:bg-gray-200/50 text-[#111] focus:bg-white focus:hover:bg-white'"
                            :style="cart.language === 'ar' ? 'padding: 0 48px 0 18px;' : 'padding: 0 18px 0 48px;'"
                            :placeholder="cart.language === 'ar' ? 'البحث عن التكنولوجيا والمنتجات...' : 'Search for products, tech, brands...'"
                        >
                        <div class="absolute top-1/2 -translate-y-1/2 opacity-60 flex items-center justify-center pointer-events-none"
                             :style="cart.language === 'ar' ? 'right: 16px; left: auto;' : 'left: 16px; right: auto;'">
                            <svg class="w-[18px] h-[18px] transition-colors" 
                                 :class="[isDark && !isScrolled ? 'text-white' : 'text-[#111]']"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        
                        <!-- Search Shortcut -->
                        <div class="absolute top-1/2 -translate-y-1/2 hidden lg:flex items-center gap-1.5 px-2 py-1 rounded-lg border text-[9px] font-black pointer-events-none transition-opacity"
                             :style="cart.language === 'ar' ? 'left: 16px; right: auto;' : 'right: 16px; left: auto;'"
                             :class="isDark && !isScrolled ? 'border-white/10 text-white/40' : 'border-gray-200 text-gray-400 opacity-60'">
                            <span>COMMAND</span>
                            <span class="opacity-50">+</span>
                            <span>K</span>
                        </div>
                    </div>

                    <!-- Enhanced Live Search Dropdown -->
                    <div v-if="showDropdown && globalSearch" class="absolute top-[calc(100%+8px)] left-0 right-0 bg-white border border-gray-100 rounded-[16px] shadow-[0_20px_60px_rgba(0,0,0,0.1)] overflow-hidden z-[999] animate-in fade-in slide-in-from-top-2 duration-300">
                        <div v-if="isSearching" class="p-12 flex flex-col items-center justify-center gap-4">
                            <div class="relative w-10 h-10">
                                <div class="absolute inset-0 border-2 border-brand-500/20 rounded-full"></div>
                                <div class="absolute inset-0 border-2 border-brand-500 rounded-full border-t-transparent animate-spin"></div>
                            </div>
                            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Loading Results...</span>
                        </div>
                        
                        <div v-else-if="liveResults.length > 0" class="max-h-[400px] overflow-y-auto w-full">
                            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-white text-[#222]">
                                <span class="text-[11px] font-bold uppercase text-gray-400">Matches Found</span>
                                <span class="px-2 py-0.5 bg-brand-500/10 text-brand-500 rounded-lg text-[10px] font-black">{{ liveResults.length }}</span>
                            </div>
                            
                            <div v-for="(product, index) in liveResults" :key="product.id"
                                @click="selectResult(product)"
                                class="flex items-center gap-6 p-6 hover:bg-gray-50 transition-all cursor-pointer group/item border-b border-gray-50 last:border-0"
                                :class="{ 'bg-brand-50/50': selectedIndex === index }">
                                <div class="w-16 h-16 bg-white rounded-2xl border border-gray-100 overflow-hidden shrink-0 p-2 shadow-sm group-hover/item:scale-105 transition-transform duration-500">
                                    <img :src="product.image" class="w-full h-full object-contain">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-black text-gray-900 group-hover/item:text-brand-500 transition-colors">{{ product.name }}</h4>
                                    <div class="flex items-center gap-3 mt-1.5 font-bold uppercase tracking-widest text-[10px]">
                                        <span class="text-gray-400">{{ product.category }}</span>
                                        <span :class="product.stock > 0 ? 'text-emerald-500' : 'text-rose-500'">{{ product.stock > 0 ? 'Available' : 'Sold Out' }}</span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-black text-gray-900">{{ formatCurrency(product.price) }}</div>
                                </div>
                            </div>
                            <button @click="handleGlobalSearch" class="w-full py-4 bg-gray-50 text-xs font-black text-brand-500 hover:bg-gray-100 transition-colors uppercase tracking-widest border-t border-gray-100">
                                View all results
                            </button>
                        </div>
                        <div v-else class="p-10 text-center flex flex-col items-center justify-center">
                            <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span class="text-sm font-bold text-gray-500 block">No results found</span>
                            <span class="text-[11px] text-gray-400 mt-1 block">Try adjusting your search terms</span>
                        </div>
                    </div>
                </div>

                <!-- Action Hub -->
                <div class="flex items-center gap-6 shrink-0">
                    <!-- Nav Links Horizontal (Desktop) -->
                    <div class="hidden lg:flex items-center gap-8 border-r border-gray-200/20 pr-8 mr-2" :class="isDark && !isScrolled ? 'text-white' : 'text-gray-900'">
                        <Link v-for="link in navLinks" :key="link.name" :href="route(link.route)"
                              class="text-[10px] font-black uppercase tracking-[0.25em] hover:text-brand-500 transition-all relative group/link">
                            {{ cart.t(link.name) }}
                            <span class="absolute -bottom-2 left-0 w-0 h-0.5 bg-brand-500 transition-all duration-300 group-hover/link:w-full"></span>
                        </Link>
                    </div>

                    <!-- Icons -->
                    <div class="flex items-center gap-3">
                        <button @click="cart.toggleLanguage"
                                class="w-10 h-10 flex items-center justify-center rounded-xl transition-all border font-black text-[10px]"
                                :class="isDark && !isScrolled ? 'border-white/10 hover:bg-white/10 text-white' : 'border-gray-100 hover:bg-gray-100 text-gray-500'">
                            {{ cart.language === 'en' ? 'AR' : 'EN' }}
                        </button>

                        <button @click="$emit('open-cart')"
                                class="relative w-10 h-10 flex items-center justify-center rounded-xl transition-all group/cart"
                                :class="isDark && !isScrolled ? 'text-white hover:bg-white/10' : 'text-gray-900 hover:bg-gray-100'">
                            <svg class="w-6 h-6 transition-transform group-hover/cart:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            <span v-if="cart.count > 0" class="absolute -top-1 -right-1 w-5 h-5 bg-brand-500 text-white text-[9px] font-black rounded-full flex items-center justify-center ring-4" 
                                  :class="isDark && !isScrolled ? 'ring-[#020202]' : 'ring-white'">
                                {{ cart.count }}
                            </span>
                        </button>

                        <Link v-if="$page.props.auth.user" :href="route('profile.edit')"
                              class="w-10 h-10 border rounded-xl flex items-center justify-center font-black text-xs transition-all overflow-hidden"
                              :class="isDark && !isScrolled ? 'bg-white text-black border-white' : 'bg-gray-900 text-white border-gray-900 hover:bg-brand-500 hover:border-brand-500'">
                            {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                        </Link>
                        <Link v-else :href="route('login')"
                              class="w-10 h-10 flex items-center justify-center rounded-xl transition-all"
                              :class="isDark && !isScrolled ? 'text-white border border-white/20 hover:bg-white/10' : 'text-gray-900 border border-gray-100 hover:bg-gray-900 hover:text-white'">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                        </Link>

                        <button @click="isMobileMenuOpen = !isMobileMenuOpen" class="lg:hidden w-10 h-10 flex items-center justify-center rounded-xl bg-gray-100 text-gray-900 active:scale-95 transition-all">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" /></svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Search Bar (Only visible on small screens) -->
            <div class="md:hidden absolute top-[90px] left-0 right-0 bg-white border-b border-gray-100 p-4 shadow-sm z-[99]">
                <div class="relative w-full max-w-[720px] mx-auto group">
                    <input 
                        v-model="globalSearch"
                        @input="handleInput"
                        @keydown="onKeydown"
                        @focus="fetchLiveSearch"
                        @blur="closeDropdown"
                        type="text" 
                        class="w-full h-[56px] rounded-[14px] text-[14px] font-medium tracking-[0.3px] border border-transparent focus:border-[#ff2d55] focus:ring-0 focus:shadow-[0_0_0_3px_rgba(255,45,85,0.12)] outline-none transition-all placeholder:text-[13px] placeholder:text-[#9aa0a6] placeholder:font-normal"
                        :class="isDark ? 'bg-white/10 text-white' : 'bg-gray-100 text-[#111] focus:bg-white'"
                        :style="cart.language === 'ar' ? 'padding: 0 48px 0 18px;' : 'padding: 0 18px 0 48px;'"
                        :placeholder="cart.language === 'ar' ? 'بحث...' : 'Search for products...'"
                    >
                    <div class="absolute top-1/2 -translate-y-1/2 opacity-60 flex items-center justify-center pointer-events-none"
                         :style="cart.language === 'ar' ? 'right: 16px; left: auto;' : 'left: 16px; right: auto;'">
                        <svg class="w-[18px] h-[18px]" :class="isDark ? 'text-white' : 'text-[#111]'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </div>
                </div>

                <!-- Dropdown UI Mobile -->
                <div v-if="showDropdown && globalSearch" class="absolute top-[calc(100%-1rem)] left-4 right-4 bg-white border border-gray-100 rounded-3xl shadow-xl overflow-hidden animate-in fade-in slide-in-from-top-4 duration-300">
                    <div v-if="isSearching" class="p-6 text-center text-gray-400 text-xs font-bold uppercase tracking-widest flex items-center justify-center gap-3">
                        <svg class="animate-spin h-4 w-4 text-brand-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </div>
                    
                    <div v-else-if="liveResults.length > 0" class="max-h-[50vh] overflow-y-auto">
                        <div 
                            v-for="(product, index) in liveResults" 
                            :key="product.id"
                            @mousedown.prevent="selectResult(product)"
                            @touchstart.passive="selectResult(product)"
                            class="flex items-center gap-3 p-3 active:bg-slate-50 border-b border-gray-50/50"
                        >
                            <div class="w-10 h-10 bg-white rounded-lg border border-gray-100 overflow-hidden shrink-0 flex items-center justify-center p-0.5">
                                <img :src="product.image" class="w-full h-full object-contain">
                            </div>
                            
                            <div class="flex-1 min-w-0">
                                <h4 class="text-[11px] font-black text-[#222] truncate">{{ product.name }}</h4>
                                <div class="text-[10px] font-black text-brand-500 mt-0.5">{{ formatCurrency(product.price) }}</div>
                            </div>
                        </div>
                        <button @mousedown.prevent="handleGlobalSearch" @touchstart.passive="handleGlobalSearch" class="w-full py-3 bg-gray-50 text-[9px] font-black uppercase tracking-widest text-[#222] text-center">
                            View All Results
                        </button>
                    </div>

                    <div v-else class="p-6 text-center">
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">No products found</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Professional Mobile Drawer (Premium Design) -->
        <Transition
            enter-active-class="transition duration-500 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-400 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="isMobileMenuOpen" class="fixed inset-0 z-[200] lg:hidden">
                <!-- Backdrop Blur -->
                <div @click="isMobileMenuOpen = false" class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>

                <!-- Sidebar Content -->
                <Transition
                    enter-active-class="transition-transform duration-500 cubic-bezier(0.16, 1, 0.3, 1)"
                    enter-from-class="translate-x-full"
                    enter-to-class="translate-x-0"
                    leave-active-class="transition-transform duration-400 ease-in"
                    leave-from-class="translate-x-0"
                    leave-to-class="translate-x-full"
                >
                    <div v-if="isMobileMenuOpen" class="absolute right-0 top-0 bottom-0 w-[85%] max-w-sm bg-white shadow-2xl flex flex-col overflow-hidden">
                        <!-- Header -->
                        <div class="px-8 py-10 flex items-center justify-between border-b border-slate-50">
                            <Link href="/" @click="isMobileMenuOpen = false" class="flex items-center gap-2">
                                <img src="/images/logo.png" class="h-10">
                                <span class="text-lg font-black tracking-tighter uppercase text-black">Thinker</span>
                            </Link>
                            <button @click="isMobileMenuOpen = false" class="p-2 bg-slate-50 rounded-xl text-slate-400 active:scale-90 transition-all">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>

                        <!-- Menu Body -->
                        <div class="flex-1 overflow-y-auto px-8 py-12 space-y-12">
                            <!-- Main Section -->
                            <div class="space-y-6">
                                <h6 class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400 px-1">{{ cart.language === 'ar' ? 'القائمة الرئيسية' : 'Main Menu' }}</h6>
                                <div class="space-y-2">
                                    <Link 
                                        v-for="link in navLinks" 
                                        :key="link.name" 
                                        :href="route(link.route)"
                                        @click="isMobileMenuOpen = false"
                                        class="flex items-center justify-between group py-3 px-1"
                                    >
                                        <span class="text-2xl font-black text-[#222] tracking-tighter uppercase group-active:text-brand-500 transition-colors">
                                            {{ cart.t(link.name) }}
                                        </span>
                                        <svg class="w-5 h-5 text-slate-200 group-hover:text-brand-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                                    </Link>
                                </div>
                            </div>

                            <!-- User Section -->
                            <div class="space-y-6">
                                <h6 class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400 px-1">{{ cart.language === 'ar' ? 'حساب المستخدم' : 'User Section' }}</h6>
                                <div class="space-y-2">
                                    <template v-if="$page.props.auth.user">
                                        <Link 
                                            v-if="$page.props.auth.user.is_admin" 
                                            :href="route('admin.dashboard')" 
                                            @click="isMobileMenuOpen = false" 
                                            class="block py-3 px-1 text-xl font-black uppercase tracking-tighter text-brand-500"
                                        >
                                            {{ cart.t('dashboard') }}
                                        </Link>
                                        <Link 
                                            :href="route('profile.edit')" 
                                            @click="isMobileMenuOpen = false" 
                                            class="block py-3 px-1 text-xl font-black uppercase tracking-tighter text-[#222]"
                                        >
                                            {{ cart.t('profile') }}
                                        </Link>
                                        <Link 
                                            :href="route('lms-points')" 
                                            @click="isMobileMenuOpen = false" 
                                            class="block py-3 px-1 text-xl font-black uppercase tracking-tighter text-[#222]"
                                        >
                                            {{ cart.language === 'ar' ? 'النقاط' : 'Points' }}
                                        </Link>
                                    </template>
                                    <Link 
                                        v-else 
                                        :href="route('login')" 
                                        @click="isMobileMenuOpen = false" 
                                        class="block py-3 px-1 text-xl font-black uppercase tracking-tighter text-[#222]"
                                    >
                                        {{ cart.t('login') }}
                                    </Link>
                                </div>
                            </div>

                            <!-- System Section -->
                            <div class="space-y-6">
                                <h6 class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400 px-1">{{ cart.language === 'ar' ? 'النظام' : 'System' }}</h6>
                                <div class="space-y-1">
                                    <Link :href="route('privacy')" @click="isMobileMenuOpen = false" class="block py-2 px-1 text-[11px] font-bold uppercase tracking-widest text-slate-400 hover:text-black transition-colors">{{ cart.language === 'ar' ? 'سياسة الخصوصية' : 'Privacy' }}</Link>
                                    <Link :href="route('terms')" @click="isMobileMenuOpen = false" class="block py-2 px-1 text-[11px] font-bold uppercase tracking-widest text-slate-400 hover:text-black transition-colors">{{ cart.language === 'ar' ? 'الشروط والأحكام' : 'Terms' }}</Link>
                                    <Link :href="route('contact')" @click="isMobileMenuOpen = false" class="block py-2 px-1 text-[11px] font-bold uppercase tracking-widest text-slate-400 hover:text-black transition-colors">{{ cart.language === 'ar' ? 'اتصل بنا' : 'Contact' }}</Link>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Logout -->
                        <div v-if="$page.props.auth.user" class="p-8 border-t border-slate-50">
                            <Link 
                                :href="route('logout')" 
                                method="post" 
                                as="button" 
                                @click="isMobileMenuOpen = false" 
                                class="w-full py-5 bg-slate-50 text-slate-400 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] hover:bg-red-50 hover:text-red-500 transition-all flex items-center justify-center gap-3"
                            >
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                                {{ cart.t('logout') }}
                            </Link>
                        </div>
                    </div>
                </Transition>
            </div>
        </Transition>
    </nav>
</template>
