<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import Navbar from '@/Components/Thinker/Navbar.vue';
import Hero from '@/Components/Thinker/Hero.vue';
import ProductCard from '@/Components/Thinker/ProductCard.vue';
import CartModal from '@/Components/Thinker/CartModal.vue';
import { cart } from '@/Stores/CartStore';

const props = defineProps({
    products: Array
});

const isCartOpen = ref(false);

const toggleCart = () => {
    isCartOpen.value = !isCartOpen.value;
};

</script>

<template>
    <Head :title="cart.t('thinking') + ' - ' + cart.t('slogan')" />
    
    <div class="bg-white min-h-screen font-sans selection:bg-red-600 selection:text-white">
        <!-- Navigation -->
        <Navbar @open-cart="toggleCart" />

        <!-- Hero Section -->
        <section class="relative min-h-screen flex items-center pt-20 overflow-hidden bg-white">
            <div class="absolute inset-0 z-0">
                <img src="/images/hero_bg.png" alt="Futuristic Tech" class="w-full h-full object-cover opacity-20 scale-105 animate-pulse-slow" />
                <div class="absolute inset-0 bg-gradient-to-b from-white via-transparent to-white"></div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="grid lg:grid-cols-2 gap-12 items-center">
                    <div class="space-y-8 animate-in slide-in-from-left duration-1000">
                        <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-red-50 border border-red-100 text-red-600 text-sm font-bold tracking-wider uppercase">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-red-600"></span>
                            </span>
                            <span>{{ cart.t('innovation') }}</span>
                        </div>
                        
                        <h1 class="text-6xl md:text-8xl font-black text-black leading-tight tracking-tighter">
                            {{ cart.t('smart') }} <span class="text-red-600 italic">{{ cart.t('thinking') }}</span><br/>
                            {{ cart.t('smart') }} Systems.
                        </h1>
                        
                        <p class="text-xl text-gray-600 max-w-lg leading-relaxed">
                            We design the future today. Discover our range of high-end electronics, software solutions, and cutting-edge smart systems built for the next generation of thinkers.
                        </p>
                        
                        <div class="flex flex-col sm:flex-row gap-4 pt-4">
                            <Link :href="route('shop')" class="px-8 py-4 bg-red-600 text-white font-bold rounded-full hover:bg-black transition-all duration-300 transform hover:-translate-y-1 hover:shadow-2xl flex items-center justify-center space-x-2 group">
                                <span>{{ cart.t('shopNow') }}</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </Link>
                            <a href="#" class="px-8 py-4 border-2 border-black text-black font-bold rounded-full hover:bg-black hover:text-white transition-all duration-300 flex items-center justify-center">
                                {{ cart.t('explore') }}
                            </a>
                        </div>
                    </div>
                    
                    <div class="relative hidden lg:block animate-in zoom-in duration-1000 delay-300">
                        <div class="relative z-10 transform hover:scale-105 transition-transform duration-700">
                            <img src="/images/products/iot_device.png" alt="Thinker Tech" class="rounded-3xl shadow-[0_50px_100px_-20px_rgba(0,0,0,0.3)] border-8 border-white" />
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Featured Section -->
        <section class="py-24 bg-gray-50 overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-2 gap-16 items-center">
                    <div class="relative group" :class="cart.language === 'ar' ? 'order-last' : ''">
                        <div class="absolute -inset-4 bg-gradient-to-r from-red-600 to-black rounded-3xl blur-2xl opacity-10 group-hover:opacity-20 transition-opacity duration-500"></div>
                        <img src="/images/products/iot_device.png" alt="Advanced Electronics" class="relative rounded-3xl shadow-2xl transition-transform duration-700 group-hover:scale-[1.02]" />
                    </div>
                    
                    <div class="space-y-8">
                        <h2 class="text-4xl md:text-5xl font-black text-black tracking-tighter uppercase">
                            {{ cart.t('hardware') }} <span class="text-red-600 italic">{{ cart.t('excellence') }}</span>
                        </h2>
                        <p class="text-lg text-gray-600 leading-relaxed">
                            Our smart systems are built on a foundation of precision engineering and innovative design. From custom 3D printed components to complex integrated circuits, we deliver excellence in every layer.
                        </p>
                        <ul class="space-y-4">
                            <li v-for="item in ['Custom PCB Design', '3D Prototyping', 'AI Integration', 'Smart Home Ecosystems']" :key="item" class="flex items-center space-x-3 text-black font-bold">
                                <svg class="h-6 w-6 text-red-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>{{ item }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- Product Preview Section -->
        <section id="shop" class="py-24 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16 space-y-4">
                    <h2 class="text-5xl font-black text-black tracking-tighter uppercase">{{ cart.t('ourProducts') }}</h2>
                    <p class="text-gray-500 max-w-2xl mx-auto text-lg underline decoration-red-600 decoration-2 underline-offset-8">{{ cart.t('discover') }}</p>
                </div>
                
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <ProductCard 
                        v-for="product in products" 
                        :key="product.id" 
                        :product="product" 
                    />
                </div>
                
                <div class="mt-16 text-center">
                    <Link :href="route('shop')" class="inline-flex items-center space-x-4 group text-black font-black uppercase tracking-widest hover:text-red-600 transition-colors">
                        <span>View All Products</span>
                        <svg class="w-6 h-6 transform group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                    </Link>
                </div>
            </div>
        </section>

        <!-- Newsletter -->
        <section class="py-24 bg-black text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-96 h-96 bg-red-600/20 rounded-full blur-[120px]"></div>
            <div class="max-w-7xl mx-auto px-4 text-center space-y-8 relative z-10">
                <h2 class="text-4xl md:text-5xl font-black tracking-tighter uppercase leading-tight">
                    {{ cart.t('stayAhead') }} <span class="text-red-600 italic">{{ cart.t('theFuture') }}</span>
                </h2>
                <button class="px-12 py-5 bg-red-600 hover:bg-white hover:text-black text-white font-black rounded-full transition-all duration-500 uppercase tracking-widest shadow-2xl shadow-red-600/20 transform active:scale-95">
                    {{ cart.t('joinNow') }}
                </button>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-100 py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row justify-between items-center space-y-6 md:space-y-0 text-center md:text-left">
                    <div class="flex items-center space-x-2">
                        <img src="/images/logo.png" class="w-8 h-8 object-contain" alt="Thinker" />
                        <span class="text-xl font-black tracking-tighter uppercase">Thinker</span>
                    </div>
                    <p class="text-gray-400 text-sm">
                        &copy; 2026 Thinker Inc. {{ cart.t('slogan') }}
                    </p>
                </div>
            </div>
        </footer>

        <CartModal :is-open="isCartOpen" @close="toggleCart" />
    </div>
</template>

<style>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@100;400;700;900&display=swap');
:root { font-family: 'Outfit', sans-serif; }
@keyframes pulse-slow {
    0%, 100% { transform: scale(1.05); opacity: 0.15; }
    50% { transform: scale(1.1); opacity: 0.25; }
}
.animate-pulse-slow { animation: pulse-slow 10s ease-in-out infinite; }
</style>
