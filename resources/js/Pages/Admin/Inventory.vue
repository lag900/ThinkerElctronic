<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import { cart } from '@/Stores/CartStore';

// Mock inventory data - placeholder for real data streaming
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

const getStatusLabel = (status) => {
    if (cart.language === 'ar') {
        const mapAr = {
            'stable': 'مخزون مستقر',
            'low': 'مخزون منخفض',
            'out': 'نفدت الكمية'
        };
        return mapAr[status] || status;
    }
    const map = {
        'stable': 'Stable Stock',
        'low': 'Low Level',
        'out': 'Out of Stock'
    };
    return map[status] || status;
};

const formatCurrency = (val) => {
    return new Intl.NumberFormat(cart.language === 'ar' ? 'ar-EG' : 'en-US', { 
        style: 'currency', 
        currency: 'EGP',
        maximumFractionDigits: 0
    }).format(val || 0);
};

const formatNumber = (val) => {
    return new Intl.NumberFormat(cart.language === 'ar' ? 'ar-EG' : 'en-US').format(val || 0);
};
</script>

<template>
    <Head :title="cart.language === 'ar' ? 'إدارة المخزون' : 'Inventory Oversight'" />

    <AdminLayout>
        <template #header>{{ cart.language === 'ar' ? 'إدارة حركات المخزون والجرد' : 'Inventory Flux & Audit Management' }}</template>

        <div class="space-y-8 font-inter" :dir="cart.language === 'ar' ? 'rtl' : 'ltr'">
            <!-- Operational Control Hub -->
            <div class="flex flex-col md:flex-row gap-6 p-8 bg-white rounded-[20px] border border-gray-100 shadow-sm items-center relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-gray-50 rounded-full -mr-16 -mt-16 opacity-50"></div>
                
                <div class="flex-1 relative w-full z-10">
                    <svg class="absolute w-5 h-5 text-gray-400 top-1/2 -translate-y-1/2" :class="cart.language === 'ar' ? 'right-6' : 'left-6'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    <input type="text" :placeholder="cart.language === 'ar' ? 'البحث بالاسم أو كود الصنف (SKU)...' : 'Scan Resource or SKU Identifier...'" class="w-full py-4.5 bg-gray-50 border-none rounded-[16px] text-sm font-black text-gray-900 focus:ring-2 focus:ring-gray-900 transition-all shadow-inner placeholder:text-gray-400" :class="cart.language === 'ar' ? 'pr-16 pl-8' : 'pl-16 pr-8'">
                </div>
                <div class="flex items-center gap-4 w-full md:w-auto z-10">
                    <button class="flex-1 md:flex-none px-10 py-4.5 bg-white border border-gray-200 rounded-[16px] text-[11px] font-black uppercase tracking-widest text-gray-500 hover:bg-gray-50 transition-all shadow-sm active:scale-95">
                        {{ cart.language === 'ar' ? 'الفلاتر' : 'Configurations' }}
                    </button>
                    <button class="flex-1 md:flex-none px-10 py-4.5 bg-gray-900 text-white rounded-[16px] text-[11px] font-black uppercase tracking-widest shadow-2xl shadow-gray-900/20 hover:bg-gray-800 transition-all active:scale-95">
                        + {{ cart.language === 'ar' ? 'إضافة مخزون' : 'Add Stock Entry' }}
                    </button>
                </div>
            </div>

            <!-- Global Asset Registry -->
            <div class="bg-white rounded-[24px] border border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left" :dir="cart.language === 'ar' ? 'rtl' : 'ltr'">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-100 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">
                                <th class="px-10 py-8">{{ cart.language === 'ar' ? 'بيانات الصنف' : 'Item Signature' }}</th>
                                <th class="px-10 py-8 text-center">{{ cart.language === 'ar' ? 'كود الصنف' : 'SKU' }}</th>
                                <th class="px-10 py-8 text-center">{{ cart.language === 'ar' ? 'الكمية الحالية' : 'Current Density' }}</th>
                                <th class="px-10 py-8 text-center">{{ cart.language === 'ar' ? 'حالة التوفر' : 'Health Status' }}</th>
                                <th class="px-10 py-8 text-center">{{ cart.language === 'ar' ? 'التكلفة الأساسية' : 'Base Cost Basis' }}</th>
                                <th class="px-10 py-8" :class="cart.language === 'ar' ? 'text-left' : 'text-right'">{{ cart.language === 'ar' ? 'الإجراءات' : 'Audit Control' }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <tr v-for="item in inventory" :key="item.id" class="hover:bg-gray-50/20 transition-all group duration-300">
                                <td class="px-10 py-8">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-black text-gray-900 tracking-tighter">{{ item.name }}</span>
                                        <div v-if="item.price" class="flex items-center gap-2 mt-2">
                                            <span class="text-[9px] text-gray-400 font-black uppercase tracking-widest">{{ cart.language === 'ar' ? 'سعر البيع المقترح:' : 'MSRP:' }}</span>
                                            <span class="text-[11px] text-emerald-600 font-black tracking-tight">{{ formatCurrency(item.price) }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-10 py-8 text-center">
                                    <span class="text-[11px] font-black text-gray-900 bg-gray-50 border border-gray-100 px-4 py-2 rounded-[10px] shadow-sm">{{ item.sku }}</span>
                                </td>
                                <td class="px-10 py-8 text-center">
                                    <span class="text-lg font-black text-gray-900 tracking-tighter">{{ formatNumber(item.stock) }}</span>
                                </td>
                                <td class="px-10 py-8 text-center">
                                    <div class="flex items-center justify-center">
                                        <span 
                                            class="inline-flex px-5 py-2.5 rounded-full text-[10px] font-black uppercase tracking-widest border shadow-sm transition-all animate-in zoom-in-50 duration-500"
                                            :class="getStatusClass(item.status)"
                                        >
                                            <div class="w-2.5 h-2.5 rounded-full mr-2" :class="item.status === 'out' ? 'bg-red-500' : (item.status === 'low' ? 'bg-amber-500' : 'bg-emerald-500')"></div>
                                            {{ getStatusLabel(item.status) }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-10 py-8 text-center">
                                    <span class="text-sm font-black text-gray-400 tracking-tighter">{{ formatCurrency(item.cost) }}</span>
                                </td>
                                <td class="px-10 py-8" :class="cart.language === 'ar' ? 'text-left' : 'text-right'">
                                    <div class="flex items-center gap-3" :class="cart.language === 'ar' ? 'justify-start' : 'justify-end'">
                                        <button class="p-3 bg-gray-900 text-white rounded-[12px] hover:bg-gray-800 transition-all shadow-lg shadow-gray-900/10 active:scale-90" title="Adjust Balance">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                                        </button>
                                        <button class="p-3 bg-white border border-gray-100 text-gray-400 hover:text-gray-900 hover:border-gray-900 rounded-[12px] transition-all shadow-sm active:scale-90" title="Modify Registry">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- Empty State Oversight -->
                <div v-if="inventory.length === 0" class="py-32 text-center bg-gray-50/20">
                    <div class="w-24 h-24 bg-white border border-gray-100 rounded-full flex items-center justify-center text-4xl mx-auto mb-8 grayscale opacity-30 shadow-inner">📦</div>
                    <p class="text-gray-300 font-black uppercase tracking-[0.3em] text-[11px]">{{ cart.language === 'ar' ? 'لا توجد حركة مسجلة للأصناف في المستودعات' : 'Zero Inventory Nodes Tracked' }}</p>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
