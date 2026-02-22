<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import Navbar from '@/Components/Thinker/Navbar.vue';
import ProductCard from '@/Components/Thinker/ProductCard.vue';
import CartModal from '@/Components/Thinker/CartModal.vue';
import Footer from '@/Components/Thinker/Footer.vue';
import { cart } from '@/Stores/CartStore';

const props = defineProps({
    products: Array,
    categories: Array,
    homeData: Object
});

const isCartOpen = ref(false);
const toggleCart = () => isCartOpen.value = !isCartOpen.value;

const formatNumber = (val) => {
    return new Intl.NumberFormat(cart.language === 'ar' ? 'ar-EG' : 'en-US').format(val);
};

// Default content if not set in admin
const defaultData = {
    hero: {
        title: 'Smart Thinking. Smart Systems.',
        subtitle: 'A world of technical and engineering solutions tailored specifically to your needs.',
        button_text: 'Shop Now',
        image: null
    },
    features: [
        { title: 'Advanced Performance', text: 'Proprietary hardware architectures optimized for speed.', icon: 'zap' },
        { title: 'Secure Transmission', text: 'Encrypted communication layers for your digital assets.', icon: 'shield' },
        { title: 'Fluid Scalability', text: 'Nodes that grow alongside your infrastructure.', icon: 'cpu' },
        { title: 'Neural Integration', text: 'Systems designed to harmonize with human interaction.', icon: 'heart' }
    ],
    custom_section: {
        title: 'Premium Engineering for Modern Ecosystems',
        text: 'We bridge the gap between complex hardware and user-centric design, ensuring every node in your network performs with absolute precision.',
        button_text: 'Explore Hardware',
        image: null,
        link: null
    },
    slider: []
};

const d = computed(() => {
    const slider = props.homeData?.slider || [];
    const customSection = { ...defaultData.custom_section, ...props.homeData?.custom_section };
    
    // Fallback logic: If no custom section image, use first slider image if available
    if (!customSection.image && slider.length > 0) {
        customSection.image = slider[0].image;
    }

    return {
        hero: { ...defaultData.hero, ...props.homeData?.hero },
        features: props.homeData?.features?.length ? props.homeData.features : defaultData.features,
        custom_section: customSection,
        slider: slider
    };
});

const colorize = (text) => {
    if (!text) return '';
    return text.replace(/Thinking/gi, '<span class="text-brand-500">Thinking</span>');
};

const isExternal = (url) => {
    return url && (url.startsWith('http') || url.startsWith('www') || url.startsWith('//'));
};
</script>

