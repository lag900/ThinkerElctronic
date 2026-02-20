<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import Navbar from '@/Components/Thinker/Navbar.vue';
import ProductCard from '@/Components/Thinker/ProductCard.vue';
import CartModal from '@/Components/Thinker/CartModal.vue';
import Footer from '@/Components/Thinker/Footer.vue';
import { cart } from '@/Stores/CartStore';

const props = defineProps({
    products: Array,
    categories: Array
});

const isCartOpen = ref(false);
const toggleCart = () => isCartOpen.value = !isCartOpen.value;

const isFilterDrawerOpen = ref(false);
const selectedCategory = ref('');
const minPrice = ref('');
const maxPrice = ref('');
const sortBy = ref('latest');

const filteredProducts = computed(() => {
    let result = props.products.filter(product => {
        const matchesCategory = !selectedCategory.value || product.category_id == selectedCategory.value;
        const matchesMinPrice = !minPrice.value || product.price >= parseFloat(minPrice.value);
        const matchesMaxPrice = !maxPrice.value || product.price <= parseFloat(maxPrice.value);
        return matchesCategory && matchesMinPrice && matchesMaxPrice;
    });

    if (sortBy.value === 'price_asc') {
        result.sort((a, b) => a.price - b.price);
    } else if (sortBy.value === 'price_desc') {
        result.sort((a, b) => b.price - a.price);
    }

    return result;
});

const clearFilters = () => {
    selectedCategory.value = '';
    minPrice.value = '';
    maxPrice.value = '';
    sortBy.value = 'latest';
};
</script>

<template>
    <Head title="Thinker Store - Industrial Inventory" />
    
    <div class="bg-gray-50 min-h-screen font-sans">
        <Navbar @open-cart="toggleCart" />

        <!-- Header -->
        <header class="pt-32 pb-16 bg-white border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-6 sm:px-8">
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                    <div class="space-y-4">
                        <nav class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-widest text-gray-400">
                            <Link href="/" class="hover:text-black">Home</Link>
                            <span>/</span>
                            <span class="text-black">Store</span>
                        </nav>
                        <h1 class="text-5xl font-black text-black tracking-tighter uppercase">Our <span class="text-[#ff2d55]">Hardware.</span></h1>
                    </div>
                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                        Showing {{ filteredProducts.length }} Total Units
                    </div>
                </div>
            </div>
        </header>

        <main class="max-w-7xl mx-auto px-6 sm:px-8 py-12">
            <div class="flex flex-col lg:flex-row gap-12">
                <!-- Sidebar -->
                <aside class="hidden lg:block w-64 shrink-0 space-y-12 h-fit sticky top-32">
                    <!-- Categories -->
                    <div class="space-y-6">
                        <h3 class="text-[11px] font-black uppercase tracking-widest text-black border-b border-gray-100 pb-4">Categories</h3>
                        <div class="space-y-1">
                            <button 
                                @click="selectedCategory = ''"
                                :class="['w-full text-left px-4 py-3 rounded-xl transition-all font-bold text-xs uppercase tracking-widest', selectedCategory === '' ? 'bg-black text-white shadow-lg' : 'text-gray-400 hover:bg-white']"
                            >
                                All Products
                            </button>
                            <button 
                                v-for="cat in categories" 
                                :key="cat.id" 
                                @click="selectedCategory = cat.id"
                                :class="['w-full text-left px-4 py-3 rounded-xl transition-all font-bold text-xs uppercase tracking-widest', selectedCategory === cat.id ? 'bg-[#ff2d55] text-white shadow-lg' : 'text-gray-400 hover:bg-white']"
                            >
                                {{ cat.name }}
                            </button>
                        </div>
                    </div>

                    <!-- Price Filter -->
                    <div class="space-y-6">
                        <h3 class="text-[11px] font-black uppercase tracking-widest text-black border-b border-gray-100 pb-4">Price Range</h3>
                        <div class="grid grid-cols-2 gap-3">
                            <input v-model="minPrice" type="number" placeholder="Min" class="w-full bg-white border-gray-100 rounded-xl px-4 py-3 text-xs font-bold focus:border-[#ff2d55] focus:ring-1 focus:ring-[#ff2d55]" />
                            <input v-model="maxPrice" type="number" placeholder="Max" class="w-full bg-white border-gray-100 rounded-xl px-4 py-3 text-xs font-bold focus:border-[#ff2d55] focus:ring-1 focus:ring-[#ff2d55]" />
                        </div>
                        <button v-if="selectedCategory || minPrice || maxPrice" @click="clearFilters" class="text-[10px] font-bold text-[#ff2d55] uppercase tracking-widest hover:underline">Reset Filters</button>
                    </div>
                </aside>

                <!-- Grid -->
                <div class="flex-1">
                    <div v-if="filteredProducts.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                        <ProductCard 
                            v-for="product in filteredProducts" 
                            :key="product.id" 
                            :product="product" 
                        />
                    </div>
                    <!-- Empty State -->
                    <div v-else class="bg-white rounded-3xl border border-dashed border-gray-200 py-32 text-center">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">No products found for this criteria.</p>
                        <button @click="clearFilters" class="mt-4 text-[#ff2d55] text-xs font-bold uppercase underline">Reset</button>
                    </div>
                </div>
            </div>
        </main>

        <!-- Mobile Filter Sticky -->
        <div class="lg:hidden fixed bottom-8 left-1/2 -translate-x-1/2 z-50">
            <button @click="isFilterDrawerOpen = true" class="bg-black text-white px-8 py-4 rounded-full shadow-2xl font-bold text-[10px] uppercase tracking-widest flex items-center gap-3">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" /></svg>
                Filter Inventory
            </button>
        </div>

        <Footer />
        <CartModal :is-open="isCartOpen" @close="toggleCart" />
    </div>
</template>
