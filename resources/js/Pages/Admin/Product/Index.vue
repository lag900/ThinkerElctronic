<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { cart } from '@/Stores/CartStore';

const props = defineProps({
    products: Array
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

const deleteProduct = (id) => {
    if (confirm('Verify protocol shutdown? This will terminate the product node.')) {
        router.delete(route('admin.products.destroy', id));
    }
};
</script>

<template>
    <Head title="Hardware Catalog" />

    <AdminLayout>
        <template #header>{{ cart.language === 'ar' ? 'كتالوج المنتجات' : 'Hardware Catalog Matrix' }}</template>

        <div class="space-y-12">
            <!-- Tactical Controls -->
            <div class="flex justify-between items-center bg-white p-10 rounded-[50px] border border-slate-50 shadow-[0_20px_60px_rgba(0,0,0,0.02)]">
                <div>
                    <h3 class="text-3xl font-black text-[#222] tracking-tighter">{{ cart.language === 'ar' ? 'المنتجات النشطة' : 'Active Hardware Nodes' }}</h3>
                    <p class="text-[10px] font-black text-slate-300 uppercase tracking-[0.4em] mt-2">Inventory Deployment Systems</p>
                </div>
                <Link :href="route('admin.products.create')" class="bg-[#222] hover:bg-brand-500 text-white px-8 py-4 rounded-[28px] font-black uppercase tracking-widest text-[11px] transition-all shadow-xl shadow-black/10 active:scale-95">
                    + {{ cart.language === 'ar' ? 'إضافة منتج' : 'Deploy New Node' }}
                </Link>
            </div>

            <!-- Product Data Stream -->
            <div class="bg-white rounded-[50px] border border-slate-50 shadow-[0_40px_80px_rgba(0,0,0,0.02)] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full" :class="{'text-right': cart.language === 'ar', 'text-left': cart.language !== 'ar'}">
                        <thead>
                            <tr class="bg-slate-50/50 text-[10px] font-black uppercase tracking-[0.3em] text-slate-300">
                                <th class="px-10 py-10">{{ cart.language === 'ar' ? 'المنتج' : 'Hardware Node' }}</th>
                                <th class="px-10 py-10">{{ cart.language === 'ar' ? 'التصنيف' : 'Classification' }}</th>
                                <th class="px-10 py-10">{{ cart.language === 'ar' ? 'السعر' : 'Unit Value' }}</th>
                                <th class="px-10 py-10">{{ cart.language === 'ar' ? 'المخزون' : 'Stock Availability' }}</th>
                                <th class="px-10 py-10" :class="cart.language === 'ar' ? 'text-left' : 'text-right'">Protocols</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <tr v-for="product in products.data" :key="product.id" class="group hover:bg-slate-50/30 transition-all duration-500">
                                <td class="px-10 py-8">
                                    <div class="flex items-center gap-6">
                                        <div class="w-20 h-20 bg-white border border-slate-100 rounded-[24px] overflow-hidden shrink-0 shadow-sm group-hover:border-brand-500/20 transition-all">
                                            <img :src="product.image_url" class="w-full h-full object-cover group-hover:scale-110 transition-all duration-700">
                                        </div>
                                        <div class="overflow-hidden">
                                            <h4 class="text-[15px] font-black text-[#222] truncate">{{ cart.language === 'ar' ? (product.name_ar || product.name) : product.name }}</h4>
                                            <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest mt-1">ID: #{{ product.id }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-10 py-8">
                                    <span class="inline-flex px-4 py-1.5 bg-slate-50 rounded-full text-[10px] font-black uppercase tracking-widest text-[#222] border border-slate-100 group-hover:bg-white transition-all">
                                        {{ product.category?.name }}
                                    </span>
                                </td>
                                <td class="px-10 py-8">
                                    <p class="text-sm font-black text-brand-500">{{ formatCurrency(product.price) }}</p>
                                    <p v-if="product.cost_price" class="text-[9px] font-bold text-slate-300 uppercase tracking-tighter mt-1">Cost: {{ formatCurrency(product.cost_price) }}</p>
                                </td>
                                <td class="px-10 py-8">
                                    <div class="flex items-center gap-2">
                                        <div class="w-1.5 h-1.5 rounded-full" :class="product.stock_quantity > 5 ? 'bg-emerald-500' : (product.stock_quantity > 0 ? 'bg-amber-400' : 'bg-red-500')"></div>
                                        <span class="text-xs font-black text-[#222] uppercase">{{ formatNumber(product.stock_quantity) }}</span>
                                    </div>
                                    <p class="text-[9px] font-black uppercase text-slate-300 tracking-widest mt-1">Units Synced</p>
                                </td>
                                <td class="px-10 py-8" :class="cart.language === 'ar' ? 'text-left' : 'text-right'">
                                    <div class="flex gap-3 justify-end">
                                        <Link :href="route('admin.products.edit', product.id)" class="p-4 bg-white border border-slate-100 hover:border-brand-500/30 rounded-2xl text-slate-400 hover:text-brand-500 transition-all shadow-sm">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                        </Link>
                                        <button @click="deleteProduct(product.id)" class="p-4 bg-white border border-slate-100 hover:border-red-500/30 rounded-2xl text-slate-400 hover:text-red-500 transition-all shadow-sm">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="products.links.length > 3" class="px-10 py-10 bg-slate-50/50 flex items-center justify-center gap-2 border-t border-slate-50">
                    <Link 
                        v-for="link in products.links" 
                        :key="link.label"
                        :href="link.url || '#'"
                        class="px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all"
                        :class="[
                            link.active ? 'bg-brand-500 text-white shadow-lg shadow-brand-500/20' : 'bg-white text-slate-400 border border-slate-100 hover:bg-slate-50',
                            !link.url && 'opacity-30 cursor-not-allowed'
                        ]"
                        v-html="link.label"
                    />
                </div>

                <!-- Empty State -->
                <div v-if="products.data.length === 0" class="p-40 text-center">
                    <div class="w-24 h-24 bg-slate-50 rounded-[40px] flex items-center justify-center mx-auto mb-10 text-slate-200">
                         <svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                    </div>
                    <h5 class="text-xl font-black text-[#222] tracking-tighter uppercase">No hardware detected</h5>
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-300 mt-2">Initialize new node deployment to populate matrix.</p>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
