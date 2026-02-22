<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { cart } from '@/Stores/CartStore';

const props = defineProps({
    report: Array,
    range: String,
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat(cart.language === 'ar' ? 'ar-EG' : 'en-US', {
        style: 'currency',
        currency: 'EGP',
    }).format(value);
};

const changeRange = (newRange) => {
    router.visit(route('admin.inventory.reports.profit', { range: newRange }), {
        preserveScroll: true
    });
};
</script>

<template>
    <Head :title="cart.language === 'ar' ? 'تقرير الأرباح التفصيلي' : 'Profit Strategy Report'" />

    <AdminLayout>
        <template #header>{{ cart.language === 'ar' ? 'سجل الأرباح والعوائد التشغيلية' : 'Operational Yield & Profit Intelligence' }}</template>

        <div class="space-y-10 font-inter" :dir="cart.language === 'ar' ? 'rtl' : 'ltr'">
            <!-- View Logic Selector -->
            <div class="flex items-center justify-between bg-white p-8 rounded-[24px] border border-gray-100 shadow-sm relative overflow-hidden group">
                <div class="absolute inset-0 bg-gray-50/50 -z-0"></div>
                <div class="relative z-10">
                    <h3 class="text-2xl font-black text-gray-900 tracking-tighter uppercase">{{ cart.language === 'ar' ? 'تحليل العوائد حسب الدورة' : 'Yield Cycle Analysis' }}</h3>
                    <p class="text-[10px] font-black text-gray-400 mt-1 uppercase tracking-widest">{{ cart.language === 'ar' ? 'اختر النطاق الزمني لعرض الأداء المالي' : 'Select operational timeframe for performance scanning' }}</p>
                </div>
                <div class="flex bg-gray-100 p-1.5 rounded-[20px] relative z-10 border border-gray-200 shadow-inner">
                    <button 
                        @click="changeRange('day')"
                        class="px-8 py-3 rounded-[16px] text-xs font-black uppercase tracking-widest transition-all"
                        :class="range === 'day' ? 'bg-white text-gray-900 shadow-xl border border-gray-200' : 'text-gray-400 hover:text-gray-600'"
                    >{{ cart.language === 'ar' ? 'اليوم' : '24H' }}</button>
                    <button 
                        @click="changeRange('week')"
                        class="px-8 py-3 rounded-[16px] text-xs font-black uppercase tracking-widest transition-all"
                        :class="range === 'week' ? 'bg-white text-gray-900 shadow-xl border border-gray-200' : 'text-gray-400 hover:text-gray-600'"
                    >{{ cart.language === 'ar' ? 'الأسبوع' : 'Week' }}</button>
                    <button 
                        @click="changeRange('month')"
                        class="px-8 py-3 rounded-[16px] text-xs font-black uppercase tracking-widest transition-all"
                        :class="range === 'month' ? 'bg-white text-gray-900 shadow-xl border border-gray-200' : 'text-gray-400 hover:text-gray-600'"
                    >{{ cart.language === 'ar' ? 'الشهر' : 'Month' }}</button>
                    <button 
                        @click="changeRange('year')"
                        class="px-8 py-3 rounded-[16px] text-xs font-black uppercase tracking-widest transition-all"
                        :class="range === 'year' ? 'bg-white text-gray-900 shadow-xl border border-gray-200' : 'text-gray-400 hover:text-gray-600'"
                    >{{ cart.language === 'ar' ? 'السنة' : 'Annual' }}</button>
                </div>
            </div>

            <!-- Profit Intelligence Matrix -->
            <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm overflow-hidden">
                <table class="w-full" :class="cart.language === 'ar' ? 'text-right' : 'text-left'">
                    <thead>
                        <tr class="bg-gray-50/50 border-b border-gray-100">
                            <th class="px-10 py-8 text-[11px] font-black uppercase text-gray-400 tracking-widest">{{ cart.language === 'ar' ? 'التاريخ' : 'Operational Cycle' }}</th>
                            <th class="px-10 py-8 text-[11px] font-black uppercase text-gray-400 tracking-widest text-center">{{ cart.language === 'ar' ? 'إجمالي الإيراد' : 'Gross Revenue' }}</th>
                            <th class="px-10 py-8 text-[11px] font-black uppercase text-gray-400 tracking-widest text-center">{{ cart.language === 'ar' ? 'التكلفة الحقيقية (FIFO)' : 'Real Cost (FIFO)' }}</th>
                            <th class="px-10 py-8 text-[11px] font-black uppercase text-gray-400 tracking-widest text-right">{{ cart.language === 'ar' ? 'صافي الربح' : 'Net Strategic Yield' }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr v-for="row in report" :key="row.date" class="hover:bg-gray-50 transition-all group">
                            <td class="px-10 py-8">
                                <span class="text-sm font-black text-gray-900">{{ new Date(row.date).toLocaleDateString(cart.language === 'ar' ? 'ar-EG' : 'en-US', { day: 'numeric', month: 'long', year: 'numeric' }) }}</span>
                            </td>
                            <td class="px-10 py-8 text-center">
                                <span class="text-sm font-black text-gray-600">{{ formatCurrency(row.revenue) }}</span>
                            </td>
                            <td class="px-10 py-8 text-center">
                                <div class="inline-flex items-center gap-2 bg-gray-50 px-4 py-2 rounded-full border border-gray-100">
                                    <div class="w-1.5 h-1.5 rounded-full bg-blue-400"></div>
                                    <span class="text-[11px] font-black text-gray-500 uppercase">{{ formatCurrency(row.cost) }}</span>
                                </div>
                            </td>
                            <td class="px-10 py-8 text-right">
                                <div class="inline-block px-8 py-3 rounded-[16px] border shadow-sm transition-all group-hover:scale-105"
                                     :class="(row.profit > 0) ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-red-50 text-red-700 border-red-100'">
                                    <span class="text-lg font-black tracking-tighter">{{ formatCurrency(row.profit) }}</span>
                                    <div class="text-[9px] font-black uppercase tracking-widest mt-0.5 opacity-60">{{ (row.profit > 0) ? (cart.language === 'ar' ? 'زيادة إيجابية' : 'Positive Surplus') : (cart.language === 'ar' ? 'عجز تحت التكلفة' : 'Deficit Detected') }}</div>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="report.length === 0">
                            <td colspan="4" class="px-10 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-16 h-16 text-gray-200 mb-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                                    <p class="text-xs font-black text-gray-400 uppercase tracking-widest">{{ cart.language === 'ar' ? 'لا توجد بيانات عملياتية لهذا النطاق' : 'No tactical data recorded for this timeframe' }}</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>
