<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import Navbar from '@/Components/Thinker/Navbar.vue';
import ProductCard from '@/Components/Thinker/ProductCard.vue';
import CartModal from '@/Components/Thinker/CartModal.vue';
import Footer from '@/Components/Thinker/Footer.vue';
import { cart } from '@/Stores/CartStore';

const props = defineProps({
    products: Array,
    categories: Array,
    lmsPackages: {
        type: Array,
        default: () => []
    }
});

const isCartOpen = ref(false);
const toggleCart = () => isCartOpen.value = !isCartOpen.value;

const formatNumber = (val) => {
    return new Intl.NumberFormat(cart.language === 'ar' ? 'ar-EG' : 'en-US').format(val);
};
</script>

<template>
    <Head title="Thinker - Intelligence Hub" />
    
    <div class="bg-gray-50 min-h-screen font-sans" :dir="cart.language === 'ar' ? 'rtl' : 'ltr'">
        <Navbar @open-cart="toggleCart" />

        <!-- Simple Hero -->
        <header class="pt-32 pb-20 bg-white border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-6 sm:px-8">
                <div class="max-w-3xl space-y-8">
                    <h1 class="text-6xl sm:text-8xl font-black text-black leading-tight uppercase tracking-tighter">
                        Smart <span class="text-[#ff2d55]">Thinking.</span><br/>
                        Smart Systems.
                    </h1>
                    <p class="text-xl text-gray-500 leading-relaxed font-medium">
                        {{ cart.language === 'ar' ? 'عالم من الحلول التقنية والهندسية المصممة خصيصاً لاحتياجاتك.' : 'A world of technical and engineering solutions tailored specifically to your needs.' }}
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <Link :href="route('shop')" class="bg-black text-white px-10 py-5 rounded-2xl font-bold uppercase tracking-widest hover:bg-gray-800 transition-all">
                            {{ cart.t('shopNow') }}
                        </Link>
                    </div>
                </div>
            </div>
        </header>

        <!-- Category Grid -->
        <section v-if="categories && categories.length > 0" class="py-24 bg-white">
            <div class="max-w-7xl mx-auto px-6 sm:px-8">
                <h2 class="text-3xl font-black text-black uppercase tracking-tighter mb-12">Browse Categories</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <Link 
                        v-for="cat in categories" 
                        :key="cat.id"
                        :href="route('shop', { category: cat.id })"
                        class="group bg-gray-50 p-8 rounded-3xl border border-gray-100 hover:bg-white hover:shadow-xl transition-all duration-300 text-center"
                    >
                        <div class="w-20 h-20 mx-auto mb-6 bg-white rounded-2xl p-4 shadow-sm group-hover:scale-110 transition-transform">
                            <img v-if="cat.image" :src="cat.image" class="w-full h-full object-contain">
                            <div v-else class="w-full h-full flex items-center justify-center text-gray-200 text-2xl">📦</div>
                        </div>
                        <h3 class="text-sm font-black text-black uppercase tracking-tight">{{ cat.name }}</h3>
                        <p class="mt-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                            {{ formatNumber(cat.products_count || 0) }} Units
                        </p>
                    </Link>
                </div>
            </div>
        </section>

        <!-- Featured Products -->
        <section class="py-24 bg-gray-50">
            <div class="max-w-7xl mx-auto px-6 sm:px-8">
                <div class="flex items-end justify-between mb-12">
                    <h2 class="text-3xl font-black text-black uppercase tracking-tighter">Featured Hardware</h2>
                    <Link :href="route('shop')" class="text-sm font-bold text-[#ff2d55] hover:underline">View All -></Link>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    <ProductCard 
                        v-for="product in products" 
                        :key="product.id" 
                        :product="product" 
                    />
                </div>
            </div>
        </section>

        <!-- LMS Packages -->
        <section class="py-24 bg-black text-white">
            <div class="max-w-7xl mx-auto px-6 sm:px-8 text-center space-y-16">
                <div class="max-w-2xl mx-auto space-y-4">
                    <h2 class="text-5xl font-black tracking-tighter uppercase leading-none">
                        BATU LMS <br/> <span class="text-[#ff2d55]">Access.</span>
                    </h2>
                    <p class="text-lg text-gray-400">Select your credit package to synchronize with the learning matrix.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div
                        v-for="pkg in lmsPackages"
                        :key="pkg.id"
                        class="p-12 bg-white/5 rounded-3xl border border-white/10 hover:border-[#ff2d55]/50 transition-all group"
                    >
                        <div class="text-5xl font-black tracking-tighter mb-4">{{ formatNumber(pkg.points) }}</div>
                        <div class="text-[10px] font-bold text-[#ff2d55] uppercase tracking-[0.3em] mb-8">Points</div>
                        <div class="w-full h-px bg-white/10 mb-8"></div>
                        <div class="flex items-center justify-between">
                            <span class="text-2xl font-black">{{ formatNumber(pkg.price) }} EGP</span>
                            <a v-if="pkg.paymentLink" :href="pkg.paymentLink" target="_blank" class="bg-white text-black px-6 py-3 rounded-xl font-bold text-[10px] uppercase hover:bg-[#ff2d55] hover:text-white transition-all">Buy</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <Footer />
        <CartModal :is-open="isCartOpen" @close="toggleCart" />
    </div>
</template>
