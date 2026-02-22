<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { cart } from '@/Stores/CartStore';

const props = defineProps({
    customer: Object
});

const formatCurrency = (val) => {
    return new Intl.NumberFormat(cart.language === 'ar' ? 'ar-EG' : 'en-US', { 
        style: 'currency', 
        currency: 'EGP',
        maximumFractionDigits: 0
    }).format(val || 0);
};

</script>

<template>
    <Head :title="customer.name" />

    <AdminLayout>
        <template #header>{{ cart.language === 'ar' ? 'الملف الشخصي والائتماني للعميل' : 'Client Profile Tracking' }} / {{ customer.name }}</template>

        <div class="space-y-8 font-inter" :dir="cart.language === 'ar' ? 'rtl' : 'ltr'">
            <!-- Strategic Performance Card -->
            <div class="bg-white p-10 rounded-[20px] border border-gray-100 shadow-sm relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-gray-50 rounded-full -mr-32 -mt-32 opacity-50"></div>
                
                <div class="flex flex-col md:flex-row items-center gap-10 border-b border-gray-50 pb-10 relative z-10">
                    <div class="w-24 h-24 bg-gray-900 text-white rounded-[24px] flex items-center justify-center font-black text-4xl shadow-2xl group hover:scale-105 transition-all">
                        {{ customer.name.charAt(0) }}
                    </div>
                    <div class="flex-1 text-center md:text-right" :class="cart.language === 'ar' ? 'md:text-right' : 'md:text-left'">
                        <h2 class="text-3xl font-black tracking-tighter text-gray-900">{{ customer.name }}</h2>
                        <div class="flex flex-wrap justify-center md:justify-start gap-6 mt-3" :class="cart.language === 'ar' ? 'md:justify-start' : 'md:justify-start'">
                            <span class="text-[12px] font-bold text-gray-400 uppercase tracking-widest flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                {{ customer.email }}
                            </span>
                            <span class="text-[12px] font-bold text-gray-400 uppercase tracking-widest flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                                {{ customer.phone || '---' }}
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 relative z-10">
                        <Link :href="route('admin.customers.index')" class="px-8 py-4 bg-gray-50 text-gray-600 rounded-[12px] text-[11px] font-black uppercase tracking-widest border border-gray-100 hover:bg-gray-200 transition-all shadow-sm">
                            {{ cart.language === 'ar' ? 'العودة للقائمة' : 'Return to Core' }}
                        </Link>
                    </div>
                </div>

                <!-- Financial Exposure Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 pt-10 relative z-10">
                    <div class="p-8 rounded-[16px] bg-gray-50 border border-gray-100 shadow-sm flex flex-col items-center text-center group hover:bg-white transition-all">
                        <span class="text-[11px] font-bold uppercase text-gray-400 tracking-[0.2em] mb-2">{{ cart.language === 'ar' ? 'إجمالي المديونية التاريخية' : 'Lifetime Invoiced' }}</span>
                        <h4 class="text-3xl font-black text-gray-900 tracking-tighter">{{ formatCurrency(customer.total_invoiced) }}</h4>
                    </div>

                    <div class="p-8 rounded-[16px] bg-emerald-50 border border-emerald-100 shadow-sm flex flex-col items-center text-center group hover:bg-white transition-all">
                        <span class="text-[11px] font-bold uppercase text-emerald-600 tracking-[0.2em] mb-2">{{ cart.language === 'ar' ? 'إجمالي المبالغ المحصلة' : 'Settled Amount' }}</span>
                        <h4 class="text-3xl font-black text-emerald-600 tracking-tighter">{{ formatCurrency(customer.total_paid) }}</h4>
                    </div>

                    <div class="p-8 rounded-[16px] bg-red-50 border border-red-100 shadow-sm flex flex-col items-center text-center group hover:bg-white transition-all">
                        <span class="text-[11px] font-bold uppercase text-red-600 tracking-[0.2em] mb-2">{{ cart.language === 'ar' ? 'صافي المديونية الحالية' : 'Current Exposure' }}</span>
                        <h4 class="text-3xl font-black text-red-600 tracking-tighter">{{ formatCurrency(customer.total_debt) }}</h4>
                    </div>
                </div>
            </div>

            <!-- Interaction & Ledger History -->
            <div class="bg-white rounded-[20px] border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-10 py-8 border-b border-gray-50 bg-gray-50/20 flex flex-col md:flex-row items-center justify-between gap-4">
                    <div>
                        <h4 class="text-xl font-black tracking-tighter text-gray-900">{{ cart.language === 'ar' ? 'سجل المعاملات والتحصيلات' : 'Transaction & Ledger History' }}</h4>
                        <p class="text-xs font-bold text-gray-400 mt-1">{{ cart.language === 'ar' ? 'أرشيف الفواتير المصدرة وحركة الحساب المقيدة' : 'Audit logs of issued statements and settlement fluxes' }}</p>
                    </div>
                    <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center border border-gray-200 shadow-inner">
                        <span class="text-sm font-black text-gray-900">{{ customer.invoices?.length || 0 }}</span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left" :dir="cart.language === 'ar' ? 'rtl' : 'ltr'">
                        <thead>
                            <tr class="bg-gray-50/30 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 border-b border-gray-50">
                                <th class="px-10 py-6">{{ cart.language === 'ar' ? 'التاريخ' : 'Auth Date' }}</th>
                                <th class="px-10 py-6">{{ cart.language === 'ar' ? 'رقم الفاتورة' : 'Invoice Reference' }}</th>
                                <th class="px-10 py-6 text-center">{{ cart.language === 'ar' ? 'حالة السداد' : 'Settlement Status' }}</th>
                                <th class="px-10 py-6 text-center">{{ cart.language === 'ar' ? 'القيمة الإجمالية' : 'Aggregated Total' }}</th>
                                <th class="px-10 py-6 text-center">{{ cart.language === 'ar' ? 'المحصل فعلياً' : 'Liquid Paid' }}</th>
                                <th class="px-10 py-6" :class="cart.language === 'ar' ? 'text-left' : 'text-right'">{{ cart.language === 'ar' ? 'المستندات' : 'Documentation' }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <tr v-for="inv in customer.invoices" :key="inv.id" class="hover:bg-gray-50/30 transition-all group">
                                <td class="px-10 py-6 text-sm font-bold text-gray-400">{{ new Date(inv.created_at).toLocaleDateString(cart.language === 'ar' ? 'ar-EG' : 'en-US') }}</td>
                                <td class="px-10 py-6 text-sm font-black text-gray-900 tracking-tighter">#{{ inv.invoice_number }}</td>
                                <td class="px-10 py-6 text-center">
                                    <span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest border shadow-sm transition-all"
                                        :class="{
                                            'bg-emerald-50 text-emerald-600 border-emerald-100': inv.status === 'paid',
                                            'bg-amber-50 text-amber-600 border-amber-100': inv.status === 'partial' || inv.status === 'pending',
                                            'bg-red-50 text-red-600 border-red-100': inv.status === 'cancelled'
                                        }">
                                        {{ cart.t(inv.status) }}
                                    </span>
                                </td>
                                <td class="px-10 py-6 text-center text-sm font-black text-gray-900 tracking-tighter">{{ formatCurrency(inv.total) }}</td>
                                <td class="px-10 py-6 text-center text-sm font-black tracking-tighter" :class="inv.amount_paid >= inv.total ? 'text-emerald-600' : 'text-red-500'">{{ formatCurrency(inv.amount_paid) }}</td>
                                <td class="px-10 py-6" :class="cart.language === 'ar' ? 'text-left' : 'text-right'">
                                    <a :href="route('admin.invoices.download', inv.secure_token)" target="_blank" class="inline-flex items-center gap-3 px-6 py-2.5 bg-gray-900 text-white rounded-[12px] text-[10px] font-black uppercase tracking-widest hover:bg-gray-800 transition-all shadow-lg shadow-gray-900/10 active:scale-95">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                        {{ cart.language === 'ar' ? 'معاينة PDF' : 'Download PDF' }}
                                    </a>
                                </td>
                            </tr>
                            <tr v-if="!customer.invoices || customer.invoices.length === 0">
                                <td colspan="6" class="py-32 text-center">
                                    <div class="text-5xl mb-6 grayscale opacity-20">📭</div>
                                    <p class="font-black text-gray-300 uppercase tracking-[0.3em] text-[11px]">{{ cart.language === 'ar' ? 'لا يوجد سجل معاملات ائتمانية لهذا الحساب' : 'Empty Transaction Ledger identified' }}</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
