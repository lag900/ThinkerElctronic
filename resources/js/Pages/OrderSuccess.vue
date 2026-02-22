<script setup>
import { Head, Link } from '@inertiajs/vue3';
import Navbar from '@/Components/Thinker/Navbar.vue';
import Footer from '@/Components/Thinker/Footer.vue';
import { cart } from '@/Stores/CartStore';

const props = defineProps({
    order: Object
});

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};
</script>

<template>
    <Head title="Order Confirmed" />

    <div class="min-h-screen bg-surface-50 font-roboto">
        <Navbar />

        <main class="max-w-4xl mx-auto px-4 py-20">
            <!-- Success Header -->
            <div class="text-center mb-16 animate-fade-in">
                <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-brand-50 mb-8 scale-110 shadow-2xl shadow-brand-500/10 animate-slide-up">
                    <svg class="w-12 h-12 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h1 class="text-4xl md:text-5xl font-black text-ink-main mb-4 tracking-tight">
                    {{ cart.language === 'ar' ? 'تم استلام طلبك بنجاح' : 'Success! Order Received' }}
                </h1>
                <p class="text-lg text-ink-muted/70 font-medium">
                    {{ cart.language === 'ar' ? 'شكراً لثقتك بـ Thinker Electronics. تم تأكيد طلبك وجاري معالجته.' : 'Thank you for choosing Thinker Electronics. Your order is being processed.' }}
                </p>
            </div>

            <!-- Order Quick Info Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-12">
                <div class="bg-white p-6 rounded-3xl border border-border/10 shadow-sm hover:shadow-md transition-shadow">
                    <span class="text-[10px] uppercase font-black tracking-widest text-ink-muted opacity-40 block mb-2">{{ cart.language === 'ar' ? 'رقم الطلب' : 'ORDER NO' }}</span>
                    <span class="text-sm font-black text-ink-main block">#{{ order.order_number }}</span>
                </div>
                <div class="bg-white p-6 rounded-3xl border border-border/10 shadow-sm hover:shadow-md transition-shadow">
                    <span class="text-[10px] uppercase font-black tracking-widest text-ink-muted opacity-40 block mb-2">{{ cart.language === 'ar' ? 'التاريخ' : 'DATE' }}</span>
                    <span class="text-sm font-black text-ink-main block">{{ formatDate(order.created_at) }}</span>
                </div>
                <div class="bg-white p-6 rounded-3xl border border-border/10 shadow-sm hover:shadow-md transition-shadow">
                    <span class="text-[10px] uppercase font-black tracking-widest text-ink-muted opacity-40 block mb-2">{{ cart.language === 'ar' ? 'وسيلة الدفع' : 'PAYMENT' }}</span>
                    <span class="text-sm font-black text-ink-main block">{{ order.payment_method.toUpperCase() }}</span>
                </div>
                <div class="bg-white p-6 rounded-3xl border border-border/10 shadow-sm hover:shadow-md transition-shadow">
                    <span class="text-[10px] uppercase font-black tracking-widest text-ink-muted opacity-40 block mb-2">{{ cart.language === 'ar' ? 'الإجمالي' : 'TOTAL' }}</span>
                    <span class="text-sm font-black text-brand-500 block">{{ order.total }} EGP</span>
                </div>
            </div>

            <div class="bg-white rounded-[40px] border border-border/10 shadow-2xl overflow-hidden mb-12">
                <!-- Products Table -->
                <div class="p-8 md:p-12 border-b border-border/5">
                    <h2 class="text-2xl font-black text-ink-main mb-8">{{ cart.language === 'ar' ? 'تفاصيل المنتجات' : 'Order Items' }}</h2>
                    <div class="space-y-6">
                        <div v-for="item in order.items" :key="item.id" class="flex items-center gap-6 group">
                            <div class="w-20 h-20 rounded-2xl overflow-hidden bg-surface-50 shrink-0 border border-border/10">
                                <img :src="item.product.image_url" :alt="item.product.name" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-base font-black text-ink-main truncate leading-tight mb-1">{{ item.product.name }}</h3>
                                <p class="text-xs font-bold text-ink-muted opacity-50 uppercase tracking-widest">Qty: {{ item.quantity }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-black text-ink-main">{{ item.price * item.quantity }} EGP</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary & Invoice Action -->
                <div class="bg-surface-50/50 p-8 md:p-12">
                    <div class="flex flex-col md:flex-row gap-12 items-start justify-between">
                        <!-- Invoice Download -->
                        <div class="order-bottom md:order-first w-full md:w-auto">
                            <a :href="route('order.invoice', order.order_number)" target="_blank"
                               class="inline-flex items-center gap-4 bg-ink-main text-white px-10 py-6 rounded-3xl font-black text-sm uppercase tracking-widest hover:bg-brand-500 hover:shadow-2xl hover:shadow-brand-500/20 transition-all active:scale-95 w-full md:w-auto justify-center group"
                            >
                                <svg class="w-5 h-5 group-hover:animate-bounce" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                <span>{{ cart.language === 'ar' ? 'تحميل الفاتورة (PDF)' : 'Download Invoice (PDF)' }}</span>
                            </a>
                        </div>

                        <!-- Totals Summary -->
                        <div class="w-full md:w-80 space-y-4">
                            <div class="flex justify-between items-center text-sm font-bold opacity-60">
                                <span>Subtotal</span>
                                <span>{{ order.subtotal }} EGP</span>
                            </div>
                            <div class="flex justify-between items-center text-sm font-bold opacity-60">
                                <span>Shipping</span>
                                <span v-if="order.delivery_price > 0">{{ order.delivery_price }} EGP</span>
                                <span v-else class="text-brand-500">{{ cart.language === 'ar' ? 'مجاني' : 'FREE' }}</span>
                            </div>
                            <div class="h-px bg-border/10 my-6"></div>
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-black text-ink-main">Total</span>
                                <span class="text-2xl font-black text-brand-500">{{ order.final_total }} EGP</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Home Link -->
            <div class="text-center">
                <Link :href="route('home')" class="text-sm font-black uppercase tracking-widest text-ink-muted hover:text-brand-500 transition-colors">
                    {{ cart.language === 'ar' ? 'العودة للرئيسية' : 'Return to Homepage' }}
                </Link>
            </div>
        </main>

        <Footer />
    </div>
</template>

<style scoped>
.font-roboto { font-family: 'Roboto', 'Inter', sans-serif; }
</style>
