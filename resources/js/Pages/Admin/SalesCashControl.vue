<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { cart } from '@/Stores/CartStore';
import { router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    summary: Object,
    employeeStats: Array,
    paymentMethods: Array,
});

const formatMoney = (amount) => {
    if (amount === null || amount === undefined) return '***';
    return new Intl.NumberFormat(cart.language === 'ar' ? 'ar-EG' : 'en-US', {
        style: 'currency',
        currency: 'EGP',
        maximumFractionDigits: 0
    }).format(amount);
};

const dateRange = ref(props.summary.filter);
const customStart = ref(props.summary.start_date);
const customEnd = ref(props.summary.end_date);

const applyFilter = () => {
    const params = { date_range: dateRange.value };
    if (dateRange.value === 'custom') {
        params.start_date = customStart.value;
        params.end_date = customEnd.value;
    }
    router.get(route('admin.sales-cash-control'), params, { preserveState: true, replace: true });
};

watch(dateRange, (newVal) => {
    if (newVal !== 'custom') applyFilter();
});

const getPaymentLabel = (method) => {
    if (!method) return 'Unknown';
    if (cart.language === 'ar') {
        const mapAr = {
            'cash': 'تحصيل نقدي',
            'credit_card': 'بطاقة ائتمان',
            'stripe': 'سترايب (Stripe)',
            'paypal': 'بايبال (PayPal)',
            'bank_transfer': 'تحويل بنكي',
            'card': 'فيزا / ماستركارد'
        };
        return mapAr[method] || method;
    }
    const map = {
        'cash': 'Cash Payment',
        'credit_card': 'Credit Card',
        'stripe': 'Stripe',
        'paypal': 'PayPal',
        'bank_transfer': 'Bank Transfer',
        'card': 'Credit/Debit Card'
    };
    return map[method] || method;
};
</script>

