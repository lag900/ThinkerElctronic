<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import Navbar from '@/Components/Thinker/Navbar.vue';
import CartModal from '@/Components/Thinker/CartModal.vue';
import { cart } from '@/Stores/CartStore';

const props = defineProps({
    packages: Array,
});

const isCartOpen = ref(false);
const toggleCart = () => isCartOpen.value = !isCartOpen.value;
</script>

<template>
    <Head :title="cart.language === 'ar' ? 'نقاط Batu LMS - Thinker' : 'Batu LMS Points - Thinker'" />
    
    <div class="bg-white min-h-screen font-sans selection:bg-red-600 selection:text-white">
        <Navbar @open-cart="toggleCart" />

        <section class="pt-32 pb-16 bg-black text-white relative overflow-hidden">
            <div class="absolute inset-0 opacity-20">
                <img src="/images/hero_bg.png" alt="Tech" class="w-full h-full object-cover grayscale" />
            </div>
            <div class="max-w-7xl mx-auto px-4 relative z-10 text-center">
                <h1 class="text-5xl md:text-7xl font-black tracking-tighter uppercase mb-4">
                    Batu LMS <span class="text-red-600 italic">Points</span>
                </h1>
                <p class="text-gray-400 text-xl max-w-2xl mx-auto uppercase tracking-widest font-bold">
                    {{ cart.language === 'ar' ? 'اشترِ نقاطًا لحساب LMS الخاص بك' : 'Purchase points for your LMS account' }}
                </p>
            </div>
        </section>

        <section class="py-24 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16 space-y-4">
                    <p class="text-gray-600 text-lg leading-relaxed max-w-3xl mx-auto" dir="auto">
                        {{ cart.language === 'ar' ? 'يتم استخدام هذه النقاط في نظام الطباعة الذكية بجامعة برج العرب التكنولوجية بالإسكندرية - ودفع تكلفة بعض المنتجات المعروضة على المنصة الخاصة بالجامعة.' : 'These points are used in the smart printing system at Borg El Arab Technological University in Alexandria - and to pay for some products displayed on the university\'s platform.' }}
                    </p>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-8">
                    <div v-for="pkg in packages" :key="pkg.id" 
                        class="bg-white rounded-[40px] p-6 sm:p-10 text-center border border-gray-100 shadow-xl shadow-gray-200/50 hover:shadow-2xl hover:shadow-red-500/10 transition-all duration-500 group relative overflow-hidden"
                    >
                        <div class="absolute top-0 right-0 w-32 h-32 bg-red-500/5 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-700"></div>
                        
                        <div class="relative z-10">
                            <div class="w-20 h-20 bg-black text-white rounded-3xl mx-auto flex items-center justify-center text-2xl font-black mb-6 group-hover:bg-red-600 transition-colors">
                                {{ pkg.points }}
                            </div>
                            <h3 class="text-3xl font-black text-black tracking-tight mb-2">{{ pkg.points }} <span class="text-sm uppercase tracking-widest text-gray-400">pts</span></h3>
                            <p class="text-2xl font-bold text-red-600 mb-8">{{ pkg.price }}</p>
                            
                            <a v-if="pkg.payment_link" :href="pkg.payment_link" target="_blank"
                                class="inline-block w-full py-4 bg-black text-white rounded-2xl font-black uppercase tracking-widest text-xs hover:bg-red-600 transition-all shadow-lg shadow-black/10 active:scale-95"
                            >
                                {{ cart.language === 'ar' ? 'شراء النقاط' : 'Purchase Now' }}
                            </a>
                            <div v-else class="w-full py-4 bg-gray-100 text-gray-400 rounded-2xl font-black uppercase tracking-widest text-[10px]">
                                {{ cart.language === 'ar' ? 'الرابط غير متوفر' : 'Link not set' }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-20 p-10 bg-white rounded-[40px] border border-gray-100 shadow-sm text-center">
                    <p class="text-gray-500 text-sm">
                        {{ cart.language === 'ar' ? 'بإتمام عملية الشراء، فإنك توافق على شروط الخدمة وسياسة الاسترجاع الخاصة بنا.' : 'By completing a purchase, you agree to our Terms of Service and Refund Policy.' }}
                    </p>
                </div>
            </div>
        </section>

        <footer class="bg-white border-t border-gray-100 py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row justify-between items-center space-y-6 md:space-y-0 text-center md:text-left">
                    <div class="flex items-center space-x-2">
                        <img src="/images/logo.png" class="w-8 h-8 object-contain" alt="Thinker" />
                        <span class="text-xl font-black tracking-tighter uppercase">Thinker</span>
                    </div>
                    <div class="flex flex-wrap justify-center gap-6 text-sm">
                        <Link :href="route('privacy')" class="text-gray-500 hover:text-red-600 transition-colors">Privacy Policy</Link>
                        <Link :href="route('terms')" class="text-gray-500 hover:text-red-600 transition-colors">Terms of Service</Link>
                        <Link :href="route('refund-policy')" class="text-gray-500 hover:text-red-600 transition-colors">Refund Policy</Link>
                        <Link :href="route('contact')" class="text-gray-500 hover:text-red-600 transition-colors">Contact</Link>
                    </div>
                    <p class="text-gray-400 text-sm">&copy; 2026 Thinker Inc.</p>
                </div>
            </div>
        </footer>

        <CartModal :is-open="isCartOpen" @close="toggleCart" />
    </div>
</template>
