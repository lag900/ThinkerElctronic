<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { cart } from '@/Stores/CartStore';
import { system } from '@/Stores/SystemStore';
import { ref } from 'vue';

const props = defineProps({
    products: Array,
    batches: Object, // Paginated
});

const showEntryForm = ref(false);

const form = useForm({
    product_id: '',
    quantity: '',
    cost_price: '',
    supplier: '',
    invoice_number: '',
});

const submit = () => {
    form.post(route('admin.purchases.store'), {
        onSuccess: () => {
            showEntryForm.value = false;
            form.reset();
            system.notify(cart.language === 'ar' ? 'تم تسجيل توريد بضاعة جديد بنجاح' : 'Inventory batch successful');
        },
    });
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat(cart.language === 'ar' ? 'ar-EG' : 'en-US', {
        style: 'currency',
        currency: 'EGP',
    }).format(value);
};
</script>

<template>
    <Head :title="cart.language === 'ar' ? 'التوريدات والمخزون FIFO' : 'Purchases & FIFO Batches'" />

    <AdminLayout>
        <template #header>{{ cart.language === 'ar' ? 'إدارة التوريدات وحسابات التكلفة FIFO' : 'Inventory Strategic Sourcing & FIFO Control' }}</template>

        <div class="space-y-10 font-inter" :dir="cart.language === 'ar' ? 'rtl' : 'ltr'">
            <!-- Command Hub -->
            <div class="flex flex-col md:flex-row justify-between items-center bg-white p-10 rounded-[24px] border border-gray-100 shadow-sm gap-8 transition-all hover:shadow-md">
                <div :class="cart.language === 'ar' ? 'text-right' : 'text-left'">
                    <h3 class="text-3xl font-black text-gray-900 tracking-tighter uppercase">{{ cart.language === 'ar' ? 'سجل توريدات المخزون' : 'Stock Batch Repository' }}</h3>
                    <p class="text-[10px] font-black text-gray-400 mt-2 uppercase tracking-widest">{{ cart.language === 'ar' ? 'نظام المحاسبة المتقدم: الوارد أولاً يصرف أولاً (FIFO)' : 'Enterprise-Grade FIFO Accounting Protocol Active' }}</p>
                </div>
                <button 
                    @click="showEntryForm = true"
                    class="bg-gray-900 text-white px-12 py-5 rounded-[16px] font-black text-xs uppercase tracking-[0.2em] shadow-2xl shadow-gray-900/10 hover:bg-gray-800 transition-all active:scale-95"
                >
                    + {{ cart.language === 'ar' ? 'تسجيل توريد بضاعة' : 'Induct Stock Entry' }}
                </button>
            </div>

            <!-- Batch Registry -->
            <div class="bg-white rounded-[28px] border border-gray-100 shadow-sm overflow-hidden">
                <table class="w-full text-left" :class="cart.language === 'ar' ? 'text-right' : 'text-left'">
                    <thead>
                        <tr class="bg-gray-50/50 border-b border-gray-100">
                            <th class="px-8 py-6 text-[10px] font-black uppercase text-gray-400 tracking-widest">{{ cart.language === 'ar' ? 'المنتج' : 'Strategic Asset' }}</th>
                            <th class="px-8 py-6 text-[10px] font-black uppercase text-gray-400 tracking-widest">{{ cart.language === 'ar' ? 'الكمية الأصلية' : 'Initial Qty' }}</th>
                            <th class="px-8 py-6 text-[10px] font-black uppercase text-gray-400 tracking-widest">{{ cart.language === 'ar' ? 'المتبقي' : 'Available' }}</th>
                            <th class="px-8 py-6 text-[10px] font-black uppercase text-gray-400 tracking-widest">{{ cart.language === 'ar' ? 'تكلفة الوحدة' : 'Unit Cost' }}</th>
                            <th class="px-8 py-6 text-[10px] font-black uppercase text-gray-400 tracking-widest">{{ cart.language === 'ar' ? 'التكلفة الإجمالية' : 'Total Valuation' }}</th>
                            <th class="px-8 py-6 text-[10px] font-black uppercase text-gray-400 tracking-widest">{{ cart.language === 'ar' ? 'المورد / الفاتورة' : 'Supplier / Ref' }}</th>
                            <th class="px-8 py-6 text-[10px] font-black uppercase text-gray-400 tracking-widest">{{ cart.language === 'ar' ? 'التاريخ' : 'Time Stamp' }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr v-for="batch in batches.data" :key="batch.id" class="hover:bg-gray-50/80 transition-colors group">
                            <td class="px-8 py-6">
                                <div class="font-black text-gray-900 text-sm">{{ batch.product.name }}</div>
                                <div class="text-[9px] text-gray-400 font-bold uppercase tracking-widest mt-1">ID: #{{ batch.product_id }} Batch #{{ batch.id }}</div>
                            </td>
                            <td class="px-8 py-6 font-black text-gray-600 text-sm">{{ batch.quantity }}</td>
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-3">
                                    <span class="font-black text-sm" :class="batch.remaining_quantity === 0 ? 'text-red-400' : 'text-emerald-600'">{{ batch.remaining_quantity }}</span>
                                    <div class="w-16 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                        <div class="h-full bg-emerald-500 transition-all duration-1000" :style="{ width: (batch.remaining_quantity / batch.quantity * 100) + '%' }"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6 font-black text-gray-900 text-sm">{{ formatCurrency(batch.cost_price) }}</td>
                            <td class="px-8 py-6 font-black text-gray-900 text-sm">{{ formatCurrency(batch.total_cost) }}</td>
                            <td class="px-8 py-6">
                                <span class="text-[10px] font-black text-gray-500 uppercase">{{ batch.supplier || 'N/A' }}</span>
                                <div class="text-[9px] text-gray-400 font-bold mt-1 uppercase">{{ batch.invoice_number || 'Internal Reference' }}</div>
                            </td>
                            <td class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-tighter">{{ new Date(batch.created_at).toLocaleString() }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Modal for Stock Entry -->
            <div v-if="showEntryForm" class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-gray-900/60 backdrop-blur-md">
                <div class="bg-white w-full max-w-2xl rounded-[32px] shadow-2xl border border-gray-100 animate-in zoom-in-95 duration-300">
                    <div class="px-10 py-8 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="text-2xl font-black text-gray-900 tracking-tighter uppercase">{{ cart.language === 'ar' ? 'حقن مخزون جديد (FIFO)' : 'New Batch Induction' }}</h3>
                        <button @click="showEntryForm = false" class="text-gray-400 hover:text-gray-900 transition-colors">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                    
                    <form @submit.prevent="submit" class="p-10 space-y-8">
                        <div class="space-y-6">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black uppercase text-gray-400 tracking-widest px-1">{{ cart.language === 'ar' ? 'اختر المنتج' : 'Target Asset' }}</label>
                                <select v-model="form.product_id" class="w-full bg-gray-50 border-none rounded-[16px] px-6 py-4 text-sm font-black text-gray-900 focus:ring-2 focus:ring-gray-900 shadow-inner appearance-none" required>
                                    <option value="" disabled>{{ cart.language === 'ar' ? 'اختر منتجاً...' : 'Select Strategic Product...' }}</option>
                                    <option v-for="p in products" :key="p.id" :value="p.id">{{ p.name }} (Stock: {{ p.stock_quantity }})</option>
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black uppercase text-gray-400 tracking-widest px-1">{{ cart.language === 'ar' ? 'الكمية الواردة' : 'Quantity In' }}</label>
                                    <input v-model="form.quantity" type="number" step="1" min="1" class="w-full bg-gray-50 border-none rounded-[16px] px-6 py-4 text-sm font-black text-gray-900 focus:ring-2 focus:ring-gray-900 shadow-inner" placeholder="0" required>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black uppercase text-gray-400 tracking-widest px-1">{{ cart.language === 'ar' ? 'تكلفة الوحدة' : 'Landed Unit Cost' }}</label>
                                    <input v-model="form.cost_price" type="number" step="0.01" min="0" class="w-full bg-gray-50 border-none rounded-[16px] px-6 py-4 text-sm font-black text-gray-900 focus:ring-2 focus:ring-gray-900 shadow-inner" placeholder="0.00" required>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black uppercase text-gray-400 tracking-widest px-1">{{ cart.language === 'ar' ? 'المورد' : 'Strategic Supplier' }}</label>
                                    <input v-model="form.supplier" type="text" class="w-full bg-gray-50 border-none rounded-[16px] px-6 py-4 text-sm font-black text-gray-900 focus:ring-2 focus:ring-gray-900 shadow-inner" :placeholder="cart.language === 'ar' ? 'اسم المورد...' : 'Source Logistics Entity...'">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black uppercase text-gray-400 tracking-widest px-1">{{ cart.language === 'ar' ? 'رقم الفاتورة' : 'Ref Invoice #' }}</label>
                                    <input v-model="form.invoice_number" type="text" class="w-full bg-gray-50 border-none rounded-[16px] px-6 py-4 text-sm font-black text-gray-900 focus:ring-2 focus:ring-gray-900 shadow-inner" placeholder="INV-XXXXX">
                                </div>
                            </div>
                        </div>

                        <div class="pt-6 border-t border-gray-50 flex gap-4">
                            <button type="button" @click="showEntryForm = false" class="flex-1 px-8 py-4 font-black text-[10px] uppercase tracking-widest text-gray-400 hover:text-gray-900 transition-all">{{ cart.language === 'ar' ? 'إلغاء' : 'Abort' }}</button>
                            <button 
                                type="submit" 
                                :disabled="form.processing"
                                class="flex-[2] bg-gray-900 text-white py-4 rounded-[16px] font-black text-[10px] uppercase tracking-[0.3em] transition-all shadow-2xl shadow-gray-900/40 hover:bg-emerald-600 hover:scale-[1.02] disabled:opacity-50 active:scale-95"
                            >
                                {{ cart.language === 'ar' ? 'تثبيت التوريد' : 'Commit Batch' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
