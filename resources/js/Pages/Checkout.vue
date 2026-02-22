<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import { cart } from '@/Stores/CartStore';
import Navbar from '@/Components/Thinker/Navbar.vue';
import Footer from '@/Components/Thinker/Footer.vue';
import { onMounted, ref, computed, watch } from 'vue';
import axios from 'axios';
import SearchableSelect from '@/Components/Thinker/SearchableSelect.vue';
import CartModal from '@/Components/Thinker/CartModal.vue';

const props = defineProps({
    savedCustomer: Object,
    governorates: Array,
    deliverySettings: Object
});

const isCartOpen = ref(false);
const toggleCart = () => isCartOpen.value = !isCartOpen.value;

const availableAreas = ref([]);
const deliveryInfo = ref(null);
const isLoadingAreas = ref(false);

const form = useForm({
    customer_name: props.savedCustomer?.name || '',
    customer_email: props.savedCustomer?.email || '',
    customer_phone: props.savedCustomer?.phone || '',
    shipping_address: props.savedCustomer?.address || '',
    governorate_id: '',
    area_id: '',
    use_express: false,
    payment_method: 'cod',
    notes: '',
    items: cart.items,
    // Legacy fields for backward compat or if needed by backend elsewhere
    city: '',
    province: ''
});

// Watch governorate to load areas
watch(() => form.governorate_id, async (newGovId) => {
    form.area_id = '';
    availableAreas.value = [];
    deliveryInfo.value = null;
    
    if (newGovId) {
        isLoadingAreas.value = true;
        try {
            const response = await axios.get(`/admin/api/governorates/${newGovId}/areas`);
            availableAreas.value = response.data;
            
            const gov = props.governorates.find(g => g.id === newGovId);
            form.province = cart.language === 'ar' ? gov.name_ar : gov.name_en;
            form.governorate_id = gov.id;
        } catch (e) {
            console.error('Failed to load areas', e);
        } finally {
            isLoadingAreas.value = false;
        }
    }
});

// Watch area to load price
watch(() => form.area_id, async (newAreaId) => {
    deliveryInfo.value = null;
    if (newAreaId) {
        try {
            const response = await axios.get(`/api/delivery-price/${newAreaId}`);
            deliveryInfo.value = response.data;
            
            const area = availableAreas.value.find(a => a.id === newAreaId);
            form.city = cart.language === 'ar' ? area.name_ar : area.name_en;
        } catch (e) {
            console.error('Failed to load delivery price', e);
        }
    }
});

const currentDeliveryPrice = computed(() => {
    if (!deliveryInfo.value) return 0;
    
    let price = 0;
    if (form.use_express && deliveryInfo.value.express_price) {
        price = parseFloat(deliveryInfo.value.express_price);
    } else {
        price = parseFloat(deliveryInfo.value.delivery_price);
    }

    // DEBUG VALIDATION (Rule 11) - Check against locally computed sum
    if (price > 0 && (cart.total + price) === cart.total) {
        console.error('ERP PROTOCOL ERROR: Area has price but calculation failed (Price not added to subtotal)');
    }

    return price;
});

const finalTotal = computed(() => {
    return cart.total + currentDeliveryPrice.value;
});

onMounted(() => {
    document.documentElement.dir = cart.language === 'ar' ? 'rtl' : 'ltr';
});

