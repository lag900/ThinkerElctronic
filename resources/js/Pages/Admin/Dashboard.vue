<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { cart } from '@/Stores/CartStore';

const props = defineProps({
    stats: Object,
    analytics: Array,
    topProducts: Array,
    categories: Array
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

const statCards = computed(() => [
    { label: 'salesToday', value: formatCurrency(props.stats?.total_sales_today || 0), sub: '+12.5%', color: 'brand' },
    { label: 'revenue', value: formatCurrency(props.stats?.monthly_revenue || 0), sub: 'Goal: 100k', color: 'slate' },
    { label: 'profit', value: formatCurrency(props.stats?.net_profit || 0), sub: '25% margin', color: 'slate' },
    { label: 'orders', value: formatNumber(props.stats?.orders_count || 0), sub: 'Processed', color: 'slate' },
    { label: 'customers', value: formatNumber(props.stats?.new_customers || 0), sub: 'Retention: 85%', color: 'slate' },
]);

// Category Modal Logic
const showCategoryModal = ref(false);
const editingCategory = ref(null);
const form = useForm({
    name: '',
    image_file: null,
    show_on_homepage: false,
});

const openCategoryModal = (cat = null) => {
    editingCategory.value = cat;
    if (cat) {
        form.name = cat.name;
        form.show_on_homepage = !!cat.show_on_homepage;
    } else {
        form.reset();
    }
    showCategoryModal.value = true;
};

const submitCategory = () => {
    if (editingCategory.value) {
        form.post(route('admin.categories.update', editingCategory.value.id), {
            onSuccess: () => {
                showCategoryModal.value = false;
                form.reset();
            }
        });
    } else {
        form.post(route('admin.categories.store'), {
            onSuccess: () => {
                showCategoryModal.value = false;
                form.reset();
            }
        });
    }
};

const deleteCategory = (id) => {
    if (confirm('Verify protocol execution? This will remove the category node.')) {
        router.delete(route('admin.categories.destroy', id));
    }
};

const handleImageUpload = (e) => {
    form.image_file = e.target.files[0];
};
</script>

<template>
    <Head :title="cart.t('welcome')" />

    <AdminLayout>
        <template #header>{{ cart.language === 'ar' ? 'نظرة عامة على النظام' : 'Intelligence Protocol' }}</template>

        <div class="space-y-12">
            <!-- Urgent Status Bar -->
            <div v-if="stats.out_of_stock_products > 0 || stats.low_stock_products > 0" class="flex flex-col xl:flex-row gap-6">
                <!-- Redesign slightly to match the ultra-clean style -->
                <div v-if="stats.out_of_stock_products > 0" class="flex-1 bg-white p-6 rounded-[32px] border border-red-50 flex items-center gap-6 shadow-sm shadow-red-500/5">
                    <div class="w-14 h-14 bg-red-500 rounded-2xl flex items-center justify-center text-white shadow-xl shadow-red-500/20">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-red-500 uppercase tracking-widest">{{ cart.language === 'ar' ? 'انهيار المخزون' : 'Resource Depletion' }}</p>
                        <p class="text-lg font-black text-[#222] mt-0.5">{{ formatNumber(stats.out_of_stock_products) }} {{ cart.language === 'ar' ? 'أصناف غير متوفرة' : 'Out of Stock' }}</p>
                    </div>
                </div>
                <!-- ... Same for Low Stock ... -->
            </div>

            <!-- Modern Stats Horizon -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                <div v-for="card in statCards" :key="card.label" 
                     class="bg-white p-8 rounded-[40px] border border-slate-100 shadow-[0_10px_30px_rgba(0,0,0,0.02)] hover:shadow-[0_20px_40px_rgba(0,0,0,0.05)] hover:-translate-y-2 transition-all duration-500 group">
                    <div class="flex items-center justify-between mb-8">
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">{{ cart.t(card.label) }}</p>
                        <div class="w-2 h-2 rounded-full" :class="card.color === 'brand' ? 'bg-brand-500 animate-pulse' : 'bg-slate-200'"></div>
                    </div>
                    <h3 class="text-3xl font-black text-[#222] tracking-tighter mb-2">{{ card.value }}</h3>
                    <div class="flex items-center gap-2">
                        <span class="text-[11px] font-black" :class="card.color === 'brand' ? 'text-brand-500' : 'text-emerald-500'">{{ card.sub }}</span>
                        <svg class="w-3 h-3" :class="card.color === 'brand' ? 'text-brand-500' : 'text-emerald-500'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 10l7-7 7 7" /></svg>
                    </div>
                </div>
            </div>

            <!-- Connected Classification Nodes Section -->
            <div class="space-y-6">
                <div class="flex items-center justify-between mb-8 px-4">
                    <div>
                        <h4 class="text-2xl font-black text-[#222] tracking-tighter">{{ cart.language === 'ar' ? 'تصنيفات النظام' : 'Classification Nodes' }}</h4>
                        <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest mt-1">Direct Database Integration</p>
                    </div>
                    <button @click="openCategoryModal()" class="px-6 py-3 bg-[#222] text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-brand-500 transition-all shadow-xl shadow-black/5 active:scale-95">
                        + {{ cart.language === 'ar' ? 'تصنيف جديد' : 'New Category' }}
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div v-for="cat in categories" :key="cat.id" class="group bg-white p-6 rounded-[40px] border border-slate-100 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 relative overflow-hidden">
                        <div class="flex items-center gap-5 mb-6">
                            <div class="w-16 h-16 bg-slate-50 rounded-[20px] overflow-hidden border border-slate-100 shrink-0">
                                <img v-if="cat.image" :src="cat.image" class="w-full h-full object-cover">
                                <div v-else class="w-full h-full flex items-center justify-center text-slate-200">
                                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                </div>
                            </div>
                            <div class="overflow-hidden">
                                <h5 class="text-[15px] font-black text-[#222] truncate">{{ cat.name }}</h5>
                                <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest mt-0.5">{{ formatNumber(cat.products_count || 0) }} Products</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-6 border-t border-slate-50">
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full" :class="cat.show_on_homepage ? 'bg-emerald-500' : 'bg-slate-200'"></div>
                                <span class="text-[9px] font-black uppercase text-slate-400 tracking-widest">{{ cat.show_on_homepage ? 'Revealed' : 'Cloaked' }}</span>
                            </div>
                            <div class="flex gap-2">
                                <button @click="openCategoryModal(cat)" class="p-2.5 bg-slate-50 rounded-xl text-slate-400 hover:text-brand-500 hover:bg-brand-50 transition-all">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                </button>
                                <button @click="deleteCategory(cat.id)" class="p-2.5 bg-slate-50 rounded-xl text-slate-400 hover:text-red-500 hover:bg-red-50 transition-all">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Central Matrix (Chart & Top Products) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                <!-- Chart Area -->
                <div class="lg:col-span-8 bg-white p-10 rounded-[50px] border border-slate-100 shadow-[0_20px_50px_rgba(0,0,0,0.02)]">
                    <!-- ... Chart Content ... -->
                </div>
                <!-- ... Top Products ... -->
            </div>
        </div>

        <!-- Professional Category Modal -->
        <div v-if="showCategoryModal" class="fixed inset-0 z-[100] flex items-center justify-center p-6 backdrop-blur-md bg-white/40">
            <div class="bg-white w-full max-w-lg rounded-[50px] shadow-[0_40px_100px_rgba(0,0,0,0.1)] border border-slate-50 p-12 relative animate-in zoom-in duration-300">
                <button @click="showCategoryModal = false" class="absolute top-10 right-10 p-3 bg-slate-50 rounded-2xl text-slate-400 hover:text-brand-500 transition-all">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>

                <h3 class="text-3xl font-black text-[#222] tracking-tighter mb-2">{{ editingCategory ? 'Modify Node' : 'Initialize Node' }}</h3>
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-300 mb-10">Category Transmission Protocol</p>

                <form @submit.prevent="submitCategory" class="space-y-8">
                    <div>
                        <label class="text-[9px] font-black uppercase tracking-[0.3em] text-slate-400 block mb-3">Classification Title</label>
                        <input v-model="form.name" type="text" class="w-full bg-slate-50 border-none rounded-2xl py-4 px-6 text-sm font-bold focus:ring-2 focus:ring-brand-500 transition-all" required>
                    </div>

                    <div>
                        <label class="text-[9px] font-black uppercase tracking-[0.3em] text-slate-400 block mb-3">Visual representation</label>
                        <div class="flex items-center gap-6">
                            <div class="w-20 h-20 bg-slate-50 rounded-[28px] border-2 border-dashed border-slate-200 flex items-center justify-center text-slate-300 overflow-hidden relative group">
                                <input type="file" @change="handleImageUpload" class="absolute inset-0 opacity-0 cursor-pointer z-10">
                                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            </div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 line-clamp-1 flex-1">{{ form.image_file ? form.image_file.name : 'Select JPG/PNG' }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 bg-slate-50 p-6 rounded-3xl cursor-pointer" @click="form.show_on_homepage = !form.show_on_homepage">
                        <div class="w-10 h-6 rounded-full p-1 transition-all" :class="form.show_on_homepage ? 'bg-brand-500' : 'bg-slate-200'">
                            <div class="w-4 h-4 bg-white rounded-full shadow-sm transition-all" :class="form.show_on_homepage ? (cart.language === 'ar' ? '-translate-x-4' : 'translate-x-4') : 'translate-x-0'"></div>
                        </div>
                        <span class="text-[11px] font-black uppercase tracking-widest text-[#222]">Show in Welcome Dashboard</span>
                    </div>

                    <button type="submit" class="w-full py-5 bg-[#222] text-white rounded-[28px] font-black uppercase tracking-widest text-[11px] shadow-2xl shadow-black/20 hover:bg-brand-500 transition-all active:scale-95 disabled:opacity-50" :disabled="form.processing">
                        {{ editingCategory ? 'Update Protocol' : 'Execute Creation' }}
                    </button>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
