<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import Navbar from '@/Components/Thinker/Navbar.vue';
import ProductCard from '@/Components/Thinker/ProductCard.vue';
import CartModal from '@/Components/Thinker/CartModal.vue';
import { cart } from '@/Stores/CartStore';

const props = defineProps({
    products: Array,
    categories: Array,
    filters: Object
});

const isCartOpen = ref(false);
const toggleCart = () => isCartOpen.value = !isCartOpen.value;

const selectedCategory = ref(props.filters.category || '');
const minPrice = ref(props.filters.min_price || '');
const maxPrice = ref(props.filters.max_price || '');

const applyFilters = () => {
    router.get(route('shop'), {
        category: selectedCategory.value,
        min_price: minPrice.value,
        max_price: maxPrice.value
    }, { preserveState: true, preserveScroll: true });
};

watch([selectedCategory], () => applyFilters());

</script>

<template>
    <Head :title="cart.t('shop') + ' - Thinker'" />
    
    <div class="bg-white min-h-screen font-sans selection:bg-red-600 selection:text-white pb-20">
        <Navbar @open-cart="toggleCart" />

        <!-- Shop Hero -->
        <section class="pt-32 pb-16 bg-black text-white relative overflow-hidden">
            <div class="absolute inset-0 opacity-20">
                <img src="/images/hero_bg.png" alt="Tech" class="w-full h-full object-cover grayscale" />
            </div>
            <div class="max-w-7xl mx-auto px-4 relative z-10 text-center">
                <h1 class="text-5xl md:text-7xl font-black tracking-tighter uppercase mb-4">
                    Explore <span class="text-red-600 italic">Smart</span> Tech
                </h1>
                <p class="text-gray-400 text-xl">{{ cart.t('discover') }}</p>
            </div>
        </section>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12">
            <div class="flex flex-col lg:flex-row gap-12">
                <!-- Sidebar Filters -->
                <aside class="w-full lg:w-64 space-y-10 animate-in slide-in-from-left duration-700">
                    <div class="space-y-6">
                        <h3 class="text-sm font-black uppercase tracking-widest text-black border-b border-gray-100 pb-4">
                            {{ cart.t('categories') }}
                        </h3>
                        <div class="space-y-2">
                            <button 
                                @click="selectedCategory = ''"
                                :class="['w-full text-left px-4 py-3 rounded-xl transition-all font-bold', selectedCategory === '' ? 'bg-red-600 text-white shadow-xl shadow-red-600/20' : 'bg-gray-50 text-black hover:bg-gray-100']"
                            >
                                All Products
                            </button>
                            <button 
                                v-for="cat in categories" 
                                :key="cat.id"
                                @click="selectedCategory = cat.id"
                                :class="['w-full text-left px-4 py-3 rounded-xl transition-all font-bold', selectedCategory === cat.id ? 'bg-red-600 text-white shadow-xl shadow-red-600/20' : 'bg-gray-50 text-black hover:bg-gray-100']"
                            >
                                {{ cat.name }}
                            </button>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <h3 class="text-sm font-black uppercase tracking-widest text-black border-b border-gray-100 pb-4">
                            {{ cart.t('filter') }}
                        </h3>
                        <div class="grid grid-cols-2 gap-2">
                            <input v-model="minPrice" type="number" placeholder="Min" class="bg-gray-50 border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-red-600" />
                            <input v-model="maxPrice" type="number" placeholder="Max" class="bg-gray-50 border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-red-600" />
                        </div>
                        <button @click="applyFilters" class="w-full bg-black text-white py-3 rounded-xl font-black uppercase tracking-widest text-xs hover:bg-red-600 transition-colors">
                            Apply
                        </button>
                    </div>
                </aside>

                <!-- Product Grid -->
                <main class="flex-1">
                    <div v-if="products.length > 0" class="grid md:grid-cols-2 xl:grid-cols-3 gap-8">
                        <ProductCard 
                            v-for="product in products" 
                            :key="product.id" 
                            :product="product" 
                        />
                    </div>
                    <div v-else class="text-center py-20 bg-gray-50 rounded-3xl border-2 border-dashed border-gray-200">
                        <p class="text-gray-400 font-bold italic">No products found matching your criteria.</p>
                    </div>
                </main>
            </div>
        </div>

        <CartModal :is-open="isCartOpen" @close="toggleCart" />
    </div>
</template>
