<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <section class="relative min-h-screen flex items-center pt-20 overflow-hidden bg-white">
        <!-- Abstract background elements -->
        <div class="absolute inset-0 z-0">
            <img src="/images/hero_bg.png" alt="Futuristic Tech Background" class="w-full h-full object-cover opacity-20 scale-105 animate-pulse-slow" />
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
                        <span>Innovation at its core</span>
                    </div>
                    
                    <h1 class="text-6xl md:text-8xl font-black text-black leading-tight tracking-tighter">
                        Smart <span class="text-red-600 italic">Thinking.</span><br/>
                        Smart Systems.
                    </h1>
                    
                    <p class="text-xl text-gray-600 max-w-lg leading-relaxed">
                        We design the future today. Discover our range of high-end electronics, software solutions, and cutting-edge smart systems built for the next generation of thinkers.
                    </p>
                    
                    <div class="flex flex-col sm:flex-row gap-4 pt-4">
                        <a href="#shop" class="px-8 py-4 bg-red-600 text-white font-bold rounded-full hover:bg-black transition-all duration-300 transform hover:-translate-y-1 hover:shadow-2xl flex items-center justify-center space-x-2 group">
                            <span>Shop Now</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </a>
                        <a href="#" class="px-8 py-4 border-2 border-black text-black font-bold rounded-full hover:bg-black hover:text-white transition-all duration-300 flex items-center justify-center">
                            Explore Services
                        </a>
                    </div>
                </div>
                
                <div class="relative hidden lg:block animate-in zoom-in duration-1000 delay-300">
                    <div class="relative z-10 transform hover:scale-105 transition-transform duration-700">
                        <img src="/images/products/iot_device.png" alt="Thinker Tech" class="rounded-3xl shadow-[0_50px_100px_-20px_rgba(0,0,0,0.3)] border-8 border-white" />
                    </div>
                    <!-- Decorative accents -->
                    <div class="absolute -top-10 -right-10 w-40 h-40 bg-red-600/10 rounded-full blur-3xl animate-pulse"></div>
                    <div class="absolute -bottom-10 -left-10 w-60 h-60 bg-black/5 rounded-full blur-3xl animate-pulse"></div>
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
@keyframes pulse-slow {
    0%, 100% { transform: scale(1.05); opacity: 0.15; }
    50% { transform: scale(1.1); opacity: 0.25; }
}
.animate-pulse-slow {
    animation: pulse-slow 10s ease-in-out infinite;
}
</style>
