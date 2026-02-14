<script setup>
import { cart } from '@/Stores/CartStore';

const props = defineProps({
    isOpen: Boolean
});

const emit = defineEmits(['close']);

</script>

<template>
    <div v-if="isOpen" class="fixed inset-0 z-[100] overflow-hidden" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
        <div class="absolute inset-0 overflow-hidden">
            <!-- Backdrop -->
            <div @click="$emit('close')" class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity animate-in fade-in duration-500"></div>

            <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full" :class="cart.language === 'ar' ? 'left-0 pr-10' : 'right-0 pl-10'">
                <div class="pointer-events-auto w-screen max-w-md transform transition ease-in-out duration-500 sm:duration-700 animate-in" :class="cart.language === 'ar' ? 'slide-in-from-left' : 'slide-in-from-right'">
                    <div class="flex h-full flex-col overflow-y-scroll bg-white shadow-2xl">
                        <div class="flex-1 overflow-y-auto px-4 py-6 sm:px-6">
                            <div class="flex items-start justify-between">
                                <h2 class="text-2xl font-black text-black tracking-tighter uppercase" id="slide-over-title">{{ cart.t('cart') }}</h2>
                                <div class="ml-3 flex h-7 items-center">
                                    <button @click="$emit('close')" type="button" class="relative -m-2 p-2 text-gray-400 hover:text-red-600 transition-colors">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div class="mt-8">
                                <div v-if="cart.items.length === 0" class="text-center py-20 space-y-4">
                                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                        </svg>
                                    </div>
                                    <p class="text-gray-500 font-medium text-lg italic">{{ cart.t('emptyCart') }}</p>
                                    <button @click="$emit('close')" class="text-red-600 font-bold hover:underline">{{ cart.t('startShopping') }}</button>
                                </div>
                                <div v-else class="flow-root">
                                    <ul role="list" class="-my-6 divide-y divide-gray-100">
                                        <li v-for="item in cart.items" :key="item.id" class="flex py-6 group">
                                            <div class="h-24 w-24 flex-shrink-0 overflow-hidden rounded-xl border border-gray-100 bg-gray-50">
                                                <img :src="item.image_url" :alt="item.name" class="h-full w-full object-cover object-center group-hover:scale-110 transition-transform duration-500" />
                                            </div>

                                            <div class="mx-4 flex flex-1 flex-col">
                                                <div>
                                                    <div class="flex justify-between text-base font-bold text-black leading-tight">
                                                        <h3>{{ item.name }}</h3>
                                                        <p class="ml-4">{{ (item.price * item.quantity).toFixed(2) }} <span class="text-[10px] text-gray-400 font-normal">{{ cart.currency }}</span></p>
                                                    </div>
                                                </div>
                                                <div class="flex flex-1 items-end justify-between text-sm">
                                                    <div class="flex items-center border border-gray-100 rounded-lg bg-gray-50 p-1">
                                                        <button @click="cart.updateQuantity(item.id, item.quantity - 1)" class="w-8 h-8 flex items-center justify-center rounded-md hover:bg-white hover:text-red-600 transition-all font-bold">
                                                            -
                                                        </button>
                                                        <input 
                                                            type="number" 
                                                            :value="item.quantity"
                                                            @input="cart.updateQuantity(item.id, $event.target.value)"
                                                            class="w-10 text-center bg-transparent border-none font-black text-black text-sm p-0 focus:ring-0"
                                                        />
                                                        <button @click="cart.updateQuantity(item.id, item.quantity + 1)" class="w-8 h-8 flex items-center justify-center rounded-md hover:bg-white hover:text-red-600 transition-all font-bold">
                                                            +
                                                        </button>
                                                    </div>

                                                    <div class="flex">
                                                        <button @click="cart.removeItem(item.id)" type="button" class="font-bold text-red-600 hover:text-black transition-colors uppercase text-xs tracking-widest">{{ cart.t('remove') }}</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div v-if="cart.items.length > 0" class="border-t border-gray-100 px-4 py-8 sm:px-6 space-y-6 bg-gray-50/50">
                            <div class="flex justify-between text-base font-medium text-gray-900">
                                <p class="text-xs uppercase tracking-widest text-gray-500 font-black">{{ cart.t('subtotal') }}</p>
                                <p class="text-3xl font-black text-black">{{ cart.total.toFixed(2) }} <span class="text-sm font-normal text-gray-400">{{ cart.currency }}</span></p>
                            </div>
                            <div class="mt-6">
                                <a href="#" class="flex items-center justify-center rounded-full border border-transparent bg-red-600 px-6 py-5 text-white hover:bg-black transition-all duration-500 font-black uppercase tracking-widest shadow-2xl shadow-red-600/30 transform hover:-translate-y-1">
                                    {{ cart.t('checkout') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
