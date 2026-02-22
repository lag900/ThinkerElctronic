<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { cart } from '@/Stores/CartStore';

const props = defineProps({
    products: Object,
    filters: Object
});

const search = ref(props.filters?.search || '');

watch(search, (val) => {
    router.get(route('admin.products.index'), { search: val }, {
        preserveState: true,
        replace: true
    });
});

const deleteProduct = (id) => {
    if (confirm(cart.language === 'ar' ? 'هل أنت متأكد من حذف هذا المنتج نهائياً؟' : 'Are you sure you want to permanently delete this asset?')) {
        router.delete(route('admin.products.destroy', id));
    }
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
    <Head :title="cart.language === 'ar' ? 'كتالوج المنتجات' : 'Product Catalog'" />

    <AdminLayout>
        <template #header>{{ cart.language === 'ar' ? 'إدارة كتالوج المنتجات والأصناف' : 'Product Catalog Management' }}</template>

        <div class="space-y-8 font-inter" :dir="cart.language === 'ar' ? 'rtl' : 'ltr'">
            <!-- Operational Horizon -->
            <div class="flex flex-col md:flex-row items-center justify-between gap-6 pb-6 border-b border-[#f8f9fb]">
                <div class="relative w-full max-w-md">
                    <svg class="absolute w-5 h-5 text-gray-300 top-1/2 -translate-y-1/2" :class="cart.language === 'ar' ? 'right-6' : 'left-6'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    <input 
                        v-model="search"
                        type="text" 
                        :placeholder="cart.language === 'ar' ? 'ابحث باسم المنتج أو الكود أو الموديل...' : 'Search by Name, SKU, Node...'"
                        class="input-premium"
                        :class="cart.language === 'ar' ? 'pr-16' : 'pl-16'"
                    >
                </div>
                <Link 
                    :href="route('admin.products.create')"
                    class="w-full md:w-auto btn-red-gradient text-center"
                >
                    + {{ cart.language === 'ar' ? 'إضافة صنف جديد' : 'Deploy New Item' }}
                </Link>
            </div>

            <!-- Asset Registry Grid -->
            <div class="bg-white rounded-[32px] border border-[#ececf2] shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-[#f8f9fb] border-b border-[#ececf2] text-[10px] font-black uppercase tracking-[0.1em] text-gray-400">
                                <th class="px-10 py-8">{{ cart.language === 'ar' ? 'بيانات المنتج' : 'Item Signature' }}</th>
                                <th class="px-10 py-8 text-center">{{ cart.language === 'ar' ? 'التصنيف' : 'Category Node' }}</th>
                                <th class="px-10 py-8 text-center">{{ cart.language === 'ar' ? 'السعر الفعلي' : 'Effective Price' }}</th>
                                <th class="px-10 py-8 text-center">{{ cart.language === 'ar' ? 'مستوى المخزون' : 'Current Stock' }}</th>
                                <th class="px-10 py-8 text-center">{{ cart.t('action') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#f8f9fb]">
                            <tr v-for="p in products.data" :key="p.id" class="group hover:bg-[#ff2e63]/[0.02] transition-all">
                                <td class="px-10 py-8">
                                    <div class="flex items-center gap-6">
                                        <div class="w-16 h-16 bg-[#f8f9fb] rounded-[18px] overflow-hidden group-hover:scale-105 transition-transform shadow-inner border border-[#ececf2]">
                                            <img v-if="p.image_url" :src="p.image_url" class="w-full h-full object-cover">
                                            <div v-else class="w-full h-full flex items-center justify-center text-xs font-black text-gray-300">{{ p.name.charAt(0) }}</div>
                                        </div>
                                        <div>
                                            <p class="text-base font-black text-[#1f1f1f] tracking-tighter">{{ p.name }}</p>
                                            <div class="flex items-center gap-2 mt-1">
                                                <p class="text-[9px] text-gray-400 font-black uppercase tracking-widest">{{ p.sku || 'NO SKU' }}</p>
                                                <span v-if="p.is_on_sale" class="bg-red-50 text-[#ff2e63] text-[8px] font-black px-2 py-0.5 rounded-full uppercase">SALE</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-10 py-8 text-center">
                                    <span class="px-4 py-2 rounded-full bg-brand-50 text-brand-500 text-[9px] font-black uppercase tracking-widest shadow-sm">
                                        {{ p.category?.name || '---' }}
                                    </span>
                                </td>
                                <td class="px-10 py-8 text-center">
                                    <div class="flex flex-col items-center">
                                        <span class="text-base font-black text-[#1f1f1f] tracking-tighter">{{ formatCurrency(p.effective_price) }}</span>
                                        <span v-if="p.is_on_sale" class="text-[9px] text-gray-400 line-through font-bold">{{ formatCurrency(p.price) }}</span>
                                    </div>
                                </td>
                                <td class="px-10 py-8 text-center">
                                    <div class="flex flex-col items-center gap-2">
                                        <span class="text-sm font-black tracking-tighter" :class="p.stock_quantity <= p.min_stock_alert ? 'text-[#ff2e63]' : 'text-emerald-500'">{{ p.stock_quantity }} {{ cart.language === 'ar' ? 'قطعة' : 'Units' }}</span>
                                        <div class="w-20 h-1.5 bg-[#f8f9fb] rounded-full overflow-hidden shadow-inner">
                                            <div class="h-full transition-all duration-700 ease-out" :class="p.stock_quantity <= p.min_stock_alert ? 'bg-[#ff2e63]' : 'bg-emerald-500'" :style="{width: Math.min((p.stock_quantity / (p.min_stock_alert * 3)) * 100, 100) + '%'}"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-10 py-8">
                                    <div class="flex items-center justify-center gap-3">
                                        <Link :href="route('admin.products.edit', p.id)" class="p-3 bg-white border border-[#ececf2] rounded-[14px] text-gray-400 hover:text-brand-500 hover:border-brand-500/30 hover:shadow-md transition-all">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                        </Link>
                                        <button @click="deleteProduct(p.id)" class="p-3 bg-white border border-[#ececf2] rounded-[14px] text-gray-400 hover:text-[#ff2e63] hover:border-[#ff2e63]/30 hover:shadow-md transition-all">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="products.data.length === 0">
                                <td colspan="5" class="py-40 text-center text-gray-300">
                                    <div class="text-5xl mb-6 grayscale opacity-20">📦</div>
                                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">{{ cart.language === 'ar' ? 'لا توجد منتجات مطابقة للبحث' : 'No assets identified in this registry' }}</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination Horizon -->
            <div class="flex justify-center pt-10">
                <div v-if="products.links && products.links.length > 3" class="flex items-center gap-2 p-2 bg-white rounded-[20px] border border-[#ececf2] shadow-sm">
                    <button 
                        v-for="link in products.links" 
                        :key="link.label"
                        @click="link.url && router.visit(link.url)"
                        v-html="link.label"
                        :disabled="!link.url || link.active"
                        class="px-5 py-3 rounded-[14px] text-[11px] font-black tracking-widest uppercase transition-all duration-300"
                        :class="[
                            link.active ? 'bg-brand-500 text-white shadow-lg shadow-brand-500/20 scale-105' : 'text-gray-400 hover:text-brand-500 hover:bg-brand-50',
                            !link.url && 'opacity-20'
                        ]"
                    ></button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
