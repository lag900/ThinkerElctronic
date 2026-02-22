<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
import { cart } from '@/Stores/CartStore';

const props = defineProps({
    valuation: Array,
    total_valuation: Number,
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat(cart.language === 'ar' ? 'ar-EG' : 'en-US', {
        style: 'currency',
        currency: 'EGP',
    }).format(value);
};
</script>

<template>
    <Head :title="cart.language === 'ar' ? 'تقرير تقييم المخزون المادي' : 'Inventory Valuation Framework'" />

    <AdminLayout>
        <template #header>{{ cart.language === 'ar' ? 'الجرد والتقييم المالي للمخازن' : 'Physical Asset Audit & Structural Valuation' }}</template>

        <div class="space-y-10 font-inter" :dir="cart.language === 'ar' ? 'rtl' : 'ltr'">
            <!-- Summary Elevation -->
            <div class="bg-gray-900 rounded-[32px] p-12 text-white relative overflow-hidden group shadow-2xl">
                <div class="absolute top-0 right-0 p-12 opacity-5">
                    <svg class="w-48 h-48" fill="currentColor" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                </div>
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-end gap-10">
                    <div>
                        <h3 class="text-4xl font-black tracking-tighter">{{ formatCurrency(total_valuation) }}</h3>
                        <p class="text-[11px] font-black text-white/40 uppercase tracking-[0.3em] mt-3">{{ cart.language === 'ar' ? 'إجمالي قيمة رأس المال المخزن' : 'Gross Strategic Liquidity in Physical Assets' }}</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur-md border border-white/20 px-8 py-4 rounded-[20px] flex items-center gap-4">
                        <div class="w-3 h-3 rounded-full bg-emerald-400"></div>
                        <span class="text-[10px] font-black uppercase tracking-widest">{{ valuation.length }} {{ cart.language === 'ar' ? 'منتج تحت الجرد' : 'Distinct Assets Audited' }}</span>
                    </div>
                </div>
            </div>

            <!-- Detailed Valuation Matrix -->
            <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm overflow-hidden">
                <table class="w-full" :class="cart.language === 'ar' ? 'text-right' : 'text-left'">
                    <thead>
                        <tr class="bg-gray-50/50 border-b border-gray-100">
                            <th class="px-10 py-8 text-[11px] font-black uppercase text-gray-400 tracking-widest">{{ cart.language === 'ar' ? 'المنتج الإستراتيجي' : 'Strategic Asset' }}</th>
                            <th class="px-10 py-8 text-[11px] font-black uppercase text-gray-400 tracking-widest text-center">{{ cart.language === 'ar' ? 'الكمية الحالية' : 'Current Stocks' }}</th>
                            <th class="px-10 py-8 text-[11px] font-black uppercase text-gray-400 tracking-widest text-center">{{ cart.language === 'ar' ? 'عدد دفعات FIFO' : 'Active FIFO Batches' }}</th>
                            <th class="px-10 py-8 text-[11px] font-black uppercase text-gray-400 tracking-widest text-right">{{ cart.language === 'ar' ? 'القيمة الإجمالية بالتكلفة' : 'Net Landed Valuation' }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr v-for="item in valuation" :key="item.id" class="hover:bg-gray-50 transition-all group">
                            <td class="px-10 py-8">
                                <div class="font-black text-gray-900 text-sm">{{ item.name }}</div>
                                <div class="text-[9px] text-gray-400 font-bold uppercase tracking-widest mt-1">Ref ID: #{{ item.id }}</div>
                            </td>
                            <td class="px-10 py-8 text-center text-sm font-black text-gray-600">{{ item.current_stock }} units</td>
                            <td class="px-10 py-8 text-center">
                                <span class="bg-blue-50 text-blue-600 px-4 py-2 rounded-lg text-[10px] font-black border border-blue-100">{{ item.batches_count }} {{ cart.language === 'ar' ? 'دفعة فعالة' : 'Batches' }}</span>
                            </td>
                            <td class="px-10 py-8 text-right">
                                <div class="text-lg font-black text-gray-900 tracking-tighter">{{ formatCurrency(item.valuation) }}</div>
                                <div class="text-[9px] font-black text-gray-400 uppercase tracking-widest mt-0.5">{{ cart.language === 'ar' ? 'قيمة مادية حقيقية' : 'Structural Value' }}</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>
