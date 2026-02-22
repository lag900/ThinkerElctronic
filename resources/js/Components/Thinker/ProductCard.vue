<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { cart } from '@/Stores/CartStore';
import { system } from '@/Stores/SystemStore';

const props = defineProps({
    product: Object
});

const quantity = ref(1);
const loading = ref(false);
const isOutOfStock = computed(() => (props.product.stock_quantity ?? 0) <= 0);

const increment = () => {
    if (quantity.value < (props.product.stock_quantity || 99)) {
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
    
    // Professional UX delay for premium feel
    setTimeout(() => {
        cart.addItem({
            id: props.product.id,
            name: props.product.name,
            price: props.product.effective_price,
            image_url: props.product.image_url,
            category: props.product.category?.name,
            quantity: quantity.value
        });
        
        loading.value = false;
        system.notify(cart.language === 'ar' ? 'تمت الإضافة للسلة بنجاح' : 'Added to cart successfully');
        
        // Bounce effect logic could be added here if we had a ref for the button animation
    }, 400);
};
</script>

<template>
    <div class="group bg-white rounded-[28px] overflow-hidden shadow-sm hover:shadow-[0_40px_80px_rgba(0,0,0,0.06)] transition-all duration-700 flex flex-col h-full border border-[#ececf2] hover:-translate-y-2 md:max-w-[260px] mx-auto w-full relative">
        
        <!-- Product Image Area -->
        <Link :href="route('product.show', product.id)" class="relative aspect-square overflow-hidden block bg-[#fcfcfc] p-6">
            <img 
                :src="product.image_url" 
                :alt="product.name"
                class="w-full h-full object-contain transition-transform duration-1000 ease-out group-hover:scale-[1.05]"
            >
            
            <!-- Sale Badge -->
            <div v-if="product.is_on_sale" class="absolute top-5 left-5 bg-[#ff2e63] text-white text-[10px] font-black px-3 py-1.5 rounded-xl shadow-lg border border-white/20 uppercase tracking-widest z-10 animate-fade-in">
                -{{ product.discount_percentage }}%
            </div>

            <!-- Stock Alert -->
            <div v-if="isOutOfStock" class="absolute inset-0 bg-white/60 backdrop-blur-[6px] flex items-center justify-center z-20">
                <span class="text-[10px] font-black uppercase text-gray-500 tracking-[0.2em] bg-white/90 px-5 py-2.5 rounded-2xl border border-gray-100 shadow-xl">Sold Out</span>
            </div>
        </Link>

        <!-- Product Details -->
        <div class="p-6 flex-1 flex flex-col relative bg-white">
            <div class="flex items-center justify-between mb-2">
                <span v-if="product.category" class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em]">
                    {{ product.category.name }}
                </span>
                <div v-if="!isOutOfStock" class="flex items-center gap-1.5">
                    <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></div>
                    <span class="text-[9px] font-bold text-emerald-600 uppercase tracking-wider">In Stock</span>
                </div>
            </div>

            <Link :href="route('product.show', product.id)" class="block mb-4 flex-1">
                <h3 class="text-[15px] font-bold text-[#1a1a1a] leading-tight line-clamp-2 transition-colors group-hover:text-[#ff2e63]">
                    {{ cart.language === 'ar' ? (product.name_ar || product.name) : product.name }}
                </h3>
            </Link>

            <!-- Pricing Tier -->
            <div class="mb-5 flex items-end gap-2">
                <template v-if="product.is_on_sale">
                    <span class="text-xl font-black text-[#ff2e63] tracking-tighter">{{ parseFloat(product.effective_price).toLocaleString() }} <small class="text-[10px] uppercase ml-0.5">EGP</small></span>
                    <span class="text-[11px] text-gray-400 line-through mb-1 font-bold">{{ parseFloat(product.price).toLocaleString() }}</span>
                </template>
                <template v-else>
                    <span class="text-xl font-black text-[#1a1a1a] tracking-tighter">{{ parseFloat(product.price).toLocaleString() }} <small class="text-[10px] font-black text-gray-400 uppercase ml-0.5 tracking-tighter">EGP</small></span>
                </template>
            </div>

            <!-- Action Row: Quantity & Cart Button -->
            <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-[#f8f8f8]">
                <!-- Quantity Selector -->
                <div class="flex items-center justify-between bg-gray-50/80 border border-gray-100 rounded-xl p-1 h-[42px] min-w-[100px]" :class="{'opacity-40 pointer-events-none': isOutOfStock}">
                    <button 
                        @click.stop="decrement"
                        class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-black hover:bg-white rounded-lg transition-all active:scale-90 font-bold"
                    >
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M20 12H4" /></svg>
                    </button>
                    <span class="flex-1 text-center text-xs font-black text-[#1a1a1a]">{{ quantity }}</span>
                    <button 
                        @click.stop="increment"
                        class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-black hover:bg-white rounded-lg transition-all active:scale-90 font-bold"
                    >
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M12 4v16m8-8H4" /></svg>
                    </button>
                </div>

                <!-- Add to Cart Button -->
                <button 
                    @click.stop="addToCart"
                    :disabled="isOutOfStock || loading"
                    class="flex-1 h-[42px] flex items-center justify-center gap-2 rounded-xl text-[10px] font-black uppercase tracking-[0.1em] transition-all duration-300 shadow-md group/btn overflow-hidden relative"
                    :class="[
                        isOutOfStock 
                        ? 'bg-gray-100 text-gray-400 cursor-not-allowed shadow-none' 
                        : 'bg-[#ff2e63] text-white hover:bg-[#e60045] hover:scale-[1.03] active:scale-95 shadow-[0_4px_12px_rgba(255,46,99,0.2)]'
                    ]"
                >
                    <template v-if="loading">
                        <div class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                    </template>
                    <template v-else>
                        <svg v-if="!isOutOfStock" class="w-4 h-4 transition-transform group-hover/btn:scale-110" :class="{'animate-bounce': loading}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <span>{{ isOutOfStock ? 'Sold Out' : "Cart" }}</span>
                    </template>
                    
                    <!-- Button Glow Effect on Hover -->
                    <div v-if="!isOutOfStock" class="absolute inset-0 bg-white/10 opacity-0 group-hover/btn:opacity-100 transition-opacity"></div>
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Custom Success Bounce */
@keyframes cart-bounce {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.2); }
}
</style>
