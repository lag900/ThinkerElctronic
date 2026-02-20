<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import { cart } from '@/Stores/CartStore';
import Navbar from '@/Components/Thinker/Navbar.vue';
import Footer from '@/Components/Thinker/Footer.vue';
import { onMounted, ref, computed, watch } from 'vue';
import { EGYPT_REGIONS } from '@/Constants/EgyptData';
import axios from 'axios';
import SearchableSelect from '@/Components/Thinker/SearchableSelect.vue';

const props = defineProps({
    savedCustomer: Object
});

const isCartOpen = ref(false);
const toggleCart = () => isCartOpen.value = !isCartOpen.value;

const form = useForm({
    customer_name: props.savedCustomer?.name || '',
    customer_email: props.savedCustomer?.email || '',
    customer_phone: props.savedCustomer?.phone || '',
    shipping_address: props.savedCustomer?.address || '',
    city: props.savedCustomer?.city || '',
    province: props.savedCustomer?.province || '',
    country: 'Egypt',
    payment_method: 'cod',
    notes: '',
    items: cart.items
});

const selectedGov = computed(() => {
    return EGYPT_REGIONS.find(g => g.name_ar === form.province || g.name_en === form.province);
});

const availableAreas = computed(() => {
    return selectedGov.value ? selectedGov.value.areas : [];
});

// Clear city if province changes
watch(() => form.province, () => {
    form.city = '';
});

onMounted(() => {
    // Sync with persisted settings from CartStore
    document.documentElement.dir = cart.language === 'ar' ? 'rtl' : 'ltr';
});

const submit = () => {
    console.log('submitting order...');
    if (form.processing) return;
    
    form.post(route('order.store'), {
        onSuccess: () => {
            cart.clearCart();
            // Inertia handles the redirect automatically
        },
        onError: (errors) => {
            console.error('Order creation failed:', errors);
        }
    });
};
</script>

