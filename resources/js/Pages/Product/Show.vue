<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import Navbar from '@/Components/Thinker/Navbar.vue';
import ProductCard from '@/Components/Thinker/ProductCard.vue';
import CartModal from '@/Components/Thinker/CartModal.vue';
import Footer from '@/Components/Thinker/Footer.vue';
import { cart } from '@/Stores/CartStore';

const props = defineProps({
    product: Object,
    relatedProducts: Array
});

const isCartOpen = ref(false);
const toggleCart = () => isCartOpen.value = !isCartOpen.value;

const quantity = ref(1);
const increment = () => { if (quantity.value < props.product.stock_quantity) quantity.value++; };
const decrement = () => { if (quantity.value > 1) quantity.value--; };

const youtubeId = computed(() => {
    if (!props.product.youtube_url) return null;
    const regex = /(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i;
    const match = props.product.youtube_url.match(regex);
    return match ? match[1] : null;
});

const youtubeEmbedUrl = computed(() => {
    if (!youtubeId.value) return null;
    return `https://www.youtube.com/embed/${youtubeId.value}?rel=0&modestbranding=1&origin=${window.location.origin}`;
});

const addToCart = () => {
    if (props.product.stock_quantity > 0) {
        cart.addItem({ ...props.product, quantity: quantity.value });
        isCartOpen.value = true;
    }
};

const mainImage = ref(props.product.image_url);

const galleryImages = computed(() => {
    const imgs = [props.product.image_url];
    if (props.product.images && props.product.images.length > 0) {
        props.product.images.forEach(img => imgs.push(img.image_path));
    }
    return imgs.filter(Boolean);
});

const isOutOfStock = computed(() => props.product.stock_quantity <= 0);

// Localization Helpers
const productName = computed(() => {
    return cart.language === 'ar' && props.product.name_ar ? props.product.name_ar : props.product.name;
});

const productDescription = computed(() => {
    if (cart.language === 'ar' && props.product.description_ar) return props.product.description_ar;
    if (cart.language === 'en' && props.product.description_en) return props.product.description_en;
    return props.product.description; // Fallback
});

const copyCode = (code) => {
    navigator.clipboard.writeText(code);
    alert(cart.language === 'ar' ? 'تم نسخ الكود!' : 'Code copied!');
};
</script>

<template>
    <Head :title="productName + ' - Thinker'" />
    
    <div class="bg-white min-h-screen font-sans selection:bg-brand-600 selection:text-white" :dir="cart.language === 'ar' ? 'rtl' : 'ltr'">
        <Navbar @open-cart="toggleCart" />

        <main class="pt-48 max-w-7xl mx-auto px-6 sm:px-8">
            <!-- Breadcrumbs -->
            <nav class="flex items-center gap-2 text-[9px] font-black uppercase tracking-[0.2em] text-ink-muted mb-12">
                <Link :href="route('shop')" class="hover:text-brand-600 transition-colors">{{ cart.language === 'ar' ? 'المتجر' : 'Shop' }}</Link>
                <span class="opacity-30">/</span>
                <span class="text-ink-main">{{ productName }}</span>
            </nav>

            <div class="grid lg:grid-cols-2 gap-16 lg:gap-24 items-start">
                <!-- Gallery: Minimal White -->
                <div class="space-y-6 sticky top-32">
                    <div class="bg-surface-50 aspect-square rounded-2xl overflow-hidden border border-border/30 group relative">
                        <img :src="mainImage" :alt="productName" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-105" />
                    </div>
                    <div v-if="galleryImages.length > 1" class="flex gap-3 overflow-x-auto pb-2 custom-scrollbar">
                        <button 
                            v-for="(img, idx) in galleryImages" :key="idx" @click="mainImage = img"
                            class="w-16 h-16 rounded-xl overflow-hidden border transition-all shrink-0"
                            :class="mainImage === img ? 'border-brand-600 ring-2 ring-brand-600/20' : 'border-border/30 opacity-60 hover:opacity-100'"
                        >
                            <img :src="img" class="w-full h-full object-cover" />
                        </button>
                    </div>

                    <!-- YouTube Video (Optional) -->
                    <div v-if="youtubeId" class="mt-8 rounded-2xl overflow-hidden border border-border/30 shadow-sm bg-black group relative">
                        <iframe 
                            class="w-full aspect-video" 
                            :src="youtubeEmbedUrl" 
                            frameborder="0" 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                            allowfullscreen
                        ></iframe>
                        <div class="absolute inset-0 flex items-center justify-center bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                            <a :href="product.youtube_url" target="_blank" class="pointer-events-auto bg-white/90 backdrop-blur px-6 py-3 rounded-full text-[10px] font-black uppercase tracking-widest text-black hover:bg-red-600 hover:text-white transition-all">
                                {{ cart.language === 'ar' ? 'مشاهدة على يوتيوب' : 'Watch on YouTube' }}
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Product Info -->
                <div class="space-y-12">
                    <div class="space-y-6">
                        <div class="flex items-center gap-3">
                            <span class="px-2 py-0.5 bg-brand-50 text-brand-600 text-[8px] font-black uppercase tracking-widest rounded-md border border-brand-200/50">
                                {{ product.category?.name }}
                            </span>
                        </div>
                        <h1 class="text-4xl md:text-6xl font-extrabold text-[#111] leading-[1.1] tracking-tight">
                            {{ productName }}
                        </h1>
                        <!-- Short Description (First paragraph or truncated) -->
                        <p class="text-xl text-ink-light leading-relaxed font-medium">
                            {{ productDescription }}
                        </p>
                    </div>

                    <!-- Price & Action -->
                    <div class="p-10 border border-border/40 rounded-3xl space-y-8 bg-surface-50/30">
                        <div class="flex justify-between items-end">
                            <div class="space-y-1">
                                <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-ink-muted">{{ cart.language === 'ar' ? 'السعر النهائي' : 'Final Price' }}</span>
                                <div class="text-4xl font-extrabold text-brand-600 transition-all">{{ parseFloat(product.price).toLocaleString(undefined, {minimumFractionDigits: 2}) }} <span class="text-sm font-medium text-ink-muted ml-1 uppercase">Egp</span></div>
                            </div>
                            <div class="flex items-center gap-2 bg-white border border-border/50 rounded-2xl p-1.5 shadow-sm">
                                <button @click="decrement" class="w-12 h-12 flex items-center justify-center font-bold text-ink-muted hover:text-brand-600 hover:bg-brand-50 rounded-xl transition-all text-xl">-</button>
                                <span class="w-12 text-center font-extrabold text-lg">{{ quantity }}</span>
                                <button @click="increment" class="w-12 h-12 flex items-center justify-center font-bold text-ink-muted hover:text-brand-600 hover:bg-brand-50 rounded-xl transition-all text-xl">+</button>
                            </div>
                        </div>

                        <button 
                            @click="addToCart" :disabled="isOutOfStock"
                            class="w-full h-16 bg-[#111] hover:bg-brand-600 text-white rounded-2xl font-bold uppercase tracking-[0.25em] text-[11px] transition-all active:scale-95 disabled:opacity-50 disabled:grayscale shadow-2xl shadow-black/10"
                        >
                            {{ isOutOfStock ? (cart.language === 'ar' ? 'نفذت الكمية' : 'Out of Stock') : (cart.language === 'ar' ? 'إضافة للسلة' : 'Add to Cart') }}
                        </button>
                    </div>

                    <!-- Vertical Sections -->
                    <div class="space-y-20 pt-16 border-t border-border/20">
                        <!-- SPECS SECTION -->
                        <div v-if="product.specs && product.specs.length > 0" class="space-y-8">
                            <h3 class="text-xs font-black uppercase tracking-[0.4em] text-ink-muted flex items-center gap-4">
                                {{ cart.language === 'ar' ? 'المواصفات الفنية' : 'Technical Specifications' }}
                                <div class="h-px bg-border/20 flex-1"></div>
                            </h3>
                            <div class="grid grid-cols-1 gap-px bg-border/10 rounded-[32px] border border-border/20 overflow-hidden shadow-sm">
                                <div v-for="spec in product.specs" :key="spec.id" class="flex justify-between items-center p-6 bg-white hover:bg-surface-50 transition-colors">
                                    <span class="text-[11px] font-bold text-ink-muted uppercase tracking-widest">{{ spec.label }}</span>
                                    <span class="text-sm font-bold text-ink-main">{{ spec.value }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- CODE BLOCK SECTION -->
                        <div v-if="product.codes && product.codes.length > 0" class="space-y-8">
                            <h3 class="text-xs font-black uppercase tracking-[0.4em] text-ink-muted flex items-center gap-4">
                                {{ cart.language === 'ar' ? 'كود التشغيل' : 'Production Code' }}
                                <div class="h-px bg-border/20 flex-1"></div>
                            </h3>
                            <div class="space-y-6">
                                <div v-for="codeBlock in product.codes" :key="codeBlock.id" class="bg-[#0d1117] rounded-[32px] overflow-hidden border border-white/5 shadow-2xl">
                                    <div class="flex items-center justify-between px-8 py-5 bg-white/5 border-b border-white/5">
                                        <div class="flex items-center gap-3">
                                            <div class="flex gap-1.5">
                                                <div class="w-2.5 h-2.5 rounded-full bg-red-500/50"></div>
                                                <div class="w-2.5 h-2.5 rounded-full bg-amber-500/50"></div>
                                                <div class="w-2.5 h-2.5 rounded-full bg-emerald-500/50"></div>
                                            </div>
                                            <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest ml-4">{{ codeBlock.title || codeBlock.language }}</span>
                                        </div>
                                        <button @click="copyCode(codeBlock.code)" class="text-[10px] font-black text-brand-500 hover:text-white transition-colors uppercase tracking-widest">
                                            {{ cart.language === 'ar' ? 'نسخ الكود' : 'Copy Sequence' }}
                                        </button>
                                    </div>
                                    <pre class="p-8 text-[13px] font-mono text-gray-300 overflow-x-auto custom-scrollbar leading-relaxed"><code>{{ codeBlock.code }}</code></pre>
                                </div>
                            </div>
                        </div>

                        <!-- DESCRIPTION SECTION -->
                        <div class="space-y-8">
                            <h3 class="text-xs font-black uppercase tracking-[0.4em] text-ink-muted flex items-center gap-4">
                                {{ cart.language === 'ar' ? 'نظرة عامة' : 'Detailed Overview' }}
                                <div class="h-px bg-border/20 flex-1"></div>
                            </h3>
                            <div class="prose prose-lg max-w-none text-ink-light leading-[1.8] font-medium">
                                <p class="whitespace-pre-line">{{ productDescription }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related -->
            <section class="mt-40 pt-24 border-t border-border/30">
                <h3 class="text-3xl font-black text-ink-main uppercase tracking-tighter mb-16">
                    {{ cart.language === 'ar' ? 'منتجات مشابهة' : 'Related Products.' }}
                </h3>
                <div class="grid md:grid-cols-4 gap-8">
                    <ProductCard v-for="item in relatedProducts" :key="item.id" :product="item" />
                </div>
            </section>
        </main>

        <Footer />
        <CartModal :is-open="isCartOpen" @close="toggleCart" />
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    height: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #e2e8f0;
    border-radius: 4px;
}
</style>
