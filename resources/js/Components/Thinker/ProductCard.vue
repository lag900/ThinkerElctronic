<script setup>
import { cart } from '@/Stores/CartStore';

const props = defineProps({
    product: Object
});

const addToCart = () => {
    cart.addItem(props.product);
};
</script>

<template>
    <div class="group relative bg-white rounded-3xl p-6 border border-gray-100 hover:border-red-100 transition-all duration-500 hover:shadow-[0_30px_60px_-15px_rgba(227,27,35,0.1)] overflow-hidden">
        <!-- Product Image -->
        <div class="aspect-square w-full overflow-hidden rounded-2xl bg-gray-50 relative">
            <img 
                :src="product.image_url" 
                :alt="product.name"
                class="h-full w-full object-cover object-center transform group-hover:scale-110 transition-transform duration-700"
            />
            
            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/5 transition-colors duration-500"></div>
            
            <!-- Category Tag -->
            <div class="absolute top-4 left-4" v-if="product.category">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black bg-black text-white uppercase tracking-widest">
                    {{ product.category.name }}
                </span>
            </div>
        </div>

        <!-- Product Info -->
        <div class="mt-6 space-y-2">
            <h3 class="text-xl font-bold text-black group-hover:text-red-600 transition-colors">
                {{ product.name }}
            </h3>
            <p class="text-sm text-gray-500 line-clamp-2 h-10">
                {{ product.description }}
            </p>
            
            <div class="flex items-center justify-between pt-4">
                <span class="text-2xl font-black text-black">
                    {{ parseFloat(product.price).toFixed(2) }} <span class="text-sm font-normal text-gray-500">{{ cart.currency }}</span>
                </span>
                
                <button 
                    @click="addToCart"
                    class="h-12 w-12 bg-black hover:bg-red-600 rounded-full flex items-center justify-center text-white transition-all duration-300 transform active:scale-95 shadow-lg group-hover:rotate-6"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                </button>
            </div>
        </div>
        
        <!-- Decorative background glow -->
        <div class="absolute -bottom-10 -right-10 w-32 h-32 bg-red-600/5 rounded-full blur-2xl group-hover:bg-red-600/10 transition-all duration-500"></div>
    </div>
</template>