<template>
    <Head :title="cart.t('checkout') + ' - Thinker'" />
    
    <div class="min-h-screen bg-surface-50 font-cairo selection:bg-brand-500 selection:text-white pb-20">
        <Navbar @open-cart="toggleCart" />

        <main class="max-w-7xl mx-auto px-6 sm:px-10 lg:px-16 pt-32 sm:pt-48">
            <div class="flex flex-col lg:flex-row gap-16 items-start">
                
                <!-- Billing Details Section -->
                <div class="flex-1 w-full space-y-12 animate-in fade-in slide-in-from-right duration-700">
                    <div class="space-y-2">
                        <h1 class="text-4xl font-black text-ink-main tracking-tight uppercase" :class="cart.language === 'ar' ? 'text-right' : 'text-left font-black'">
                            بيانات الشحن <span class="text-xl opacity-30 ml-2">/ Shipping</span>
                        </h1>
                        <p class="text-ink-muted text-xs font-bold uppercase tracking-widest opacity-60" :class="cart.language === 'ar' ? 'text-right' : 'text-left'">Complete your architecture order</p>
                    </div>

                    <form id="checkout-form" @submit.prevent="submit" class="space-y-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Full Name -->
                            <div class="space-y-2 group">
                                <label class="flex flex-col" :class="cart.language === 'ar' ? 'text-right' : 'text-left font-black'">
                                    <span class="text-[17px] font-black text-ink-main leading-none">الاسم الكامل</span>
                                    <span class="text-[12px] font-roboto text-ink-muted opacity-50 font-medium mt-1 leading-none uppercase tracking-wider">Full Name</span>
                                </label>
                                <input 
                                    v-model="form.customer_name" 
                                    type="text" 
                                    class="w-full bg-white border border-border/40 rounded-2xl p-5 text-sm font-bold focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all shadow-sm group-hover:shadow-md"
                                    required
                                />
                                <div v-if="form.errors.customer_name" class="text-brand-500 text-[10px] font-black uppercase tracking-widest mt-2">{{ form.errors.customer_name }}</div>
                            </div>

                            <!-- Phone Number -->
                            <div class="space-y-2 group">
                                <label class="flex flex-col" :class="cart.language === 'ar' ? 'text-right' : 'text-left font-black'">
                                    <span class="text-[17px] font-black text-ink-main leading-none">رقم الهاتف</span>
                                    <span class="text-[12px] font-roboto text-ink-muted opacity-50 font-medium mt-1 leading-none uppercase tracking-wider">Phone Number</span>
                                </label>
                                <input 
                                    v-model="form.customer_phone" 
                                    type="tel" 
                                    dir="ltr"
                                    class="w-full bg-white border border-border/40 rounded-2xl p-5 text-sm font-black font-roboto focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all shadow-sm group-hover:shadow-md"
                                    :class="cart.language === 'ar' ? 'text-right' : 'text-left'"
                                    required
                                />
                                <div v-if="form.errors.customer_phone" class="text-brand-500 text-[10px] font-black uppercase tracking-widest mt-2">{{ form.errors.customer_phone }}</div>
                            </div>
                        </div>

                        <!-- Email Address -->
                        <div class="space-y-2 group">
                            <label class="flex flex-col" :class="cart.language === 'ar' ? 'text-right' : 'text-left font-black'">
                                <span class="text-[17px] font-black text-ink-main leading-none">البريد الإلكتروني</span>
                                <span class="text-[12px] font-roboto text-ink-muted opacity-50 font-medium mt-1 leading-none uppercase tracking-wider">Email Address</span>
                            </label>
                            <input 
                                v-model="form.customer_email" 
                                type="email" 
                                dir="ltr"
                                class="w-full bg-white border border-border/40 rounded-2xl p-5 text-sm font-black font-roboto focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all shadow-sm group-hover:shadow-md"
                                :class="cart.language === 'ar' ? 'text-right' : 'text-left'"
                                required
                            />
                            <div v-if="form.errors.customer_email" class="text-brand-500 text-[10px] font-black uppercase tracking-widest mt-2">{{ form.errors.customer_email }}</div>
                        </div>

                        <!-- Street Address -->
                        <div class="space-y-2 group">
                            <label class="flex flex-col" :class="cart.language === 'ar' ? 'text-right' : 'text-left font-black'">
                                <span class="text-[17px] font-black text-ink-main leading-none">العنوان بالتفصيل</span>
                                <span class="text-[12px] font-roboto text-ink-muted opacity-50 font-medium mt-1 leading-none uppercase tracking-wider">Street Address</span>
                            </label>
                            <textarea 
                                v-model="form.shipping_address" 
                                rows="3"
                                class="w-full bg-white border border-border/40 rounded-3xl p-6 text-sm font-bold focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all shadow-sm group-hover:shadow-md"
                                required
                            ></textarea>
                            <div v-if="form.errors.shipping_address" class="text-brand-500 text-[10px] font-black uppercase tracking-widest mt-2">{{ form.errors.shipping_address }}</div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Province/Gov -->
                            <SearchableSelect 
                                v-model="form.province"
                                :options="EGYPT_REGIONS"
                                label-ar="المحافظة"
                                label-en="Governorate"
                                :placeholder="cart.language === 'ar' ? 'اختر المحافظة' : 'Select Governorate'"
                                :language="cart.language"
                                :error="form.errors.province"
                            />

                            <!-- City / Area -->
                            <SearchableSelect 
                                v-model="form.city"
                                :options="availableAreas"
                                label-ar="المنطقة"
                                label-en="Area / City"
                                :placeholder="!form.province ? (cart.language === 'ar' ? 'اختر المحافظة أولاً' : 'Select Governorate first') : (cart.language === 'ar' ? 'اختر المنطقة' : 'Select Area')"
                                :disabled="!form.province"
                                :language="cart.language"
                                :error="form.errors.city"
                            />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Country -->
                            <div class="space-y-2 group">
                                <label class="flex flex-col" :class="cart.language === 'ar' ? 'text-right' : 'text-left font-black'">
                                    <span class="text-[17px] font-black text-ink-main leading-none">الدولة</span>
                                    <span class="text-[12px] font-roboto text-ink-muted opacity-50 font-medium mt-1 leading-none uppercase tracking-wider">Country</span>
                                </label>
                                <input 
                                    v-model="form.country" 
                                    type="text" 
                                    disabled
                                    class="w-full bg-surface-100 border border-border/20 rounded-2xl p-5 text-sm font-black text-ink-muted opacity-50 cursor-not-allowed h-[64px]"
                                />
                            </div>
                        </div>

                        <!-- Order Notes -->
                        <div class="space-y-2 group">
                            <label class="flex flex-col" :class="cart.language === 'ar' ? 'text-right' : 'text-left font-black'">
                                <span class="text-[17px] font-black text-ink-main leading-none">ملاحظات الطلب</span>
                                <span class="text-[12px] font-roboto text-ink-muted opacity-50 font-medium mt-1 leading-none uppercase tracking-wider">Order Notes</span>
                            </label>
                            <textarea 
                                v-model="form.notes" 
                                rows="2"
                                class="w-full bg-white border border-border/40 rounded-3xl p-6 text-sm font-bold focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all shadow-sm group-hover:shadow-md"
                                placeholder="Special instructions for delivery..."
                            ></textarea>
                        </div>

                        <!-- Payment Methods Section -->
                        <div class="space-y-8 pt-10 border-t border-border/20">
                            <div class="space-y-2">
                                <h2 class="text-2xl font-black text-ink-main tracking-tight uppercase" :class="cart.language === 'ar' ? 'text-right' : 'text-left font-black'">
                                    طرق الدفع <span class="text-base opacity-30 ml-2">/ Payment</span>
                                </h2>
                                <p class="text-ink-muted text-[10px] font-bold uppercase tracking-widest opacity-60" :class="cart.language === 'ar' ? 'text-right' : 'text-left'">Secured transaction layer</p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <!-- Cash on Delivery -->
                                <label class="relative flex flex-col p-6 rounded-[2rem] border-2 cursor-pointer transition-all hover:scale-[1.02] active:scale-95 shadow-sm"
                                    :class="form.payment_method === 'cod' ? 'border-brand-500 bg-brand-50/10 shadow-xl shadow-brand-500/5' : 'border-border/30 bg-white hover:border-brand-500/30'"
                                >
                                    <input type="radio" value="cod" v-model="form.payment_method" class="sr-only" />
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="w-10 h-10 rounded-2xl bg-surface-50 flex items-center justify-center text-ink-main">
                                            🚚
                                        </div>
                                        <div class="w-5 h-5 rounded-full border-4 transition-all" :class="form.payment_method === 'cod' ? 'border-brand-500 bg-white shadow-brand-500/50' : 'border-surface-200'"></div>
                                    </div>
                                    <span class="text-lg font-black text-ink-main leading-none">الدفع عند الاستلام</span>
                                    <span class="text-[10px] font-roboto text-ink-muted opacity-60 font-bold mt-2 uppercase tracking-widest">Cash on delivery</span>
                                </label>

                                <!-- InstaPay -->
                                <label class="relative flex flex-col p-6 rounded-[2rem] border-2 cursor-pointer transition-all hover:scale-[1.02] active:scale-95 shadow-sm"
                                    :class="form.payment_method === 'instapay' ? 'border-brand-500 bg-brand-50/10 shadow-xl shadow-brand-500/5' : 'border-border/30 bg-white hover:border-brand-500/30'"
                                >
                                    <input type="radio" value="instapay" v-model="form.payment_method" class="sr-only" />
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="w-10 h-10 rounded-2xl bg-surface-50 flex items-center justify-center text-ink-main font-black italic text-[9px] px-1 text-center">
                                            INSTA
                                        </div>
                                        <div class="w-5 h-5 rounded-full border-4 transition-all" :class="form.payment_method === 'instapay' ? 'border-brand-500 bg-white shadow-brand-500/50' : 'border-surface-200'"></div>
                                    </div>
                                    <span class="text-lg font-black text-ink-main leading-none">تحويل إنستا باي</span>
                                    <span class="text-[10px] font-roboto text-ink-muted opacity-60 font-bold mt-2 uppercase tracking-widest">Instapay transfer</span>
                                </label>

                                <!-- Credit Card -->
                                <label class="relative flex flex-col p-6 rounded-[2rem] border-2 cursor-pointer transition-all hover:scale-[1.02] active:scale-95 shadow-sm"
                                    :class="form.payment_method === 'card' ? 'border-brand-500 bg-brand-50/10 shadow-xl shadow-brand-500/5' : 'border-border/30 bg-white hover:border-brand-500/30'"
                                >
                                    <input type="radio" value="card" v-model="form.payment_method" class="sr-only" />
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="w-10 h-10 rounded-2xl bg-surface-50 flex items-center justify-center text-ink-main font-black italic text-[9px] px-1 text-center">
                                            VISA
                                        </div>
                                        <div class="w-5 h-5 rounded-full border-4 transition-all" :class="form.payment_method === 'card' ? 'border-brand-500 bg-white shadow-brand-500/50' : 'border-surface-200'"></div>
                                    </div>
                                    <span class="text-lg font-black text-ink-main leading-none">الدفع بالبطاقة</span>
                                    <span class="text-[10px] font-roboto text-ink-muted opacity-60 font-bold mt-2 uppercase tracking-widest">Credit card</span>
                                </label>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Order Summary Sidebar -->
                <div class="w-full lg:w-[450px] space-y-8 animate-in fade-in slide-in-from-left duration-700">
                    <div class="bg-white rounded-[3rem] p-10 border border-border/30 shadow-2xl relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-brand-500/5 rounded-bl-[8rem] -mr-10 -mt-10"></div>
                        
                        <div class="relative space-y-10">
                            <div class="space-y-4">
                                <h2 class="text-3xl font-black text-ink-main tracking-tight uppercase leading-none">
                                    ملخص الطلب <span class="text-lg opacity-30 ml-2">/ SUMMARY</span>
                                </h2>
                                <div class="h-1.5 w-12 bg-brand-500 rounded-full"></div>
                            </div>

                            <!-- Cart Items List -->
                            <div class="space-y-6 max-h-[300px] overflow-y-auto pr-4 custom-scrollbar">
                                <div v-for="item in cart.items" :key="item.id" class="flex gap-4 items-center group">
                                    <div class="w-20 h-20 bg-surface-50 rounded-2xl overflow-hidden border border-border/20 shrink-0">
                                        <img :src="item.image_url" class="w-full h-full object-cover transition-transform group-hover:scale-110" />
                                    </div>
                                    <div class="flex-1 space-y-1 text-right">
                                        <h4 class="text-[11px] font-black text-ink-main uppercase tracking-widest line-clamp-1">{{ item.name }}</h4>
                                        <p class="text-[10px] font-bold text-ink-muted">ARK Unit x{{ item.quantity }}</p>
                                        <p class="text-sm font-black text-brand-500 font-roboto">{{ (item.price * item.quantity).toFixed(2) }} EGP</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Calculations -->
                            <div class="space-y-4 pt-8 border-t border-border/10">
                                <div class="flex justify-between items-center">
                                    <span class="text-[11px] font-black text-ink-muted uppercase tracking-[0.2em]">{{ cart.t('total') }}</span>
                                    <span class="text-xl font-black text-ink-main font-roboto">{{ cart.total.toFixed(2) }} EGP</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-[11px] font-black text-ink-muted uppercase tracking-[0.2em]">{{ cart.t('shipping') }}</span>
                                    <span class="text-[11px] font-black text-brand-500 uppercase tracking-widest">{{ cart.language === 'ar' ? 'مجاني' : 'FREE' }}</span>
                                </div>
                                
                                <div class="pt-6 flex justify-between items-end">
                                    <div class="space-y-1 text-right">
                                        <span class="text-[11px] font-black text-ink-muted uppercase tracking-[0.4em]">Final Sync</span>
                                        <p class="text-5xl font-black text-brand-500 tracking-tighter font-roboto">
                                            {{ cart.total.toFixed(2) }}
                                        </p>
                                    </div>
                                    <span class="text-sm font-black text-ink-main mb-2">EGP</span>
                                </div>
                            </div>

                            <!-- Final Action -->
                            <button 
                                type="submit"
                                form="checkout-form"
                                :disabled="form.processing || cart.items.length === 0"
                                class="w-full bg-brand-500 hover:bg-brand-600 text-white h-20 rounded-[2rem] flex items-center justify-center gap-6 shadow-2xl shadow-brand-500/30 transition-all active:scale-95 disabled:opacity-50 disabled:grayscale group relative overflow-hidden"
                            >
                                <template v-if="!form.processing">
                                    <span class="text-xl font-black uppercase tracking-tight relative z-10">إتمــــام الطلب <span class="text-sm opacity-50 ml-2">/ PLACE ORDER</span></span>
                                    <svg class="w-6 h-6 relative z-10 transition-transform group-hover:translate-x-[-10px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                    </svg>
                                </template>
                                <template v-else>
                                    <svg class="animate-spin h-8 w-8 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span class="text-lg font-black uppercase tracking-widest animate-pulse">Processing...</span>
                                </template>
                                <div class="absolute inset-0 bg-gradient-to-l from-white/10 to-transparent translate-x-[-100%] group-hover:translate-x-0 transition-transform duration-700"></div>
                            </button>

                            <p class="text-center text-[9px] font-bold text-ink-muted uppercase tracking-widest opacity-40">By completing this order you agree to our architecture protocols and service terms.</p>
                        </div>
                    </div>

                    <!-- Trust Badges -->
                    <div class="flex items-center justify-center gap-8 opacity-30 grayscale transition-all hover:opacity-100 hover:grayscale-0 py-4">
                        <span class="font-black text-[10px] tracking-tighter">SECURE SYNC</span>
                        <span class="font-black text-[10px] tracking-tighter">SSL ARCHITECTURE</span>
                        <span class="font-black text-[10px] tracking-tighter">ARK PROTOCOL</span>
                    </div>
                </div>

            </div>
        </main>

        <Footer />
        <CartModal :is-open="isCartOpen" @close="toggleCart" />
    </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;700;900&display=swap');

.font-cairo {
    font-family: 'Cairo', sans-serif;
}

.custom-scrollbar::-webkit-scrollbar {
    width: 2px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #e5e7eb;
    border-radius: 10px;
}
</style>
