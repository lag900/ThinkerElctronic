<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import Navbar from '@/Components/Thinker/Navbar.vue';
import CartModal from '@/Components/Thinker/CartModal.vue';
import { cart } from '@/Stores/CartStore';

const props = defineProps({
    page: Object,
});

const isCartOpen = ref(false);
const toggleCart = () => isCartOpen.value = !isCartOpen.value;

const displayTitle = computed(() => cart.language === 'ar' ? props.page.title_ar : props.page.title_en);
const displayContent = computed(() => cart.language === 'ar' ? props.page.content_ar : props.page.content_en);
</script>

<template>
    <Head title="Refund Policy - Thinker" />
    
    <div class="bg-white min-h-screen font-sans selection:bg-red-600 selection:text-white">
        <Navbar @open-cart="toggleCart" />

        <section class="pt-32 pb-16 bg-black text-white relative overflow-hidden">
            <div class="absolute inset-0 opacity-20">
                <img src="/images/hero_bg.png" alt="Tech" class="w-full h-full object-cover grayscale" />
            </div>
            <div class="max-w-7xl mx-auto px-4 relative z-10 text-center">
                <h1 class="text-5xl md:text-7xl font-black tracking-tighter uppercase mb-4" v-html="displayTitle"></h1>
                <p class="text-gray-400 text-xl">{{ cart.language === 'ar' ? 'سياستنا بخصوص الاسترجاع والاسترداد' : 'Our policy regarding refunds and returns' }}</p>
            </div>
        </section>

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="prose prose-lg max-w-none space-y-8 text-gray-700" :dir="cart.language === 'ar' ? 'rtl' : 'ltr'">
                <p class="text-gray-500 text-sm">{{ cart.language === 'ar' ? 'آخر تحديث' : 'Last updated' }}: {{ new Date(page.updated_at).toLocaleDateString() }}</p>

                <div v-html="displayContent" class="dynamic-content"></div>
            </div>

            <div class="mt-12 pt-8 border-t border-gray-100">
                <Link :href="route('home')" class="inline-flex items-center text-red-600 font-bold hover:text-black transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Home
                </Link>
            </div>
        </div>

        <footer class="bg-white border-t border-gray-100 py-12 mt-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row justify-between items-center space-y-6 md:space-y-0 text-center md:text-left">
                    <div class="flex items-center space-x-2">
                        <img src="/images/logo.png" class="w-8 h-8 object-contain" alt="Thinker" />
                        <span class="text-xl font-black tracking-tighter uppercase">Thinker</span>
                    </div>
                    <div class="flex flex-wrap justify-center gap-6 text-sm">
                        <Link :href="route('privacy')" class="text-gray-500 hover:text-red-600 transition-colors">Privacy Policy</Link>
                        <Link :href="route('terms')" class="text-gray-500 hover:text-red-600 transition-colors">Terms of Service</Link>
                        <Link :href="route('refund-policy')" class="text-red-600 font-bold">Refund Policy</Link>
                        <Link :href="route('contact')" class="text-gray-500 hover:text-red-600 transition-colors">Contact</Link>
                    </div>
                    <p class="text-gray-400 text-sm">&copy; 2026 Thinker Inc.</p>
                </div>
            </div>
        </footer>

        <CartModal :is-open="isCartOpen" @close="toggleCart" />
    </div>
</template>
