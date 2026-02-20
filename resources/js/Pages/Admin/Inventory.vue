<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import { cart } from '@/Stores/CartStore';

// Mock inventory data
const inventory = ref([
    { id: 1, name: 'Arduino Uno R3', sku: 'TH-ARD-001', stock: 125, status: 'stable', cost: 15.00, price: 25.00 },
    { id: 2, name: 'Raspberry Pi 4 Model B', sku: 'TH-RP-004', stock: 4, status: 'low', cost: 45.00, price: 75.00 },
    { id: 3, name: 'Lipo Battery 1000mAh', sku: 'TH-BAT-001', stock: 0, status: 'out', cost: 8.00, price: 18.00 },
    { id: 4, name: 'Jumper Wires (60pcs)', sku: 'TH-ACC-002', stock: 540, status: 'stable', cost: 1.20, price: 5.00 },
]);

const getStatusClass = (status) => {
    if (status === 'stable') return 'bg-emerald-50 text-emerald-600 border-emerald-100';
    if (status === 'low') return 'bg-amber-50 text-amber-600 border-amber-100';
    return 'bg-red-50 text-red-600 border-red-100';
};

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
    <Head :title="cart.t('inventory')" />

    <AdminLayout>
        <template #header>{{ cart.t('inventory') }}</template>

        <div class="space-y-8">
            <!-- Search & Filter Bar -->
            <div class="flex gap-4 p-4 bg-white rounded-[24px] border border-slate-100 shadow-sm items-center">
                <div class="flex-1 relative">
                    <svg class="absolute w-5 h-5 text-slate-300 top-1/2 -translate-y-1/2" :class="cart.language === 'ar' ? 'right-4' : 'left-4'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    <input type="text" :placeholder="cart.language === 'ar' ? 'البحث عن منتج أو كود...' : 'Search by SKU or Asset Name...'" class="w-full py-3 bg-slate-50 border-none rounded-xl text-sm font-bold focus:ring-2 focus:ring-indigo-500 transition-all" :class="cart.language === 'ar' ? 'pr-12 pl-6' : 'pl-12 pr-6'">
                </div>
                <button class="px-6 py-3 bg-white border border-slate-200 rounded-xl text-[10px] font-black uppercase tracking-widest text-slate-500 hover:bg-slate-50 transition-colors">
                    {{ cart.language === 'ar' ? 'الفلاتر' : 'Filters' }}
                </button>
                <button class="px-6 py-3 bg-indigo-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-indigo-100">
                    + {{ cart.language === 'ar' ? 'إضافة مخزون' : 'Add Stock' }}
                </button>
            </div>

            <!-- Inventory Table -->
            <div class="bg-white rounded-[40px] border border-slate-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left" :class="{'text-right': cart.language === 'ar'}">
                        <thead>
                            <tr class="bg-slate-50/50">
                                <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-slate-400">{{ cart.language === 'ar' ? 'المنتج' : 'Resource Protocol' }}</th>
                                <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-slate-400">{{ cart.language === 'ar' ? 'كود الصنف' : 'SKU Node' }}</th>
                                <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-slate-400">{{ cart.language === 'ar' ? 'الكمية' : 'Quant. Stored' }}</th>
                                <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-slate-400">{{ cart.language === 'ar' ? 'الحالة' : 'Health Status' }}</th>
                                <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-slate-400">{{ cart.language === 'ar' ? 'التكلفة' : 'Unit Cost' }}</th>
                                <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-slate-400" :class="cart.language === 'ar' ? 'text-left' : 'text-right'">{{ cart.t('action') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <tr v-for="item in inventory" :key="item.id" class="hover:bg-slate-50/50 transition-colors group">
                                <td class="px-8 py-6">
                                    <span class="text-sm font-black text-slate-900 group-hover:text-indigo-600 transition-colors">{{ item.name }}</span>
                                </td>
                                <td class="px-8 py-6">
                                    <code class="text-[11px] font-bold text-slate-400 bg-slate-50 px-2 py-1 rounded-md">{{ item.sku }}</code>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="text-sm font-black text-slate-700">{{ formatNumber(item.stock) }}</span>
                                </td>
                                <td class="px-8 py-6">
                                    <span 
                                        class="inline-flex px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest border"
                                        :class="getStatusClass(item.status)"
                                    >
                                        {{ item.status }}
                                    </span>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="text-sm font-bold text-slate-500">{{ formatCurrency(item.cost) }}</span>
                                </td>
                                <td class="px-8 py-6" :class="cart.language === 'ar' ? 'text-left' : 'text-right'">
                                    <button class="text-slate-300 hover:text-indigo-600 transition-colors">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
