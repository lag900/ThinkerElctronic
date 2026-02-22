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

const parsedYoutubeId = computed(() => {
    const targetUrl = props.product.video_url || props.product.youtube_url;
    if (!targetUrl || (props.product.video_provider && props.product.video_provider !== 'youtube')) return null;
    const regex = /(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i;
    const match = targetUrl.match(regex);
    return match ? match[1] : null;
});

const isPlaying = ref(false);

const youtubeEmbedUrl = computed(() => {
    if (!parsedYoutubeId.value) return null;
    return `https://www.youtube.com/embed/${parsedYoutubeId.value}?autoplay=1&rel=0&modestbranding=1&origin=${window.location.origin}`;
});

const vimeoId = computed(() => {
    if (props.product.video_provider !== 'vimeo' || !props.product.video_url) return null;
    const regex = /(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/(?:[^\/]*)\/videos\/|album\/(?:\d+)\/video\/|video\/|)(\d+)(?:[a-zA-Z0-9_\-]+)?/i;
    const match = props.product.video_url.match(regex);
    return match ? match[1] : null;
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

const copyStatus = ref({});

const copyCode = (code, index) => {
    navigator.clipboard.writeText(code);
    copyStatus.value[index] = true;
    setTimeout(() => {
        copyStatus.value[index] = false;
    }, 2000);
};
</script>

<template>
    <Head :title="productName + ' - Thinker'" />
    
    <div class="bg-white min-h-screen font-sans selection:bg-brand-600 selection:text-white" :dir="cart.language === 'ar' ? 'rtl' : 'ltr'">
        <Navbar @open-cart="toggleCart" />

        <main class="pt-24 sm:pt-48 max-w-7xl mx-auto px-4 sm:px-8">
            <!-- Breadcrumbs -->
            <nav class="flex items-center gap-2 text-[8px] sm:text-[9px] font-black uppercase tracking-[0.2em] text-ink-muted mb-6 sm:mb-12 overflow-x-auto whitespace-nowrap scrollbar-none">
                <Link :href="route('shop')" class="hover:text-brand-600 transition-colors">{{ cart.language === 'ar' ? 'المتجر' : 'Shop' }}</Link>
                <span class="opacity-30">/</span>
                <span class="text-ink-main">{{ productName }}</span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 sm:gap-16 lg:gap-24 items-start">
                <!-- Gallery: Minimal White -->
                <div class="space-y-6 lg:sticky lg:top-32 w-full max-w-full overflow-hidden">
                    <div class="bg-surface-50 aspect-square rounded-2xl sm:rounded-3xl overflow-hidden border border-border/30 group relative flex items-center justify-center">
                        <img :src="mainImage" :alt="productName" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-105" />
                    </div>
                    <div v-if="galleryImages.length > 1" class="flex gap-2 sm:gap-3 overflow-x-auto pb-4 custom-scrollbar snap-x">
                        <button 
                            v-for="(img, idx) in galleryImages" :key="idx" @click="mainImage = img"
                            class="w-14 h-14 sm:w-16 sm:h-16 rounded-xl overflow-hidden border transition-all shrink-0 snap-start"
                            :class="mainImage === img ? 'border-brand-600 ring-2 ring-brand-600/20' : 'border-border/30 opacity-60 hover:opacity-100'"
                        >
                            <img :src="img" class="w-full h-full object-cover" />
                        </button>
                    </div>

                    <!-- Smart Video Systems -->
                    <div v-if="(product.video_provider === 'youtube' || !product.video_provider) && parsedYoutubeId" class="mt-4 sm:mt-8 rounded-2xl overflow-hidden shadow-sm bg-black group relative aspect-video">
                        <iframe 
                            v-if="isPlaying"
                            :src="youtubeEmbedUrl" 
                            class="w-full h-full" 
                            frameborder="0" 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                            allowfullscreen
                        ></iframe>
                        <template v-else>
                            <img :src="`https://img.youtube.com/vi/${parsedYoutubeId}/maxresdefault.jpg`" class="w-full h-full object-cover opacity-80 group-hover:opacity-50 transition-all duration-700 group-hover:scale-105 cursor-pointer" @click="isPlaying = true" />
                            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                <div @click.stop="isPlaying = true" class="w-16 h-16 sm:w-20 sm:h-20 bg-[#111] rounded-full flex items-center justify-center shadow-[0_0_40px_rgba(0,0,0,0.8)] group-hover:scale-110 group-hover:bg-brand-600 transition-all duration-500 pointer-events-auto cursor-pointer">
                                    <svg class="w-6 h-6 sm:w-8 sm:h-8 text-white ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z" /></svg>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div v-else-if="product.video_provider === 'vimeo' && vimeoId" class="mt-4 sm:mt-8 rounded-2xl overflow-hidden border border-border/30 shadow-sm bg-black">
                        <iframe :src="`https://player.vimeo.com/video/${vimeoId}?color=ff0000&title=0&byline=0&portrait=0`" class="w-full aspect-video" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
                    </div>

                    <div v-else-if="product.video_provider === 'local' && product.video_path" class="mt-4 sm:mt-8 rounded-2xl overflow-hidden border border-border/30 shadow-sm bg-black">
                        <video controls class="w-full aspect-video outline-none" controlsList="nodownload">
                            <source :src="product.video_path" type="video/mp4">
                        </video>
                    </div>
                </div>

                <!-- Product Info -->
                <div class="space-y-8 sm:space-y-12">
                    <div class="space-y-4 sm:space-y-6">
                        <div class="flex items-center gap-3">
                            <span class="px-2.5 py-1 bg-brand-50 text-brand-600 text-[9px] font-black uppercase tracking-widest rounded-md border border-brand-200/30">
                                {{ product.category?.name }}
                            </span>
                        </div>
                        <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold text-[#111] leading-[1.1] tracking-tight">
                            {{ productName }}
                        </h1>
                        <p class="text-lg sm:text-xl text-ink-light leading-relaxed font-medium">
                            {{ productDescription }}
                        </p>
                    </div>

                    <!-- Price & Action -->
                    <div class="p-6 sm:p-10 border border-border/40 rounded-[32px] space-y-8 bg-surface-50/30">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-6">
                            <div class="space-y-1">
                                <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-ink-muted">{{ cart.language === 'ar' ? 'السعر النهائي' : 'Final Price' }}</span>
                                <div class="text-3xl sm:text-4xl font-extrabold text-brand-600 transition-all">
                                    {{ parseFloat(product.price).toLocaleString(undefined, {minimumFractionDigits: 2}) }} 
                                    <span class="text-sm font-medium text-ink-muted ml-1 uppercase">Egp</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 bg-white border border-border/50 rounded-2xl p-1.5 shadow-sm w-full sm:w-auto justify-center">
                                <button @click="decrement" class="w-10 h-10 sm:w-12 sm:h-12 flex items-center justify-center font-bold text-ink-muted hover:text-brand-600 hover:bg-brand-50 rounded-xl transition-all text-lg">-</button>
                                <span class="w-10 sm:w-12 text-center font-extrabold text-lg">{{ quantity }}</span>
                                <button @click="increment" class="w-10 h-10 sm:w-12 sm:h-12 flex items-center justify-center font-bold text-ink-muted hover:text-brand-600 hover:bg-brand-50 rounded-xl transition-all text-lg">+</button>
                            </div>
                        </div>

                        <button 
                            @click="addToCart" :disabled="isOutOfStock"
                            class="w-full h-14 sm:h-16 bg-[#111] hover:bg-brand-600 text-white rounded-2xl font-bold uppercase tracking-[0.25em] text-[10px] sm:text-[11px] transition-all active:scale-[0.98] disabled:opacity-50 disabled:grayscale shadow-xl shadow-black/5"
                        >
                            {{ isOutOfStock ? (cart.language === 'ar' ? 'نفذت الكمية' : 'Out of Stock') : (cart.language === 'ar' ? 'إضافة للسلة' : 'Add to Cart') }}
                        </button>
                    </div>

                    <!-- Vertical Sections -->
                    <div class="space-y-12 sm:space-y-20 pt-8 sm:pt-16 border-t border-border/10">
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
                                <div v-for="(codeBlock, index) in product.codes" :key="codeBlock.id || index" class="bg-[#0d1117] rounded-[32px] overflow-hidden border border-white/5 shadow-2xl">
                                    <div class="flex items-center justify-between px-8 py-5 bg-white/5 border-b border-white/5">
                                        <div class="flex items-center gap-3">
                                            <div class="flex gap-1.5">
                                                <div class="w-2.5 h-2.5 rounded-full bg-red-500/50"></div>
                                                <div class="w-2.5 h-2.5 rounded-full bg-amber-500/50"></div>
                                                <div class="w-2.5 h-2.5 rounded-full bg-emerald-500/50"></div>
                                            </div>
                                            <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest ml-4">{{ codeBlock.title || codeBlock.language }}</span>
                                        </div>
                                        <button @click="copyCode(codeBlock.code, index)" class="text-[10px] font-black transition-all uppercase tracking-widest flex items-center gap-1.5 px-3 py-1.5 rounded-lg border"
                                                :class="copyStatus[index] ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : 'bg-brand-500/10 text-brand-500 border-transparent hover:bg-brand-500 hover:text-white'">
                                            <svg v-if="copyStatus[index]" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                            <svg v-else class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                                            <span v-if="copyStatus[index]">{{ cart.language === 'ar' ? 'تم النسخ بنجاح' : 'Copied successfully' }}</span>
                                            <span v-else>{{ cart.language === 'ar' ? 'نسخ الكود' : 'Copy Sequence' }}</span>
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
                            <div class="prose prose-lg max-w-none text-ink-main leading-[1.8] font-medium overflow-hidden break-words prose-p:mb-6 prose-img:max-w-full prose-img:rounded-[32px] prose-img:shadow-sm prose-img:my-8 prose-headings:font-black prose-headings:text-[#111] prose-a:text-brand-600 prose-ul:list-disc prose-ul:pl-6 prose-li:my-2" v-html="productDescription">
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
