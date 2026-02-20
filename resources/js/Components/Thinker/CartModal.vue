<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { cart } from '@/Stores/CartStore';

const props = defineProps({
    isOpen: Boolean
});

const emit = defineEmits(['close']);

const handleUpdateQuantity = (item, newQty) => {
    cart.updateQuantity(item.id, newQty);
};

const subtotal = computed(() => cart.total);
</script>

<template>
    <div v-if="isOpen" class="fixed inset-0 z-[100] overflow-hidden" role="dialog" aria-modal="true">
        <div class="absolute inset-0 overflow-hidden">
            <!-- Backdrop -->
            <div @click="$emit('close')" class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity animate-in fade-in duration-500"></div>

            <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full" :class="cart.language === 'ar' ? 'left-0 pr-0' : 'right-0 pl-0'">
                <div class="pointer-events-auto w-screen max-w-md transform transition ease-in-out duration-500 sm:duration-700 animate-in" :class="cart.language === 'ar' ? 'slide-in-from-left' : 'slide-in-from-right'">
                    <div class="flex h-full flex-col bg-white shadow-2xl">
                        <!-- Header -->
                        <div class="px-8 py-8 border-b border-gray-100 flex items-center justify-between">
                            <h2 class="text-2xl font-black text-black tracking-tighter uppercase leading-none">
                                {{ cart.t('cart') }} 
                                <span class="ml-2 text-sm text-gray-400 font-bold">({{ cart.items.length }})</span>
                            </h2>
                            <button @click="$emit('close')" class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-50 text-black hover:bg-[#ff2d55] hover:text-white transition-all">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 overflow-y-auto px-8 py-6">
                            <div v-if="cart.items.length === 0" class="h-full flex flex-col items-center justify-center text-center space-y-6">
                                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center text-gray-200">
                                     <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                                </div>
                                <div class="space-y-4">
                                    <p class="text-xs font-bold uppercase tracking-widest text-gray-500">{{ cart.t('emptyCart') }}</p>
                                    <button @click="$emit('close')" class="bg-black text-white px-8 py-4 rounded-xl font-bold text-[10px] uppercase tracking-widest">{{ cart.t('startShopping') }}</button>
                                </div>
                            </div>

                            <ul v-else class="space-y-8">
                                <li v-for="item in cart.items" :key="item.id" class="flex gap-6 animate-in slide-in-from-bottom-2 duration-500">
                                    <div class="h-24 w-24 flex-shrink-0 bg-gray-50 rounded-2xl overflow-hidden border border-gray-100">
                                        <img :src="item.image_url" :alt="item.name" class="h-full w-full object-cover" />
                                    </div>
                                    <div class="flex-1 flex flex-col justify-between">
                                        <div class="space-y-1">
                                            <div class="flex justify-between items-start">
                                                <h3 class="text-[11px] font-black text-black uppercase tracking-widest truncate max-w-[150px]">{{ item.name }}</h3>
                                                <button @click="cart.removeItem(item.id)" class="text-gray-400 hover:text-red-500 transition-colors">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                                </button>
                                            </div>
                                            <div class="text-sm font-black text-[#ff2d55] tracking-tighter">
                                                {{ (parseFloat(item.price) * item.quantity).toFixed(2) }} 
                                                <span class="text-[9px] uppercase ml-1">EGP</span>
                                            </div>
                                        </div>

                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center bg-gray-50 rounded-lg px-2 py-1 border border-gray-100">
                                                <button @click="handleUpdateQuantity(item, item.quantity - 1)" 
                                                    class="w-6 h-6 flex items-center justify-center font-bold text-gray-400 hover:text-black"
                                                    :disabled="item.quantity <= 1">-</button>
                                                <span class="w-8 text-center font-black text-xs">{{ item.quantity }}</span>
                                                <button @click="handleUpdateQuantity(item, item.quantity + 1)" 
                                                    class="w-6 h-6 flex items-center justify-center font-bold text-gray-400 hover:text-black">+</button>
                                            </div>
                                            <span class="text-[9px] font-bold text-gray-300 uppercase tracking-widest">Unit x{{ item.quantity }}</span>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>

                        <!-- Footer -->
                        <div v-if="cart.items.length > 0" class="p-8 border-t border-gray-100 bg-gray-50/50 space-y-6">
                            <div class="flex justify-between items-end">
                                <span class="text-xs uppercase tracking-widest text-gray-500 font-bold">Subtotal</span>
                                <div class="text-right">
                                    <p class="text-3xl font-black text-black tracking-tighter">
                                        {{ subtotal.toFixed(2) }} 
                                        <span class="text-sm text-gray-400 ml-1 uppercase">EGP</span>
                                    </p>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <Link :href="route('checkout')" class="w-full bg-[#ff2d55] text-white h-16 rounded-2xl flex items-center justify-center font-black uppercase tracking-[0.2em] text-xs shadow-xl shadow-[#ff2d55]/20 hover:bg-[#e11d48] transition-all transform hover:-translate-y-1">
                                    {{ cart.t('checkout') }}
                                </Link>
                                <button @click="$emit('close')" class="w-full text-[10px] font-bold uppercase tracking-widest text-gray-400 hover:text-black transition-colors">Continue Shopping</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
