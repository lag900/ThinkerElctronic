<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import Navbar from '@/Components/Thinker/Navbar.vue';
import CartModal from '@/Components/Thinker/CartModal.vue';
import Footer from '@/Components/Thinker/Footer.vue';
import { cart } from '@/Stores/CartStore';

const props = defineProps({
    page: Object,
});

const isCartOpen = ref(false);
const toggleCart = () => isCartOpen.value = !isCartOpen.value;

const contactData = computed(() => {
    try {
        const content = cart.language === 'ar' ? props.page.content_ar : props.page.content_en;
        return typeof content === 'string' ? JSON.parse(content) : content;
    } catch (e) {
        return {};
    }
});

const displayTitle = computed(() => cart.language === 'ar' ? props.page.title_ar : props.page.title_en);
</script>

<template>
    <Head title="Contact Us - Thinker" />
    
    <div class="bg-white min-h-screen font-sans selection:bg-brand-600 selection:text-white pb-20">
        <Navbar @open-cart="toggleCart" />

        <header class="pt-48 pb-20 bg-surface-50 border-b border-border/50">
            <div class="max-w-7xl mx-auto px-6 sm:px-8">
                <div class="space-y-6">
                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-brand-50 rounded-full border border-brand-100">
                        <span class="text-[9px] font-black uppercase tracking-widest text-brand-600">Sync with Humans</span>
                    </div>
                    <h1 class="text-5xl md:text-8xl font-black text-ink-main uppercase tracking-tighter leading-none" v-html="displayTitle"></h1>
                    <p class="text-xl text-ink-light max-w-xl">{{ contactData.description }}</p>
                </div>
            </div>
        </header>

        <main class="max-w-7xl mx-auto px-6 sm:px-8 py-20">
            <div class="grid lg:grid-cols-2 gap-16">
                <div class="space-y-12">
                     <div class="glass-card p-10 bg-white space-y-8">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-brand-50 rounded-2xl flex items-center justify-center text-brand-600 shadow-sm">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                            </div>
                            <div>
                                <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-ink-light mb-1">{{ cart.language === 'ar' ? 'الخط الآمن' : 'Secure Line' }}</h3>
                                <a :href="'tel:' + (contactData.phone || '').replace(/\s/g, '')" class="text-2xl font-black text-ink-main hover:text-brand-600 transition-colors">
                                    {{ contactData.phone }}
                                </a>
                                <p v-if="contactData.whatsapp" class="text-xs font-bold text-emerald-500 mt-1">WhatsApp: {{ contactData.whatsapp }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-brand-50 rounded-2xl flex items-center justify-center text-brand-600 shadow-sm">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                             <div>
                                <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-ink-light mb-1">{{ cart.language === 'ar' ? 'البريد الإلكتروني' : 'Mail Protocol' }}</h3>
                                <p class="text-xl font-black text-ink-main uppercase tracking-widest">{{ contactData.email }}</p>
                            </div>
                        </div>

                         <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-brand-50 rounded-2xl flex items-center justify-center text-brand-600 shadow-sm">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            </div>
                             <div>
                                <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-ink-light mb-1">{{ cart.language === 'ar' ? 'الموقع الجغرافي' : 'HQ Architecture' }}</h3>
                                <p class="text-xl font-black text-ink-main uppercase tracking-widest">{{ contactData.address }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-8">
                    <div v-if="contactData.map_embed" v-html="contactData.map_embed" class="w-full h-96 rounded-[40px] overflow-hidden grayscale hover:grayscale-0 transition-all duration-700"></div>
                    <div class="flex flex-col gap-4">
                        <Link :href="route('home')" class="btn-secondary w-fit !px-8">
                            <svg class="w-5 h-5 mr-2 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                            Return to Interface
                        </Link>
                    </div>
                </div>
            </div>
        </main>

        <Footer />

        <CartModal :is-open="isCartOpen" @close="toggleCart" />
    </div>
</template>