<template>
    <Head :title="cart.language === 'ar' ? 'المبيعات والرقابة النقدية' : 'Financial Control Horizon'" />
    <AdminLayout>
        <template #header>
            <div class="flex flex-col md:flex-row md:items-center justify-between w-full gap-8">
                <h2 class="font-black text-2xl text-gray-900 tracking-tighter">{{ cart.language === 'ar' ? 'الرقابة المالية والنقدية' : 'Fiscal Control & Cash Auditing' }}</h2>
                <div class="flex flex-wrap items-center gap-4 font-inter">
                    <select v-model="dateRange" class="text-xs font-black text-gray-900 bg-white border border-gray-100 rounded-[12px] px-8 py-4 shadow-sm cursor-pointer outline-none focus:ring-2 focus:ring-gray-900 transition-all appearance-none" :dir="cart.language === 'ar' ? 'rtl' : 'ltr'">
                        <option value="today">{{ cart.language === 'ar' ? 'نشاط اليوم' : 'Daily Horizon' }}</option>
                        <option value="this_week">{{ cart.language === 'ar' ? 'هذا الأسبوع' : 'Weekly Stream' }}</option>
                        <option value="this_month">{{ cart.language === 'ar' ? 'هذا الشهر' : 'Monthly Cycle' }}</option>
                        <option value="custom">{{ cart.language === 'ar' ? 'نطاق مخصص...' : 'Custom Interval...' }}</option>
                    </select>
                    
                    <div v-if="dateRange === 'custom'" class="flex items-center gap-4 bg-white p-2 rounded-[16px] border border-gray-100 shadow-xl animate-in slide-in-from-top-4 duration-500">
                        <input type="date" v-model="customStart" class="text-xs font-bold text-gray-600 bg-gray-50 border-none rounded-[10px] px-4 py-3 focus:ring-1 focus:ring-gray-900 transition-all" />
                        <span class="text-[10px] font-black text-gray-300 uppercase">{{ cart.language === 'ar' ? 'إلى' : 'to' }}</span>
                        <input type="date" v-model="customEnd" class="text-xs font-bold text-gray-600 bg-gray-50 border-none rounded-[10px] px-4 py-3 focus:ring-1 focus:ring-gray-900 transition-all" />
                        <button @click="applyFilter" class="bg-gray-900 text-white px-8 py-3 rounded-[10px] text-xs font-black uppercase tracking-widest hover:bg-gray-800 transition-all shadow-lg active:scale-95">{{ cart.language === 'ar' ? 'تحديث' : 'Sync' }}</button>
                    </div>
                </div>
            </div>
        </template>

        <div class="space-y-10 font-inter" :dir="cart.language === 'ar' ? 'rtl' : 'ltr'">
            <!-- Strategic Performance Matrices -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Aggregate Sales -->
                <div class="bg-white p-10 rounded-[20px] shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-2xl hover:shadow-gray-900/5 transition-all">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-gray-50 rounded-full -mr-12 -mt-12 group-hover:bg-gray-100 transition-colors"></div>
                    <div class="flex flex-col gap-8 relative z-10">
                        <div class="w-14 h-14 bg-gray-900 rounded-[14px] flex items-center justify-center text-white shadow-2xl shadow-gray-900/20">
                            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                        </div>
                        <div>
                            <p class="text-[11px] font-black uppercase tracking-widest text-gray-400 mb-2">{{ cart.language === 'ar' ? 'إجمالي المبيعات' : 'Aggregate Sales' }}</p>
                            <h4 class="text-3xl font-black text-gray-900 tracking-tighter">{{ formatMoney(summary.total_sales_today) }}</h4>
                        </div>
                    </div>
                </div>

                <!-- Digital Assets -->
                <div class="bg-white p-10 rounded-[20px] shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-2xl hover:shadow-blue-600/5 transition-all">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-blue-50 rounded-full -mr-12 -mt-12 group-hover:bg-blue-100 transition-colors"></div>
                    <div class="flex flex-col gap-8 relative z-10">
                        <div class="w-14 h-14 bg-blue-600 rounded-[14px] flex items-center justify-center text-white shadow-2xl shadow-blue-600/20">
                            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" /></svg>
                        </div>
                        <div>
                            <p class="text-[11px] font-black uppercase tracking-widest text-gray-400 mb-2">{{ cart.language === 'ar' ? 'مبيعات المتجر الإلكتروني' : 'Digital Platform Flow' }}</p>
                            <h4 class="text-3xl font-black text-gray-900 tracking-tighter">{{ formatMoney(summary.online_sales) }}</h4>
                        </div>
                    </div>
                </div>

                <!-- Liquid Assets (POS) -->
                <div class="bg-white p-10 rounded-[20px] shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-2xl hover:shadow-emerald-600/5 transition-all">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-emerald-50 rounded-full -mr-12 -mt-12 group-hover:bg-emerald-100 transition-colors"></div>
                    <div class="flex flex-col gap-8 relative z-10">
                        <div class="w-14 h-14 bg-emerald-600 rounded-[14px] flex items-center justify-center text-white shadow-2xl shadow-emerald-600/20">
                             <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                        </div>
                        <div>
                            <p class="text-[11px] font-black uppercase tracking-widest text-gray-400 mb-2">{{ cart.language === 'ar' ? 'مبيعات الكاشير (نقدي)' : 'Liquid Point-of-Sale' }}</p>
                            <h4 class="text-3xl font-black text-gray-900 tracking-tighter">{{ formatMoney(summary.pos_sales) }}</h4>
                        </div>
                    </div>
                </div>

                <!-- Admin Yield (Profit) -->
                <div class="bg-white p-10 rounded-[20px] shadow-sm border border-gray-100 relative overflow-hidden group transition-all"
                     :class="summary.can_view_profit ? 'hover:shadow-2xl hover:shadow-indigo-600/5' : 'opacity-80 bg-gray-50/50'">
                    <div v-if="summary.can_view_profit" class="absolute top-0 right-0 w-24 h-24 bg-indigo-50 rounded-full -mr-12 -mt-12 group-hover:bg-indigo-100 transition-colors"></div>
                    <div class="flex flex-col gap-8 relative z-10">
                        <div class="w-14 h-14 rounded-[14px] flex items-center justify-center shadow-2xl transition-all" :class="summary.can_view_profit ? 'bg-indigo-600 text-white shadow-indigo-600/20' : 'bg-gray-200 text-gray-400'">
                            <svg v-if="summary.can_view_profit" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                            <svg v-else class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                        </div>
                        <div>
                            <p class="text-[11px] font-black uppercase tracking-widest text-gray-400 mb-2">{{ cart.language === 'ar' ? 'صافي أرباح الإدارة' : 'Administrative Net Yield' }}</p>
                            <h4 v-if="summary.can_view_profit" class="text-3xl font-black text-gray-900 tracking-tighter">{{ formatMoney(summary.net_profit) }}</h4>
                            <span v-else class="text-[11px] font-black text-gray-400 uppercase tracking-widest bg-gray-100 px-4 py-1.5 rounded-full inline-block border border-gray-200">{{ cart.language === 'ar' ? 'غير مصرح' : 'Access Restricted' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Fiscal Context -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-10">
                
                <!-- Gateway Nodes -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-[24px] shadow-sm border border-gray-100 p-10 h-full">
                        <h3 class="text-xl font-black text-gray-900 tracking-tighter mb-8">{{ cart.language === 'ar' ? 'بوابات السداد' : 'Gateway Distribution' }}</h3>
                        <div v-if="paymentMethods.length === 0" class="text-center py-20 flex flex-col items-center gap-6">
                            <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center text-3xl border border-gray-100 grayscale opacity-40 shadow-inner">💳</div>
                            <p class="text-gray-300 font-black uppercase tracking-widest text-[10px]">{{ cart.language === 'ar' ? 'لا توجد عمليات مؤخرًا' : 'Zero Transaction Flux' }}</p>
                        </div>
                        <div class="space-y-4">
                            <div v-for="method in paymentMethods" :key="method.payment_method" class="flex flex-col p-6 bg-gray-50/50 rounded-[16px] border border-gray-50 group hover:border-gray-900 transition-all duration-300">
                                <div class="flex items-center gap-4 mb-3">
                                    <div class="w-12 h-12 rounded-[12px] bg-white flex items-center justify-center shadow-sm border border-gray-100 text-xl group-hover:scale-110 transition-transform">
                                        <span v-if="method.payment_method === 'cash'">💵</span>
                                        <span v-else>💳</span>
                                    </div>
                                    <h5 class="text-[11px] font-black text-gray-500 uppercase tracking-widest">{{ getPaymentLabel(method.payment_method) }}</h5>
                                </div>
                                <span class="text-2xl font-black text-gray-900 tracking-tighter">{{ formatMoney(method.total) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ledger & Associate Oversight -->
                <div class="lg:col-span-3 bg-white rounded-[24px] shadow-sm border border-gray-100 overflow-hidden flex flex-col">
                    <div class="px-12 py-10 border-b border-gray-50 flex flex-col md:flex-row justify-between items-center bg-white gap-6">
                        <div :class="cart.language === 'ar' ? 'text-right' : 'text-left'">
                            <h3 class="text-2xl font-black text-gray-900 tracking-tighter">{{ cart.language === 'ar' ? 'الرقابة النقدية للموظفين' : 'Associate Cash Governance' }}</h3>
                            <p class="text-xs text-gray-400 mt-1 font-bold">{{ cart.language === 'ar' ? 'متابعة فورية لمقدار النقدية بعهدة الموظفين وحالة الدرج' : 'Real-time auditing of operative cash holdings and drawer status' }}</p>
                        </div>
                        <div class="w-14 h-14 bg-gray-50 rounded-[16px] border border-gray-100 flex items-center justify-center text-gray-400 shadow-inner">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        </div>
                    </div>
                    <div class="overflow-x-auto flex-1 h-full min-h-[400px]">
                        <table class="w-full text-left border-collapse min-w-[1000px]" :dir="cart.language === 'ar' ? 'rtl' : 'ltr'">
                            <thead>
                                <tr class="bg-gray-50/30 border-b border-gray-50">
                                    <th class="py-8 px-10 text-[10px] font-black uppercase text-gray-400 tracking-[0.2em]">{{ cart.language === 'ar' ? 'مدير المبيعات' : 'Sales Associate' }}</th>
                                    <th class="py-8 px-6 text-[10px] font-black uppercase text-gray-400 tracking-[0.2em] text-center">{{ cart.language === 'ar' ? 'العمليات' : 'Order Flux' }}</th>
                                    <th class="py-8 px-6 text-[10px] font-black uppercase text-blue-600 tracking-[0.2em] text-center">{{ cart.language === 'ar' ? 'تحصيل إلكتروني' : 'Online Flow' }}</th>
                                    <th class="py-8 px-6 text-[10px] font-black uppercase text-emerald-600 tracking-[0.2em] text-center">{{ cart.language === 'ar' ? 'حقن نقدي (كاش)' : 'Gross Liquid' }}</th>
                                    <th class="py-8 px-6 text-[10px] font-black uppercase text-red-500 tracking-[0.2em] text-center">{{ cart.language === 'ar' ? 'مرتجعات' : 'Refunds' }}</th>
                                    <th class="py-8 px-6 text-[10px] font-black uppercase text-amber-600 tracking-[0.2em] text-center">{{ cart.language === 'ar' ? 'مصروفات عهدة' : 'Exp Expenditures' }}</th>
                                    <th class="py-8 px-10 text-[10px] font-black uppercase text-gray-900 tracking-[0.2em] text-center bg-gray-50/50">{{ cart.language === 'ar' ? 'صافي العهدة النقدية' : 'Drawer Net Liquid' }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <tr v-for="stat in employeeStats" :key="stat.id" class="hover:bg-gray-50/20 transition-all duration-300 group">
                                    <td class="py-8 px-10">
                                        <div class="flex items-center gap-5">
                                            <div class="w-12 h-12 bg-gray-900 text-white rounded-[14px] flex items-center justify-center font-black text-sm shadow-xl group-hover:scale-105 transition-transform">{{ stat.user.name.charAt(0) }}</div>
                                            <div>
                                                <h5 class="text-[14px] font-black text-gray-900 tracking-tight leading-none mb-2">{{ stat.user.name }}</h5>
                                                <p class="text-[9px] font-black text-gray-400 tracking-[0.1em] uppercase">{{ stat.user.email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-8 px-6 text-center">
                                        <span class="bg-white border text-gray-700 border-gray-100 px-4 py-2 rounded-full text-[10px] font-black shadow-sm uppercase tracking-tighter">{{ stat.invoices_count }} {{ cart.language === 'ar' ? 'فاتورة' : 'States' }}</span>
                                    </td>
                                    <td class="py-8 px-6 text-center text-sm font-black text-blue-600 tracking-tighter">
                                        {{ formatMoney(stat.online_sales) }}
                                    </td>
                                    <td class="py-8 px-6 text-center text-sm font-black text-emerald-600 tracking-tighter">
                                        {{ formatMoney(stat.cash_sales) }}
                                    </td>
                                    <td class="py-8 px-6 text-center text-red-500 text-sm font-black tracking-tighter">
                                        {{ formatMoney(stat.refunds || 0) }}
                                    </td>
                                    <td class="py-8 px-6 text-center text-amber-600 text-sm font-black tracking-tighter">
                                        {{ formatMoney(stat.expenses || 0) }}
                                    </td>
                                    <td class="py-8 px-10 text-center bg-gray-50/20">
                                        <span class="text-xl font-black tracking-tighter" :class="stat.net_cash < 0 ? 'text-red-600' : 'text-gray-900'">
                                            {{ formatMoney(stat.net_cash) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr v-if="employeeStats.length === 0">
                                    <td colspan="7" class="py-32 text-center">
                                        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center text-3xl mx-auto mb-6 border border-gray-100 grayscale opacity-30 shadow-inner">📭</div>
                                        <p class="text-gray-300 font-black uppercase tracking-[0.3em] text-[11px]">{{ cart.language === 'ar' ? 'لا يوجد نشاط مسجل للعهد النقدية حالياً' : 'Zero Associate Activity Logged' }}</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </AdminLayout>
</template>
