<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import { cart } from '@/Stores/CartStore';

const props = defineProps({
    customers: {
        type: Array,
        default: () => []
    },
    stats: Object
});

const formatCurrency = (val) => {
    return new Intl.NumberFormat(cart.language === 'ar' ? 'ar-EG' : 'en-US', { 
        style: 'currency', 
        currency: 'EGP',
        maximumFractionDigits: 0
    }).format(val);
};

const formatNumber = (val) => {
    return new Intl.NumberFormat(cart.language === 'ar' ? 'ar-EG' : 'en-US').format(val);
};
</script>

<template>
    <Head :title="cart.t('customers')" />

    <AdminLayout>
        <template #header>{{ cart.language === 'ar' ? 'قاعدة بيانات العملاء' : 'Customer Intelligence Matrix' }}</template>

        <div class="space-y-12">
            <!-- Header Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white p-8 rounded-[40px] border border-slate-50 flex items-center justify-between shadow-[0_20px_60px_rgba(0,0,0,0.02)]">
                    <div>
                        <p class="text-[9px] font-black uppercase tracking-[0.3em] text-slate-300">{{ cart.language === 'ar' ? 'إجمالي العملاء' : 'Identity Nodes' }}</p>
                        <h4 class="text-4xl font-black text-[#222] tracking-tighter mt-2">{{ formatNumber(stats?.total_customers || 1240) }}</h4>
                    </div>
                    <div class="w-16 h-16 bg-brand-50 rounded-[28px] flex items-center justify-center text-brand-500 shadow-sm border border-brand-100">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    </div>
                </div>

                <div class="bg-white p-8 rounded-[40px] border border-slate-50 flex items-center justify-between shadow-[0_20px_60px_rgba(0,0,0,0.02)] group hover:border-emerald-100 transition-all duration-500">
                    <div>
                        <p class="text-[9px] font-black uppercase tracking-[0.3em] text-slate-300">{{ cart.language === 'ar' ? 'العملاء الدائمين' : 'Retention Protocol' }}</p>
                        <h4 class="text-4xl font-black text-[#222] tracking-tighter mt-2">84%</h4>
                    </div>
                    <div class="w-16 h-16 bg-emerald-50 rounded-[28px] flex items-center justify-center text-emerald-500 shadow-sm border border-emerald-100 group-hover:bg-emerald-500 group-hover:text-white transition-all">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                    </div>
                </div>

                <div class="bg-white p-8 rounded-[40px] border border-slate-50 flex items-center justify-between shadow-[0_20px_60px_rgba(0,0,0,0.02)]">
                    <div>
                        <p class="text-[9px] font-black uppercase tracking-[0.3em] text-slate-300">{{ cart.language === 'ar' ? 'متوسط السلة' : 'Avg Session Value' }}</p>
                        <h4 class="text-4xl font-black text-[#222] tracking-tighter mt-2">{{ formatCurrency(850) }}</h4>
                    </div>
                    <div class="w-16 h-16 bg-[#222] rounded-[28px] flex items-center justify-center text-white shadow-xl shadow-black/10">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                </div>
            </div>

            <!-- Customer List -->
            <div class="bg-white rounded-[50px] border border-slate-50 shadow-[0_40px_80px_rgba(0,0,0,0.02)] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full" :class="{'text-right': cart.language === 'ar', 'text-left': cart.language !== 'ar'}">
                        <thead>
                            <tr class="bg-slate-50/50 text-[10px] font-black uppercase tracking-[0.3em] text-slate-300">
                                <th class="px-10 py-10">{{ cart.language === 'ar' ? 'الهوية' : 'Identity Node' }}</th>
                                <th class="px-10 py-10">{{ cart.language === 'ar' ? 'التواصل' : 'Comm Vectors' }}</th>
                                <th class="px-10 py-10">{{ cart.language === 'ar' ? 'النشاط الاقتصادي' : 'Economic Activity' }}</th>
                                <th class="px-10 py-10">{{ cart.language === 'ar' ? 'آخر عملية' : 'Last Transmission' }}</th>
                                <th class="px-10 py-10" :class="cart.language === 'ar' ? 'text-left' : 'text-right'">Profile</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <tr v-for="c in customers" :key="c.id" class="hover:bg-slate-50/30 transition-all duration-500 group">
                                <td class="px-10 py-8">
                                    <div class="flex items-center gap-6">
                                        <div class="w-14 h-14 bg-white border border-slate-100 rounded-[20px] flex items-center justify-center text-[#222] font-black text-sm shadow-sm group-hover:bg-brand-500 group-hover:text-white transition-all duration-500">
                                            {{ c.name.charAt(0) }}
                                        </div>
                                        <p class="text-[15px] font-black text-[#222]">{{ c.name }}</p>
                                    </div>
                                </td>
                                <td class="px-10 py-8">
                                    <div class="space-y-1">
                                        <p class="text-[11px] font-black text-[#222]">{{ c.email }}</p>
                                        <p class="text-[10px] font-black text-slate-300 tracking-wider">{{ c.phone }}</p>
                                    </div>
                                </td>
                                <td class="px-10 py-8">
                                    <div class="space-y-1">
                                        <p class="text-xs font-black text-[#222] uppercase tracking-widest">{{ formatNumber(c.total_orders || 0) }} Orders</p>
                                        <p class="text-[10px] font-black text-emerald-500 uppercase tracking-[0.2em]">{{ formatCurrency(c.total_spent || 0) }}</p>
                                    </div>
                                </td>
                                <td class="px-10 py-8">
                                    <span class="text-[10px] font-black text-slate-300 uppercase tracking-[0.2em]">{{ c.last_order_at ? 'Transmitted' : 'Idle State' }}</span>
                                </td>
                                <td class="px-10 py-8" :class="cart.language === 'ar' ? 'text-left' : 'text-right'">
                                    <button class="bg-white hover:bg-[#222] hover:text-white border border-slate-100 px-6 py-3 rounded-2xl text-[9px] font-black uppercase tracking-[0.3em] text-[#222] transition-all active:scale-95 shadow-sm">
                                        Analyze Dossier
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- If No Customers -->
                <div v-if="customers.length === 0" class="p-40 text-center">
                    <div class="w-24 h-24 bg-slate-50 rounded-[40px] flex items-center justify-center mx-auto mb-10 text-slate-200">
                         <svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    </div>
                    <h5 class="text-xl font-black text-[#222] tracking-tighter uppercase">Zero subjects detected</h5>
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-300 mt-2">Active search for new subjects is recommended.</p>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
