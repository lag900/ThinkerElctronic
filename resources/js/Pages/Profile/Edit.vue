<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head, Link } from '@inertiajs/vue3';
import { cart } from '@/Stores/CartStore';

const props = defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    orders: {
        type: Array,
        default: () => []
    }
});

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString(cart.language === 'ar' ? 'ar-EG' : 'en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

const formatCurrency = (val) => {
    return new Intl.NumberFormat(cart.language === 'ar' ? 'ar-EG' : 'en-US', { 
        style: 'currency', 
        currency: 'EGP',
        maximumFractionDigits: 0
    }).format(val);
};
</script>

<template>
    <Head :title="cart.t('profile')" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                     <h2 class="text-3xl font-black tracking-tighter text-[#222]">{{ cart.language === 'ar' ? 'حسابك الشخصي' : 'Account Matrix' }}</h2>
                     <p class="text-[10px] font-black uppercase tracking-[0.4em] text-slate-300 mt-2">{{ cart.language === 'ar' ? 'إدارة البيانات والطلبات' : 'Data & Governance' }}</p>
                </div>
                <div class="text-right">
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">{{ cart.language === 'ar' ? 'إجمالي الإنفاق' : 'Total Transmitted' }}</p>
                    <p class="text-xl font-black text-brand-500">{{ formatCurrency(orders.reduce((sum, o) => sum + parseFloat(o.total), 0)) }}</p>
                </div>
            </div>
        </template>

        <div class="space-y-12 pb-20">
            <!-- Order Protocols (History) -->
            <div v-if="orders.length > 0" class="bg-white p-10 rounded-[50px] border border-slate-50 shadow-[0_30px_60px_rgba(0,0,0,0.02)]">
                <section>
                    <header class="mb-12 flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-black text-[#222] tracking-tight uppercase">{{ cart.language === 'ar' ? 'سجل الطلبات' : 'Order Protocols' }}</h2>
                            <p class="mt-1 text-[10px] font-black text-slate-300 uppercase tracking-widest">History of your architectural transmissions.</p>
                        </div>
                        <div class="w-12 h-12 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-300 font-black">
                            {{ formatNumber(orders.length) }}
                        </div>
                    </header>

                    <div class="space-y-4">
                        <div v-for="order in orders" :key="order.id" class="flex flex-col xl:flex-row items-center justify-between p-8 bg-white rounded-[32px] border border-slate-50 hover:border-brand-500/20 hover:shadow-xl hover:shadow-brand-500/5 transition-all duration-500 group">
                            <div class="flex items-center gap-8 w-full xl:w-auto">
                                <div class="w-16 h-16 rounded-[24px] bg-slate-50 flex items-center justify-center text-brand-500 shadow-sm border border-slate-100 group-hover:bg-brand-500 group-hover:text-white transition-all duration-500">
                                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-black text-[#222] group-hover:text-brand-500 transition-colors">#{{ order.order_number }}</h4>
                                    <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest mt-1">{{ formatDate(order.created_at) }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-12 mt-6 xl:mt-0 w-full xl:w-auto justify-between xl:justify-end">
                                <div :class="cart.language === 'ar' ? 'text-left' : 'text-right'">
                                    <p class="text-lg font-black text-brand-500">{{ formatCurrency(order.total) }}</p>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-slate-50 rounded-full text-[9px] font-black uppercase tracking-widest text-slate-400 border border-slate-100">
                                        <span class="w-1 h-1 rounded-full" :class="order.status === 'pending' ? 'bg-amber-400' : 'bg-emerald-500'"></span>
                                        {{ order.status }}
                                    </span>
                                </div>
                                <a :href="route('order.invoice', order.order_number)" target="_blank" class="w-14 h-14 bg-[#222] text-white hover:bg-brand-500 rounded-2xl flex items-center justify-center transition-all shadow-xl shadow-black/10 active:scale-90">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Management Nodes -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                <div class="bg-white p-12 rounded-[50px] border border-slate-50 shadow-[0_30px_60px_rgba(0,0,0,0.02)]">
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                    />
                </div>

                <div class="bg-white p-12 rounded-[50px] border border-slate-50 shadow-[0_30px_60px_rgba(0,0,0,0.02)]">
                    <UpdatePasswordForm />
                </div>
            </div>

            <div class="bg-white p-12 rounded-[50px] border border-red-50 shadow-[0_30px_60px_rgba(0,0,0,0.01)]">
                <DeleteUserForm />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
