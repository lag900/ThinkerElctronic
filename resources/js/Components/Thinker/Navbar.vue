<script setup>
import { Link } from '@inertiajs/vue3';
import { cart } from '@/Stores/CartStore';
import { ref } from 'vue';

const isMenuOpen = ref(false);
const emit = defineEmits(['open-cart']);

</script>

<template>
    <nav class="fixed top-0 w-full z-50 bg-white/80 backdrop-blur-md border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <div class="flex-shrink-0 flex items-center">
                    <Link href="/" class="flex items-center space-x-2 group">
                        <img src="/images/logo.png" alt="Thinker Logo" class="w-105 h-20 object-contain group-hover:rotate-6 transition-transform duration-300" />
                    </Link>
                </div>
                
                <div class="hidden md:flex space-x-8 items-center">
                    <Link href="/" class="text-gray-900 hover:text-red-600 font-medium transition-colors">{{ cart.t('home') }}</Link>
                    <Link :href="route('shop')" class="text-gray-900 hover:text-red-600 font-medium transition-colors">{{ cart.t('shop') }}</Link>
                    <a href="#" class="text-gray-900 hover:text-red-600 font-medium transition-colors">{{ cart.t('printing') }}</a>
                    <a href="#" class="text-gray-900 hover:text-red-600 font-medium transition-colors">{{ cart.t('solutions') }}</a>
                    <a href="#" class="text-gray-900 hover:text-red-600 font-medium transition-colors">{{ cart.t('contact') }}</a>
                    
                    <!-- Language Toggle -->
                    <button @click="cart.toggleLanguage" class="p-2 text-sm font-bold border rounded-full hover:bg-gray-50 transition-colors uppercase">
                        {{ cart.language === 'en' ? 'AR' : 'EN' }}
                    </button>

                    <button @click="$emit('open-cart')" class="relative p-2 text-gray-900 hover:text-red-600 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <span v-if="cart.count > 0" class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">
                            {{ cart.count }}
                        </span>
                    </button>
                </div>

                <div class="md:hidden flex items-center space-x-4">
                    <button @click="cart.toggleLanguage" class="p-1 px-3 text-xs font-bold border rounded-full uppercase">
                        {{ cart.language === 'en' ? 'AR' : 'EN' }}
                    </button>
                    <button @click="isMenuOpen = !isMenuOpen" class="text-gray-900">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path v-if="!isMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div v-if="isMenuOpen" class="md:hidden bg-white border-b border-gray-100 animate-in slide-in-from-top duration-300">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <Link href="/" class="block px-3 py-2 text-base font-medium text-gray-900 hover:bg-gray-50">{{ cart.t('home') }}</Link>
                <Link :href="route('shop')" class="block px-3 py-2 text-base font-medium text-gray-900 hover:bg-gray-50">{{ cart.t('shop') }}</Link>
                <a href="#" class="block px-3 py-2 text-base font-medium text-gray-900 hover:bg-gray-50">{{ cart.t('printing') }}</a>
                <a href="#" class="block px-3 py-2 text-base font-medium text-gray-900 hover:bg-gray-50">{{ cart.t('solutions') }}</a>
                <a href="#" class="block px-3 py-2 text-base font-medium text-gray-900 hover:bg-gray-50">{{ cart.t('contact') }}</a>
                <button @click="$emit('open-cart'); isMenuOpen = false" class="w-full text-left block px-3 py-2 text-base font-medium text-gray-900 hover:bg-gray-50">
                    {{ cart.t('cart') }} ({{ cart.count }})
                </button>
            </div>
        </div>
    </nav>
</template>
