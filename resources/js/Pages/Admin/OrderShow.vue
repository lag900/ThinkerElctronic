<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { cart } from '@/Stores/CartStore';

const props = defineProps({
    order: Object
});

const formatCurrency = (val) => {
    return new Intl.NumberFormat(cart.language === 'ar' ? 'ar-EG' : 'en-US', { 
        style: 'currency', 
        currency: 'EGP',
        maximumFractionDigits: 0
    }).format(val || 0);
};

const previewInvoice = (orderId) => {
    window.open(`/admin/orders/${orderId}/invoice/preview`, '_blank');
};
</script>

<template>
    <Head :title="cart.language === 'ar' ? 'تفاصيل المعاملة' : 'Transaction Details'" />

    <AdminLayout>
        <template #header>{{ cart.language === 'ar' ? 'تفاصيل المعاملة والتحصيل' : 'Transaction & Settlement Details' }}</template>

        <div class="space-y-8 font-inter" :dir="cart.language === 'ar' ? 'rtl' : 'ltr'">
            <!-- Overview & Status -->
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 bg-white p-8 rounded-[16px] border border-gray-100 shadow-sm relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-gray-900/5 rounded-bl-[64px] -mr-8 -mt-8"></div>
                <div class="flex items-center gap-6 relative z-10">
                    <div class="w-16 h-16 bg-gray-900 text-white rounded-[16px] flex items-center justify-center text-xl font-black shadow-lg">#{{ order.id }}</div>
                    <div>
                        <h4 class="text-2xl font-black text-gray-900 tracking-tighter">{{ cart.language === 'ar' ? 'فاتورة رقم' : 'Transaction Reference' }} #{{ order.id }}</h4>
                        <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mt-1">{{ cart.language === 'ar' ? 'حررت بتاريخ' : 'Authenticated' }}: {{ new Date(order.created_at).toLocaleString() }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-4 relative z-10">
                    <span class="px-6 py-2.5 rounded-full text-[11px] font-black uppercase tracking-widest border shadow-sm"
                        :class="{
                            'bg-amber-50 text-amber-600 border-amber-100': order.status === 'pending' || order.status === 'processing',
                            'bg-emerald-50 text-emerald-600 border-emerald-100': order.status === 'completed',
                            'bg-red-50 text-red-600 border-red-100': order.status === 'failed'
                        }">
                        {{ cart.t(order.status) }}
                    </span>
                    <button @click="previewInvoice(order.id)" class="px-6 py-2.5 bg-gray-900 text-white rounded-[12px] text-[11px] font-black uppercase tracking-widest hover:bg-gray-800 transition-all shadow-lg shadow-gray-900/10">
                        {{ cart.language === 'ar' ? 'معاينة الفاتورة' : 'Preview Document' }}
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Line Items Database -->
                <div class="lg:col-span-2 bg-white rounded-[16px] border border-gray-100 shadow-sm overflow-hidden flex flex-col">
                    <div class="p-8 border-b border-gray-50 bg-gray-50/10">
                        <h5 class="text-lg font-bold text-gray-900 tracking-tight">{{ cart.language === 'ar' ? 'بيان الأصناف والخدمات' : 'Itemized Resource Allocation' }}</h5>
                        <p class="text-xs text-gray-400 mt-1">{{ cart.language === 'ar' ? 'تفاصيل المنتجات المدرجة والكميات' : 'Detailed breakdown of inventory nodes' }}</p>
                    </div>
                    <div class="flex-1 overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-gray-50/50 border-b border-gray-100 text-[10px] font-bold uppercase tracking-widest text-gray-400">
                                    <th class="px-8 py-5">{{ cart.language === 'ar' ? 'الصنف' : 'Item Description' }}</th>
                                    <th class="px-8 py-5 text-center">{{ cart.language === 'ar' ? 'الكمية' : 'Qty' }}</th>
                                    <th class="px-8 py-5 text-center">{{ cart.language === 'ar' ? 'سعر الوحدة' : 'Unit Basis' }}</th>
                                    <th class="px-8 py-5 text-right">{{ cart.language === 'ar' ? 'الإجمالي' : 'Aggregated' }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <tr v-for="item in order.items" :key="item.id" class="group hover:bg-gray-50/20 transition-all">
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 bg-gray-50 border border-gray-100 rounded-[10px] overflow-hidden group-hover:shadow-md transition-all">
                                                <img v-if="item.product?.image_url" :src="item.product.image_url" class="w-full h-full object-cover">
                                                <div v-else class="w-full h-full flex items-center justify-center text-xs font-bold text-gray-300">{{ item.product?.name ? item.product.name.charAt(0) : '?' }}</div>
                                            </div>
                                            <span class="text-sm font-bold text-gray-900 tracking-tight">{{ item.product?.name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 text-center text-sm font-black text-gray-900 tracking-tighter">{{ item.quantity }}</td>
                                    <td class="px-8 py-6 text-center text-xs font-bold text-gray-400">{{ formatCurrency(item.price) }}</td>
                                    <td class="px-8 py-6 text-right text-sm font-black text-gray-900 tracking-tighter">{{ formatCurrency(item.price * item.quantity) }}</td>
                                </tr>
                                <tr v-if="!order.items || order.items.length === 0">
                                    <td colspan="4" class="py-24 text-center">
                                        <div class="text-3xl mb-4 grayscale opacity-20">📦</div>
                                        <p class="font-bold text-gray-300 uppercase tracking-widest text-[10px]">{{ cart.language === 'ar' ? 'لا يوجد أصناف في هذه الفاتورة' : 'No items found' }}</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- Financial Aggregation -->
                    <div class="p-8 bg-gray-50/20 space-y-4">
                        <div class="flex justify-between items-center text-gray-400 text-xs font-bold uppercase tracking-widest">
                            <span>{{ cart.language === 'ar' ? 'المجموع الأساسي' : 'Subtotal Basis' }}</span>
                            <span>{{ formatCurrency(order.total) }}</span>
                        </div>
                        <div class="flex justify-between items-center pt-4 border-t border-gray-100">
                            <span class="text-sm font-black text-gray-900 uppercase tracking-widest">{{ cart.language === 'ar' ? 'إجمالي المستحق' : 'Aggregated Total' }}</span>
                            <span class="text-2xl font-black text-gray-900 tracking-tighter">{{ formatCurrency(order.total) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Client & Logic Context -->
                <div class="space-y-8">
                    <!-- Client Dossier -->
                    <div class="bg-white rounded-[16px] border border-gray-100 shadow-sm p-8 space-y-6">
                        <div class="pb-4 border-b border-gray-50">
                            <h6 class="text-[11px] font-bold uppercase tracking-widest text-gray-400">{{ cart.language === 'ar' ? 'ملف العميل المخصص' : 'Client Profile Context' }}</h6>
                        </div>
                        <div v-if="order.customer" class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-gray-900 text-white rounded-[12px] flex items-center justify-center text-lg font-black shadow-md">{{ order.customer.name.charAt(0) }}</div>
                            <div>
                                <p class="text-sm font-black text-gray-900 tracking-tighter">{{ order.customer.name }}</p>
                                <p class="text-[10px] text-emerald-600 font-bold uppercase tracking-widest mt-0.5">{{ cart.language === 'ar' ? 'حساب نشط' : 'Active Account' }}</p>
                            </div>
                        </div>
                        <div v-else class="text-sm font-bold text-gray-400 italic">{{ cart.language === 'ar' ? 'معاملة نقدية مباشرة (كاشير)' : 'Direct POS Settlement' }}</div>
                        
                        <div class="space-y-4 pt-2">
                            <div class="flex items-center gap-3 text-gray-500">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                <span class="text-xs font-semibold">{{ order.customer?.email || 'N/A' }}</span>
                            </div>
                            <div class="flex items-center gap-3 text-gray-500">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                                <span class="text-xs font-semibold">{{ order.customer?.phone || 'N/A' }}</span>
                            </div>
                        </div>
                        <Link v-if="order.customer" :href="route('admin.customers.show', order.customer.id)" class="block w-full text-center py-4.5 bg-gray-50 text-gray-600 rounded-[12px] text-[11px] font-bold uppercase tracking-widest hover:bg-gray-900 hover:text-white transition-all shadow-sm">
                            {{ cart.language === 'ar' ? 'عرض السجل الائتماني' : 'View Total Credit History' }}
                        </Link>
                    </div>

                    <!-- Internal Logic -->
                    <div class="bg-gray-900 rounded-[16px] shadow-2xl p-8 text-white space-y-6">
                        <h6 class="text-[10px] font-bold uppercase tracking-widest text-gray-400">{{ cart.language === 'ar' ? 'خوارزمية الحساب' : 'Administrative Logic' }}</h6>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-[11px] font-bold text-gray-400">{{ cart.language === 'ar' ? 'الموظف المحرر' : 'Originating Entity' }}</span>
                                <span class="text-xs font-black">{{ order.employee?.name || 'SYSTEM' }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-[11px] font-bold text-gray-400">{{ cart.language === 'ar' ? 'فروع التنفيذ' : 'Node Branch' }}</span>
                                <span class="text-xs font-black">HQ Central</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