const submit = () => {
    if (form.processing) return;
    
    form.post(route('order.store'), {
        onSuccess: () => {
            cart.clearCart();
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
                                <input v-model="form.customer_name" type="text" class="w-full bg-white border border-border/40 rounded-2xl p-5 text-sm font-bold focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all shadow-sm group-hover:shadow-md" required />
                                <div v-if="form.errors.customer_name" class="text-brand-500 text-[10px] font-black uppercase tracking-widest mt-2">{{ form.errors.customer_name }}</div>
                            </div>

                            <!-- Phone Number -->
                            <div class="space-y-2 group">
                                <label class="flex flex-col" :class="cart.language === 'ar' ? 'text-right' : 'text-left font-black'">
                                    <span class="text-[17px] font-black text-ink-main leading-none">رقم الهاتف</span>
                                    <span class="text-[12px] font-roboto text-ink-muted opacity-50 font-medium mt-1 leading-none uppercase tracking-wider">Phone Number</span>
                                </label>
                                <input v-model="form.customer_phone" type="tel" dir="ltr" class="w-full bg-white border border-border/40 rounded-2xl p-5 text-sm font-black font-roboto focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all shadow-sm group-hover:shadow-md" :class="cart.language === 'ar' ? 'text-right' : 'text-left'" required />
                                <div v-if="form.errors.customer_phone" class="text-brand-500 text-[10px] font-black uppercase tracking-widest mt-2">{{ form.errors.customer_phone }}</div>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="space-y-2 group">
                            <label class="flex flex-col" :class="cart.language === 'ar' ? 'text-right' : 'text-left font-black'">
                                <span class="text-[17px] font-black text-ink-main leading-none">البريد الإلكتروني</span>
                                <span class="text-[12px] font-roboto text-ink-muted opacity-50 font-medium mt-1 leading-none uppercase tracking-wider">Email Address</span>
                            </label>
                            <input v-model="form.customer_email" type="email" dir="ltr" class="w-full bg-white border border-border/40 rounded-2xl p-5 text-sm font-black font-roboto focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all shadow-sm group-hover:shadow-md" :class="cart.language === 'ar' ? 'text-right' : 'text-left'" required />
                            <div v-if="form.errors.customer_email" class="text-brand-500 text-[10px] font-black uppercase tracking-widest mt-2">{{ form.errors.customer_email }}</div>
                        </div>

                        <!-- Address -->
                        <div class="space-y-2 group">
                            <label class="flex flex-col" :class="cart.language === 'ar' ? 'text-right' : 'text-left font-black'">
                                <span class="text-[17px] font-black text-ink-main leading-none">العنوان بالتفصيل</span>
                                <span class="text-[12px] font-roboto text-ink-muted opacity-50 font-medium mt-1 leading-none uppercase tracking-wider">Street Address</span>
                            </label>
                            <textarea v-model="form.shipping_address" rows="3" class="w-full bg-white border border-border/40 rounded-3xl p-6 text-sm font-bold focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all shadow-sm group-hover:shadow-md" required></textarea>
                            <div v-if="form.errors.shipping_address" class="text-brand-500 text-[10px] font-black uppercase tracking-widest mt-2">{{ form.errors.shipping_address }}</div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Governorate Selector -->
                            <SearchableSelect 
                                :model-value="props.governorates.find(g => g.id === form.governorate_id)?.[cart.language === 'ar' ? 'name_ar' : 'name_en']"
                                :options="governorates.map(g => ({ name_ar: g.name_ar, name_en: g.name_en, id: g.id }))"
                                label-ar="المحافظة"
                                label-en="Governorate"
                                :placeholder="cart.language === 'ar' ? 'اختر المحافظة' : 'Select Governorate'"
                                :language="cart.language"
                                :error="form.errors.governorate_id"
                                @update:modelValue="(val) => {
                                    const gov = governorates.find(g => (cart.language === 'ar' ? g.name_ar : g.name_en) === val);
                                    form.governorate_id = gov?.id || '';
                                }"
                            />

                            <!-- Area Selector -->
                            <SearchableSelect 
                                :model-value="availableAreas.find(a => a.id === form.area_id)?.[cart.language === 'ar' ? 'name_ar' : 'name_en']"
                                :options="availableAreas.map(a => ({ name_ar: a.name_ar, name_en: a.name_en, id: a.id }))"
                                label-ar="المنطقة"
                                label-en="Area / City"
                                :placeholder="!form.governorate_id ? (cart.language === 'ar' ? 'اختر المحافظة أولاً' : 'Select Governorate first') : (cart.language === 'ar' ? 'اختر المنطقة' : 'Select Area')"
                                :disabled="!form.governorate_id || isLoadingAreas"
                                :language="cart.language"
                                :error="form.errors.area_id"
                                @update:modelValue="(val) => {
                                    const area = availableAreas.find(a => (cart.language === 'ar' ? a.name_ar : a.name_en) === val);
                                    form.area_id = area?.id || '';
                                }"
                            />
                        </div>

                        <!-- Express Delivery Toggle -->
                        <div v-if="deliveryInfo?.express_price && props.deliverySettings?.express_enabled" 
                             class="rounded-[2rem] p-6 flex items-center justify-between group cursor-pointer transition-all duration-500 overflow-hidden relative" 
                             :class="form.use_express ? 'bg-brand-500 border-2 border-brand-500 shadow-2xl shadow-brand-500/20' : 'bg-slate-50 border-2 border-slate-100 hover:bg-white hover:border-brand-500/30'"
                             @click="form.use_express = !form.use_express">
                            
                            <!-- Background Accent for Active State -->
                            <div v-if="form.use_express" class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16 blur-2xl animate-pulse"></div>

                            <div class="flex items-center gap-5 relative z-10">
                                <div class="w-14 h-14 rounded-[1.5rem] flex items-center justify-center text-2xl shadow-lg transition-all duration-500 group-hover:rotate-12"
                                     :class="form.use_express ? 'bg-white text-brand-500' : 'bg-white text-slate-400'">
                                    🚀
                                </div>
                                <div :class="cart.language === 'ar' ? 'text-right' : 'text-left'">
                                    <div class="flex items-center gap-2">
                                        <p class="text-sm font-black uppercase tracking-tight" :class="form.use_express ? 'text-white' : 'text-[#111]'">
                                            {{ cart.language === 'ar' ? 'توصيل سريع' : 'Express Delivery' }}
                                        </p>
                                        <span v-if="deliveryInfo?.express_price" class="px-2 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-widest" :class="form.use_express ? 'bg-white text-brand-500' : 'bg-brand-500 text-white'">
                                            +{{ (deliveryInfo.express_price - deliveryInfo.delivery_price).toFixed(2) }} EGP
                                        </span>
                                    </div>
                                    <p class="text-[10px] font-bold uppercase tracking-widest mt-1" :class="form.use_express ? 'text-white/70' : 'text-slate-400'">
                                        {{ cart.language === 'ar' ? 'استلم طلبك خلال ' + deliveryInfo.delivery_days : 'Receive in ' + deliveryInfo.delivery_days }}
                                    </p>
                                </div>
                            </div>

                            <!-- Modern Toggle Switch -->
                            <div class="w-14 h-8 rounded-full border-2 p-1 relative transition-all duration-500" 
                                 :class="form.use_express ? 'bg-white border-white' : 'bg-slate-200 border-slate-200'">
                                <div class="absolute top-1 bottom-1 w-5 bg-white rounded-full transition-all duration-500 shadow-md" 
                                     :class="form.use_express ? 'right-1 left-7 bg-brand-500' : 'left-1 right-7 bg-white'"></div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="space-y-2 group">
                            <label class="flex flex-col" :class="cart.language === 'ar' ? 'text-right' : 'text-left font-black'">
                                <span class="text-[17px] font-black text-ink-main leading-none">ملاحظات الطلب</span>
                                <span class="text-[12px] font-roboto text-ink-muted opacity-50 font-medium mt-1 leading-none uppercase tracking-wider">Order Notes</span>
                            </label>
                            <textarea v-model="form.notes" rows="2" class="w-full bg-white border border-border/40 rounded-3xl p-6 text-sm font-bold focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all shadow-sm group-hover:shadow-md" placeholder="Special instructions for delivery..."></textarea>
                        </div>

                        <!-- Payment Methods -->
                        <div class="space-y-8 pt-10 border-t border-border/20">
                            <h2 class="text-2xl font-black text-ink-main tracking-tight uppercase" :class="cart.language === 'ar' ? 'text-right' : 'text-left font-black'">
                                طرق الدفع <span class="text-base opacity-30 ml-2">/ Payment</span>
                            </h2>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <label v-for="method in ['cod', 'instapay', 'card']" :key="method" class="relative flex flex-col p-6 rounded-[2rem] border-2 cursor-pointer transition-all hover:scale-[1.02] shadow-sm" :class="form.payment_method === method ? 'border-brand-500 bg-brand-50/10 shadow-xl shadow-brand-500/5' : 'border-border/30 bg-white'">
                                    <input type="radio" :value="method" v-model="form.payment_method" class="sr-only" />
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="w-10 h-10 rounded-2xl bg-surface-50 flex items-center justify-center text-lg">
                                            {{ method === 'cod' ? '🚚' : (method === 'instapay' ? '📱' : '💳') }}
                                        </div>
                                        <div class="w-5 h-5 rounded-full border-4" :class="form.payment_method === method ? 'border-brand-500 bg-white' : 'border-surface-200'"></div>
                                    </div>
                                    <span class="text-lg font-black text-ink-main leading-none">{{ method === 'cod' ? 'الدفع عند الاستلام' : (method === 'instapay' ? 'تحويل إنستا باي' : 'الدفع بالبطاقة') }}</span>
                                    <span class="text-[10px] font-bold text-ink-muted opacity-60 mt-2 uppercase tracking-widest">{{ method }} system</span>
                                </label>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Summary Sidebar -->
                <div class="w-full lg:w-[450px] space-y-8 animate-in fade-in slide-in-from-left duration-700">
                    <div class="bg-white rounded-[3rem] p-10 border border-border/30 shadow-2xl relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-brand-500/5 rounded-bl-[8rem] -mr-10 -mt-10"></div>
                        <div class="relative space-y-10">
                            <h2 class="text-3xl font-black text-ink-main uppercase leading-none">ملخص الطلب <span class="text-lg opacity-30 ml-2">/ SUMMARY</span></h2>
                            
                            <div class="space-y-6 max-h-[300px] overflow-y-auto pr-4 custom-scrollbar">
                                <div v-for="item in cart.items" :key="item.id" class="flex gap-4 items-center group">
                                    <div class="w-20 h-20 bg-surface-50 rounded-2xl overflow-hidden border border-border/20 shrink-0">
                                        <img :src="item.image_url" class="w-full h-full object-cover transition-all group-hover:scale-110" />
                                    </div>
                                    <div class="flex-1 space-y-1 text-right">
                                        <h4 class="text-[11px] font-black text-ink-main uppercase tracking-widest line-clamp-1">{{ item.name }}</h4>
                                        <p class="text-[10px] font-bold text-ink-muted">Unit x{{ item.quantity }}</p>
                                        <p class="text-sm font-black text-brand-500 font-roboto">{{ (item.price * item.quantity).toFixed(2) }} EGP</p>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-4 pt-8 border-t border-border/10">
                                <div class="flex justify-between items-center text-[11px] font-black uppercase tracking-[0.2em] text-ink-muted">
                                    <span>Subtotal</span>
                                    <span class="font-roboto text-ink-main text-base">{{ cart.total.toFixed(2) }} EGP</span>
                                </div>
                                <div class="flex justify-between items-center text-[11px] font-black uppercase tracking-[0.2em] text-ink-muted">
                                    <span>Shipping</span>
                                    <span v-if="currentDeliveryPrice > 0" class="font-roboto text-ink-main text-base">{{ currentDeliveryPrice.toFixed(2) }} EGP</span>
                                    <span v-else class="text-brand-500">{{ cart.language === 'ar' ? 'مجاني' : 'FREE' }}</span>
                                </div>
                                
                                <div class="pt-6 flex justify-between items-end">
                                    <div class="space-y-1 text-right">
                                        <span class="text-[11px] font-black text-ink-muted uppercase tracking-[0.4em]">Final Sync</span>
                                        <p class="text-5xl font-black text-brand-500 tracking-tighter font-roboto">{{ finalTotal.toFixed(2) }}</p>
                                    </div>
                                    <span class="text-sm font-black text-ink-main mb-2">EGP</span>
                                </div>
                            </div>

                            <button type="submit" form="checkout-form" :disabled="form.processing || cart.items.length === 0" class="w-full bg-brand-500 hover:bg-brand-600 text-white h-20 rounded-[2rem] flex items-center justify-center gap-6 shadow-2xl transition-all active:scale-95 group overflow-hidden">
                                <span class="text-xl font-black uppercase tracking-tight relative z-10">{{ form.processing ? 'Processing...' : (cart.language === 'ar' ? 'إتمــــام الطلب' : 'PLACE ORDER') }}</span>
                                <svg v-if="!form.processing" class="w-6 h-6 z-10 transition-transform group-hover:translate-x-[-10px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                            </button>
                        </div>
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
.font-cairo { font-family: 'Cairo', sans-serif; }
.custom-scrollbar::-webkit-scrollbar { width: 2px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
</style>