<template>
    <Head title="Thinker - Intelligence Hub" />
    
    <div class="bg-gray-50 min-h-screen font-sans overflow-x-hidden" :dir="cart.language === 'ar' ? 'rtl' : 'ltr'">
        <Navbar @open-cart="toggleCart" :transparent="true" theme="dark" />

        <!-- Futuristic Premium Hero Section -->
        <header class="relative min-h-screen flex items-center justify-center overflow-hidden bg-[#020202] z-10 selection:bg-brand-500 selection:text-white">
            <!-- Animated Background Layers -->
            <div class="absolute inset-0 z-0">
                <!-- Deep Dark Gradient -->
                <div class="absolute inset-0 bg-gradient-to-br from-black via-[#0a0a0a] to-[#020202]"></div>
                
                <!-- Dynamic Glow Orbs -->
                <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] bg-brand-500/10 rounded-full blur-[120px] animate-pulse"></div>
                <div class="absolute -bottom-[10%] -right-[10%] w-[40%] h-[40%] bg-brand-500/5 rounded-full blur-[120px] animate-pulse" style="animation-delay: 2s"></div>
                
                <!-- Advanced Tech Grid -->
                <div class="absolute inset-0 opacity-[0.07]" style="background-image: linear-gradient(rgba(255, 255, 255, 0.05) 1px, transparent 1px), linear-gradient(90deg, rgba(255, 255, 255, 0.05) 1px, transparent 1px); background-size: 60px 60px;"></div>
                <div class="absolute inset-0 opacity-[0.03]" style="background-image: linear-gradient(rgba(255, 255, 255, 0.05) 1px, transparent 1px), linear-gradient(90deg, rgba(255, 255, 255, 0.05) 1px, transparent 1px); background-size: 20px 20px;"></div>
                
                <!-- Subtle Grain Overlay -->
                <div class="absolute inset-0 opacity-[0.2] pointer-events-none mix-blend-overlay" style="background-image: url('https://grainy-gradients.vercel.app/noise.svg');"></div>
            </div>

            <!-- Hero Image / Canvas Placeholder for futuristic effect -->
            <div v-if="d.hero.image" class="absolute inset-0 z-0 opacity-40 mix-blend-screen overflow-hidden">
                <img :src="d.hero.image" class="w-full h-full object-cover scale-110 blur-[2px]" alt="">
                <div class="absolute inset-0 bg-gradient-to-t from-[#020202] via-[#020202]/50 to-transparent"></div>
            </div>

            <div class="max-w-7xl mx-auto px-6 sm:px-8 relative z-10 w-full pt-20">
                <div class="flex flex-col items-center text-center space-y-12">
                    <!-- Badge -->
                    <div class="inline-flex items-center gap-3 px-6 py-2 rounded-full border border-white/10 bg-white/5 backdrop-blur-2xl text-[10px] sm:text-xs font-black text-white uppercase tracking-[0.4em] animate-in fade-in slide-in-from-top-4 duration-1000">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-brand-500 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-brand-500 shadow-[0_0_10px_#ff2d55]"></span>
                        </span>
                        The Future of Hardware
                    </div>

                    <!-- Main Headline -->
                    <div class="space-y-6">
                        <h1 class="text-[50px] sm:text-[80px] lg:text-[120px] font-extrabold text-white leading-[0.9] tracking-tighter uppercase animate-in fade-in slide-in-from-bottom-8 duration-1000 delay-100" v-html="colorize(d.hero.title)">
                        </h1>
                        <p class="text-gray-400 text-sm sm:text-lg lg:text-xl font-medium max-w-2xl mx-auto leading-relaxed animate-in fade-in slide-in-from-bottom-8 duration-1000 delay-200">
                            {{ d.hero.subtitle }}
                        </p>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-6 w-full max-w-2xl animate-in fade-in slide-in-from-bottom-8 duration-1000 delay-300">
                        <Link :href="route('shop')" class="group relative w-full sm:w-auto overflow-hidden rounded-2xl bg-brand-500 px-12 py-6 text-xs font-black uppercase tracking-[0.3em] text-white transition-all hover:bg-brand-600 hover:shadow-[0_0_40px_rgba(255,45,85,0.4)] active:scale-95 duration-500">
                            <span class="relative z-10 flex items-center justify-center gap-3">
                                {{ d.hero.button_text || 'Enter Ecosystem' }}
                                <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                            </span>
                            <div class="absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/20 to-transparent transition-transform duration-1000 group-hover:translate-x-full"></div>
                        </Link>

                        <Link v-if="$page.props.auth.user && $page.props.auth.user.is_admin" :href="route('admin.dashboard')" class="w-full sm:w-auto rounded-2xl border border-white/10 bg-white/5 backdrop-blur-xl px-12 py-6 text-xs font-black uppercase tracking-[0.2em] text-white transition-all hover:bg-white/10 hover:border-white/20 active:scale-95 duration-500 flex items-center justify-center gap-3">
                            <svg class="w-4 h-4 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                            Control Center
                        </Link>
                        <Link v-else :href="route('login')" class="w-full sm:w-auto rounded-2xl border border-white/10 bg-white/5 backdrop-blur-xl px-12 py-6 text-xs font-black uppercase tracking-[0.2em] text-white transition-all hover:bg-white/10 hover:border-white/20 active:scale-95 duration-500 flex items-center justify-center gap-3">
                            Access Node
                        </Link>
                    </div>

                    <!-- Stats / Social Proof (Subtle) -->
                    <div class="pt-12 grid grid-cols-3 gap-8 sm:gap-16 border-t border-white/5 w-full max-w-4xl animate-in fade-in duration-1000 delay-500">
                        <div class="text-center space-y-1">
                            <div class="text-2xl font-black text-white tracking-tighter">10K+</div>
                            <div class="text-[9px] font-black uppercase tracking-widest text-gray-500">Nodes Active</div>
                        </div>
                        <div class="text-center space-y-1">
                            <div class="text-2xl font-black text-brand-500 tracking-tighter">99.9%</div>
                            <div class="text-[9px] font-black uppercase tracking-widest text-gray-500">Uptime Rate</div>
                        </div>
                        <div class="text-center space-y-1">
                            <div class="text-2xl font-black text-white tracking-tighter">24/7</div>
                            <div class="text-[9px] font-black uppercase tracking-widest text-gray-500">Deep Support</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Gradient Fade Out -->
            <div class="absolute bottom-0 inset-x-0 h-32 bg-gradient-to-t from-white to-transparent z-10"></div>
        </header>

        <!-- Slider Section (If exists) -->
        <section v-if="d.slider.length" class="py-24 bg-white z-10 relative">
            <div class="max-w-7xl mx-auto px-6 sm:px-8">
                <div class="flex gap-8 overflow-x-auto pb-12 scrollbar-none snap-x">
                    <div v-for="(slide, i) in d.slider" :key="i" class="min-w-[300px] md:min-w-[600px] snap-center">
                        <div class="relative aspect-video rounded-[48px] overflow-hidden group shadow-lg">
                            <img :src="slide.image" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent flex flex-col justify-end p-12">
                                <h4 class="text-4xl font-black text-white tracking-tighter uppercase">{{ slide.title }}</h4>
                                <p class="text-gray-300 font-bold mt-2 uppercase text-xs tracking-widest">{{ slide.subtitle }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

 



        <!-- Advanced Category Grid -->
        <section v-if="categories && categories.length > 0" class="py-32 bg-white z-10 relative">
            <div class="max-w-7xl mx-auto px-6 sm:px-8">
                <div class="flex flex-col sm:flex-row items-end justify-between mb-20 gap-6">
                    <div class="space-y-2">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-brand-500/5 text-brand-500 text-[10px] font-black uppercase tracking-widest">
                            Intelligence Nodes
                        </div>
                        <h2 class="text-4xl sm:text-6xl font-black text-gray-900 uppercase tracking-tighter leading-none">Browse Core <br class="hidden sm:block"> Modules</h2>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-10">
                    <div 
                        v-for="cat in categories" 
                        :key="cat.id"
                        class="group relative bg-gray-50/50 p-6 sm:p-10 rounded-[40px] border border-gray-100 hover:bg-white hover:shadow-[0_40px_100px_rgba(0,0,0,0.08)] hover:-translate-y-3 transition-all duration-700 overflow-hidden flex flex-col items-center text-center"
                    >
                        <!-- Dynamic Background Glow -->
                        <div class="absolute -top-20 -right-20 w-48 h-48 bg-brand-500/5 rounded-full blur-[60px] group-hover:bg-brand-500/10 transition-colors"></div>
                        
                        <Link :href="route('shop', { category: cat.id })" class="w-20 h-20 sm:w-28 sm:h-28 mb-8 bg-white rounded-3xl p-5 sm:p-7 shadow-[0_10px_30px_rgba(0,0,0,0.03)] border border-gray-50 group-hover:scale-110 group-hover:rotate-12 transition-all duration-700 block relative z-10">
                            <img v-if="cat.image" :src="cat.image" class="w-full h-full object-contain">
                            <div v-else class="w-full h-full flex items-center justify-center text-gray-200 text-3xl">📦</div>
                        </Link>

                        <div class="space-y-6 w-full relative z-10">
                            <div class="space-y-1">
                                <Link :href="route('shop', { category: cat.id })" class="text-lg sm:text-2xl font-black text-gray-900 uppercase tracking-tighter hover:text-brand-500 transition-colors block">
                                    {{ cat.name }}
                                </Link>
                                <div class="flex items-center justify-center gap-2">
                                     <div class="w-1.5 h-1.5 rounded-full bg-brand-500"></div>
                                     <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">
                                        {{ formatNumber(cat.products_count || 0) }} Units
                                    </p>
                                </div>
                            </div>

                            <!-- Subcategories -->
                            <div v-if="cat.children && cat.children.length > 0" class="flex flex-wrap justify-center gap-2 pt-2">
                                <Link 
                                    v-for="sub in cat.children" 
                                    :key="sub.id"
                                    :href="route('shop', { category: sub.id })"
                                    class="bg-white px-3 py-1.5 rounded-xl text-[8px] font-black uppercase tracking-widest text-gray-500 border border-gray-100 hover:border-brand-500 hover:text-brand-500 transition-all shadow-sm"
                                >
                                    {{ sub.name }}
                                </Link>
                            </div>

                            <Link :href="route('shop', { category: cat.id })" class="w-full py-4 rounded-2xl bg-gray-900 text-white text-[9px] font-black uppercase tracking-widest opacity-0 group-hover:opacity-100 translate-y-4 group-hover:translate-y-0 transition-all duration-500">
                                View Protocol
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Premium Hardware Showcase -->
        <section class="py-32 bg-gray-50 z-10 relative">
            <div class="max-w-7xl mx-auto px-6 sm:px-8">
                <div class="flex flex-col sm:flex-row items-end justify-between mb-20 gap-6">
                    <div class="space-y-2">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-brand-500/5 text-brand-500 text-[10px] font-black uppercase tracking-widest">
                            Neural Assets
                        </div>
                        <h2 class="text-4xl sm:text-6xl font-black text-gray-900 uppercase tracking-tighter leading-none">Hardware <br class="hidden sm:block"> Showcase</h2>
                    </div>
                    <Link :href="route('shop')" class="group flex items-center gap-4 bg-white border border-gray-200 px-10 py-5 rounded-2xl text-[10px] font-black uppercase tracking-widest text-gray-900 hover:bg-brand-500 hover:text-white hover:border-brand-500 transition-all shadow-sm">
                        Full Inventory
                        <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                    </Link>
                </div>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-10">
                    <ProductCard 
                        v-for="product in products" 
                        :key="product.id" 
                        :product="product" 
                        class="animate-in fade-in slide-in-from-bottom-4 duration-700"
                    />
                </div>
            </div>
        </section>
                <!-- Dynamic Content Section (Apple Style) -->
        <section class="py-32 bg-white relative z-10 group overflow-hidden">
            <div class="max-w-7xl mx-auto px-6 sm:px-8">
                <div class="bg-gray-50 rounded-[60px] p-12 lg:p-24 overflow-hidden relative flex flex-col lg:flex-row items-center gap-20 border border-gray-100">
                    <div class="w-full lg:w-1/2 relative z-10">
                        <div class="absolute -inset-10 bg-brand-500/5 rounded-full blur-3xl"></div>
                        
                        <!-- Clickable Image Container -->
                        <div class="relative group/img overflow-hidden rounded-[40px] shadow-2xl shadow-brand-500/10">
                            <component 
                                :is="d.custom_section.link ? (isExternal(d.custom_section.link) ? 'a' : Link) : 'div'"
                                :href="d.custom_section.link || null"
                                :target="isExternal(d.custom_section.link) ? '_blank' : null"
                                class="block relative overflow-hidden"
                                :class="{ 'cursor-pointer': d.custom_section.link }"
                            >
                                <img 
                                    v-if="d.custom_section.image" 
                                    :src="d.custom_section.image" 
                                    class="w-full h-full object-cover transition-transform duration-1000"
                                    :class="{ 'group-hover/img:scale-110 group-hover/img:rotate-1': d.custom_section.link }"
                                >
                                <div v-else class="aspect-square bg-white/50 backdrop-blur flex items-center justify-center text-slate-200 text-6xl font-black uppercase">Asset</div>
                                
                                <!-- Link Overlay Hint -->
                                <div v-if="d.custom_section.link" class="absolute inset-0 bg-brand-500/10 opacity-0 group-hover/img:opacity-100 transition-opacity flex items-center justify-center">
                                    <div class="bg-white/90 backdrop-blur-md p-4 rounded-2xl shadow-xl transform translate-y-4 group-hover/img:translate-y-0 transition-transform">
                                        <svg class="w-6 h-6 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                                    </div>
                                </div>
                            </component>
                        </div>
                    </div>
                    
                    <div class="w-full lg:w-1/2 space-y-8 relative z-10">
                        <h3 class="text-5xl lg:text-7xl font-black text-[#222] tracking-tighter leading-none items-center flex gap-4 uppercase">
                            {{ d.custom_section.title }}
                        </h3>
                        <p class="text-xl text-gray-500 leading-relaxed font-medium">
                            {{ d.custom_section.text }}
                        </p>
                        <div class="pt-6">
                            <Link :href="route('shop')" class="inline-flex items-center gap-4 bg-[#222] text-white px-10 py-5 rounded-2xl font-black uppercase tracking-widest hover:bg-brand-500 transition-all active:scale-95 shadow-lg shadow-black/10">
                                {{ d.custom_section.button_text }}
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4"><path d="M9 5l7 7-7 7" /></svg>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </section>
       <!-- Why Thinker (Stripe-like Section) -->
        <section class="py-32 bg-gray-50 z-10 relative overflow-hidden">
            <div class="max-w-7xl mx-auto px-6 sm:px-8">
                <div class="text-center mb-24 max-w-3xl mx-auto">
                    <h2 class="text-5xl font-black text-[#222] uppercase tracking-tighter mb-6">Why Thinker Architecture?</h2>
                    <p class="text-lg text-gray-500 font-medium leading-relaxed">We engineering a new standard for hardware integration, where every component is a node of pure intelligence.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div v-for="(feat, i) in d.features" :key="i" 
                         class="group bg-white p-10 rounded-[48px] border border-white shadow-[0_20px_50px_rgba(0,0,0,0.02)] hover:shadow-[0_40px_80px_rgba(0,0,0,0.08)] hover:-translate-y-4 transition-all duration-500">
                        <div class="w-16 h-16 bg-brand-50 rounded-2xl flex items-center justify-center text-brand-500 mb-8 font-black shadow-sm group-hover:bg-brand-500 group-hover:text-white transition-all">
                             <svg v-if="feat.icon === 'zap'" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                             <svg v-else-if="feat.icon === 'shield'" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                             <svg v-else-if="feat.icon === 'cpu'" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" /></svg>
                             <svg v-else class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                        </div>
                        <h4 class="text-xl font-black text-[#222] uppercase tracking-tighter mb-4">{{ feat.title }}</h4>
                        <p class="text-sm text-gray-400 font-bold leading-relaxed line-clamp-3 uppercase tracking-tight">{{ feat.text }}</p>
                    </div>
                </div>
            </div>
        </section>
        <Footer />
        <CartModal :is-open="isCartOpen" @close="toggleCart" />
    </div>
</template>

<style>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@900&display=swap');

.font-sans {
    font-family: 'Inter', system-ui, -apple-system, sans-serif;
}

@keyframes float {
    0% { transform: translate(0, 0) rotate(0); }
    33% { transform: translate(30px, -50px) rotate(5deg); }
    66% { transform: translate(-20px, 20px) rotate(-5deg); }
    100% { transform: translate(0, 0) rotate(0); }
}

.animate-float {
    animation: float 20s infinite ease-in-out;
}

::-webkit-scrollbar {
    display: none;
}

.scrollbar-none {
    -ms-overflow-style: none; /* IE and Edge */
    scrollbar-width: none; /* Firefox */
}
</style>
