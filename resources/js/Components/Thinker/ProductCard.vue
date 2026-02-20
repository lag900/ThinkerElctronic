<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { cart } from '@/Stores/CartStore';

const props = defineProps({
    product: Object
});

const quantity = ref(1);
const loading = ref(false);

const isOutOfStock = computed(() => (props.product.stock_quantity ?? 0) <= 0);

const increment = () => {
    if (quantity.value < props.product.stock_quantity) {
        quantity.value++;
    }
};

const decrement = () => {
    if (quantity.value > 1) {
        quantity.value--;
    }
};

const addToCart = async () => {
    if (isOutOfStock.value || loading.value) return;
    
    loading.value = true;
    
    // Simulate professional loading for better UX
    setTimeout(() => {
        cart.addItem({
            id: props.product.id,
            name: props.product.name,
            price: props.product.price,
            image_url: props.product.image_url,
            category: props.product.category?.name,
            quantity: quantity.value
        });
        loading.value = false;
        quantity.value = 1; // Reset after adding
    }, 600);
};
</script>

<template>
    <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col h-full border border-gray-50">
        <!-- Product Image -->
        <Link :href="route('product.show', product.id)" class="relative aspect-square overflow-hidden block">
            <img 
                :src="product.image_url" 
                class="w-full h-full object-cover transition-transform duration-500 hover:scale-105"
            >
            <div v-if="product.category" class="absolute top-4 left-4">
                <span class="bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-[10px] font-bold text-black uppercase shadow-sm">
                    {{ product.category.name }}
                </span>
            </div>
        </Link>

        <!-- Product Details -->
        <div class="p-6 flex-1 flex flex-col">
            <Link :href="route('product.show', product.id)" class="block mb-4">
                <h3 class="text-sm font-black text-black uppercase tracking-tight line-clamp-2 leading-tight">
                    {{ cart.language === 'ar' ? (product.name_ar || product.name) : product.name }}
                </h3>
            </Link>

            <div class="mt-auto space-y-6">
                <div class="flex items-baseline gap-1">
                    <span class="text-2xl font-black text-[#ff2d55]">{{ product.price }}</span>
                    <span class="text-[10px] font-bold text-gray-400 uppercase">EGP</span>
                </div>

                <div class="flex items-center justify-between gap-4">
                    <!-- Quantity Selector (Logic Integrated) -->
                    <div class="flex items-center bg-gray-50 rounded-full px-4 py-2 border border-gray-100 flex-1 justify-between" :class="{'opacity-50 pointer-events-none': isOutOfStock}">
                        <button 
                            @click="decrement" 
                            class="text-gray-400 font-bold text-lg hover:text-black transition-colors"
                            :disabled="quantity <= 1 || isOutOfStock"
                        >-</button>
                        <span class="text-xs font-black w-4 text-center">{{ quantity }}</span>
                        <button 
                            @click="increment" 
                            class="text-gray-400 font-bold text-lg hover:text-black transition-colors"
                            :disabled="quantity >= product.stock_quantity || isOutOfStock"
                        >+</button>
                    </div>
                    
                    <!-- Add to Cart Button (Logic Integrated) -->
                    <button 
                        @click="addToCart"
                        :disabled="isOutOfStock || loading"
                        class="w-14 h-14 bg-[#ff2d55] rounded-2xl flex items-center justify-center text-white shadow-lg shadow-[#ff2d55]/30 hover:bg-[#e11d48] active:scale-95 transition-all relative disabled:bg-gray-300 disabled:shadow-none disabled:cursor-not-allowed"
                    >
                        <template v-if="!loading">
                            <svg v-if="!isOutOfStock" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            <span v-else class="text-[8px] font-black uppercase text-center leading-tight">Out of<br>Stock</span>
                        </template>
                        <div v-else class="w-6 h-6 border-4 border-white/20 border-t-white rounded-full animate-spin"></div>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
