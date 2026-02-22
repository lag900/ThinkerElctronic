<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref, watch } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { cart } from '@/Stores/CartStore';

const props = defineProps({
    invoices: Object,
    customers: Array,
    filters: Object
});

const search = ref(props.filters?.search || '');

watch(search, (val) => {
    router.get(route('invoices.index'), { search: val }, {
        preserveState: true,
        replace: true
    });
});

const showAddModal = ref(false);
const form = useForm({
    customer_id: '',
    subtotal: 0,
    shipping: 0,
    status: 'pending',
    amount_paid: 0,
});

const submitInvoice = () => {
    form.post(route('invoices.store'), {
        onSuccess: () => {
            showAddModal.value = false;
            form.reset();
        }
    });
};

const formatCurrency = (val) => {
    return new Intl.NumberFormat(cart.language === 'ar' ? 'ar-EG' : 'en-US', { 
        style: 'currency', 
        currency: 'EGP',
        maximumFractionDigits: 0
    }).format(val || 0);
};

// Payment Update logic
const showUpdateModal = ref(false);
const updatingInvoice = ref(null);
const updateForm = useForm({
    amount_paid: 0,
    status: 'pending',
});

const openUpdateModal = (inv) => {
    updatingInvoice.value = inv;
    updateForm.amount_paid = inv.amount_paid;
    updateForm.status = inv.status;
    showUpdateModal.value = true;
};

const submitUpdate = () => {
    updateForm.put(route('invoices.update', updatingInvoice.value.id), {
        onSuccess: () => {
            showUpdateModal.value = false;
        }
    });
};

</script>

<template>
    <Head :title="cart.language === 'ar' ? 'إدارة الفواتير' : 'Statement Management'" />

    <AdminLayout>
        <template #header>{{ cart.language === 'ar' ? 'إدارة الفواتير والتحصيلات المالية' : 'Financial Statement Auditing' }}</template>

        <div class="space-y-8 font-inter" :dir="cart.language === 'ar' ? 'rtl' : 'ltr'">
            <!-- Operational Commands -->
            <div class="flex flex-col md:flex-row items-center justify-between gap-8 p-10 bg-white rounded-[20px] border border-gray-100 shadow-sm relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-gray-50 rounded-full -mr-16 -mt-16 opacity-50"></div>
                
                <div class="relative w-full max-w-lg z-10">
                    <svg class="absolute w-5 h-5 text-gray-400 top-1/2 -translate-y-1/2" :class="cart.language === 'ar' ? 'right-6' : 'left-6'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    <input 
                        type="text" 
                        v-model="search"
                        class="w-full bg-gray-50 border-none rounded-[16px] py-4.5 px-8 text-sm font-black text-gray-900 shadow-inner focus:ring-2 focus:ring-gray-900 transition-all placeholder-gray-400" 
                        :placeholder="cart.language === 'ar' ? 'البحث عن رقم فاتورة أو اسم عميل...' : 'Scan Statement Archive...'" 
                        :class="cart.language === 'ar' ? 'pr-16 pl-8' : 'pl-16 pr-8'"
                    />
                </div>
                <button 
                    @click="showAddModal = true" 
                    class="w-full md:w-auto px-12 py-4.5 bg-gray-900 text-white rounded-[16px] font-black text-xs uppercase tracking-[0.2em] shadow-2xl shadow-gray-900/10 hover:bg-gray-800 transition-all active:scale-95 relative z-10">
                    + {{ cart.language === 'ar' ? 'إصدار فاتورة جديدة' : 'Initialize Statement' }}
                </button>
            </div>

            <!-- Statement Ledger -->
            <div class="bg-white rounded-[24px] border border-gray-100 shadow-sm overflow-hidden">
                <table class="w-full" :class="cart.language === 'ar' ? 'text-right' : 'text-left'">
                    <thead>
                        <tr class="bg-gray-50/50 border-b border-gray-100 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">
                            <th class="px-10 py-8">{{ cart.language === 'ar' ? 'رقم الفاتورة' : 'Auth Ref' }}</th>
                            <th class="px-10 py-8">{{ cart.language === 'ar' ? 'العميل' : 'Client Profile' }}</th>
                            <th class="px-10 py-8 text-center">{{ cart.language === 'ar' ? 'حالة السداد' : 'Settlement status' }}</th>
                            <th class="px-10 py-8 text-center">{{ cart.language === 'ar' ? 'القيمة الإجمالية' : 'Statement Total' }}</th>
                            <th class="px-10 py-8">{{ cart.language === 'ar' ? 'موقف التحصيل' : 'Liquidity Flow' }}</th>
                            <th class="px-10 py-8" :class="cart.language === 'ar' ? 'text-left' : 'text-right'">{{ cart.language === 'ar' ? 'إدارة' : 'Modify' }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr v-for="inv in invoices.data" :key="inv.id" class="hover:bg-gray-50/30 transition-all group duration-300">
                            <td class="px-10 py-8 text-sm font-black text-gray-900 tracking-tighter">#{{ inv.invoice_number }}</td>
                            <td class="px-10 py-8">
                                <span class="text-sm font-black text-gray-900 tracking-tight">{{ inv.customer?.name || inv.order?.customer?.name || (cart.language === 'ar' ? 'عميل نقدي (مباشر)' : 'Walk-in Transaction') }}</span>
                            </td>
                            <td class="px-10 py-8 text-center">
                                <span class="px-4 py-2 rounded-full text-[10px] font-black uppercase tracking-widest border shadow-sm transition-all"
                                    :class="{
                                        'bg-emerald-50 text-emerald-600 border-emerald-100': inv.status === 'paid',
                                        'bg-amber-50 text-amber-600 border-amber-100': inv.status === 'partial' || inv.status === 'pending',
                                        'bg-red-50 text-red-600 border-red-100': inv.status === 'cancelled'
                                    }">
                                    {{ cart.t(inv.status) }}
                                </span>
                            </td>
                            <td class="px-10 py-8 text-center text-sm font-black text-gray-900 tracking-tighter">{{ formatCurrency(inv.total) }}</td>
                            <td class="px-10 py-8">
                                <div class="flex flex-col gap-1.5">
                                    <div class="flex items-center gap-2">
                                        <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
                                        <span class="text-[11px] font-black text-gray-900 tracking-tight">{{ cart.language === 'ar' ? 'تم تحصيل:' : 'Liquid:' }} {{ formatCurrency(inv.amount_paid) }}</span>
                                    </div>
                                    <div v-if="inv.total - inv.amount_paid > 0" class="flex items-center gap-2">
                                        <div class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></div>
                                        <span class="text-[11px] font-black text-red-500 tracking-tight">{{ cart.language === 'ar' ? 'المتبقي:' : 'Due:' }} {{ formatCurrency(inv.total - inv.amount_paid) }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-10 py-8" :class="cart.language === 'ar' ? 'text-left' : 'text-right'">
                                <div class="flex items-center gap-3" :class="cart.language === 'ar' ? 'justify-start' : 'justify-end'">
                                    <button @click="openUpdateModal(inv)" class="bg-gray-900 text-white px-6 py-2.5 rounded-[12px] text-[10px] font-black uppercase tracking-widest hover:bg-gray-800 transition-all shadow-lg shadow-gray-900/10 active:scale-95">
                                        {{ cart.language === 'ar' ? 'تحديث الدفع' : 'Register Flux' }}
                                    </button>
                                    <a v-if="inv.pdf_path || inv.secure_token" :href="route('admin.invoices.download', inv.secure_token)" target="_blank" class="p-3 bg-white border border-gray-100 rounded-[12px] text-gray-400 hover:text-gray-900 hover:border-gray-900 transition-all shadow-sm active:scale-90">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!invoices.data || invoices.data.length === 0">
                            <td colspan="6" class="text-center py-32">
                                <div class="text-5xl mb-6 grayscale opacity-20">📂</div>
                                <p class="text-gray-300 font-black uppercase tracking-[0.3em] text-[11px]">{{ cart.language === 'ar' ? 'لا توجد فواتير مطابقة لمعايير البحث' : 'Zero Statements Identified' }}</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Statement Initialization Modal -->
        <div v-if="showAddModal" class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-gray-900/60 backdrop-blur-md" :dir="cart.language === 'ar' ? 'rtl' : 'ltr'">
            <div class="bg-white w-full max-w-xl rounded-[28px] shadow-2xl border border-gray-100 p-12 relative animate-in zoom-in-95 duration-500">
                <div class="absolute top-0 left-0 w-full h-2 bg-gray-900"></div>
                <button @click="showAddModal = false" class="absolute top-8 left-8 p-3 text-gray-400 hover:text-gray-900 bg-gray-50 rounded-full hover:rotate-90 transition-all">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
                <h3 class="text-3xl font-black text-gray-900 tracking-tighter mb-2">{{ cart.language === 'ar' ? 'حقن فاتورة يدوية جديدة' : 'Initialize Manual Statement' }}</h3>
                <p class="text-[11px] font-black uppercase tracking-[0.2em] text-gray-400 mb-10">{{ cart.language === 'ar' ? 'تسجيل حركة مبيعات مباشرة وتخصيصها لعميل مستهدف' : 'Record a direct trade flux and allocate to client' }}</p>
                
                <form @submit.prevent="submitInvoice" class="space-y-8">
                    <div class="space-y-2">
                        <label class="text-[11px] font-black uppercase tracking-widest text-gray-500 px-1 block">{{ cart.language === 'ar' ? 'العميل المستهدف' : 'Allocated Client Entity' }}</label>
                        <select v-model="form.customer_id" class="w-full bg-gray-50 border-none rounded-[16px] py-5 px-8 text-sm font-black text-gray-900 shadow-inner focus:ring-2 focus:ring-gray-900 appearance-none cursor-pointer transition-all" required>
                            <option value="">{{ cart.language === 'ar' ? '--- اختر اسم العميل من القائمة ---' : '--- Select Client Registry ---' }}</option>
                            <option v-for="c in customers" :key="c.id" :value="c.id">{{ c.name }}</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-2">
                            <label class="text-[11px] font-black uppercase tracking-widest text-gray-500 px-1 block">{{ cart.language === 'ar' ? 'الإجمالي الفرعي' : 'Asset Subtotal' }}</label>
                            <input v-model="form.subtotal" type="number" step="0.01" class="w-full bg-gray-50 border-none rounded-[16px] py-5 px-8 text-sm font-black text-gray-900 shadow-inner focus:ring-2 focus:ring-gray-900 transition-all" placeholder="0.00" required>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[11px] font-black uppercase tracking-widest text-gray-500 px-1 block">{{ cart.language === 'ar' ? 'مصاريف اللوجستيات' : 'Logistic Surcharge' }}</label>
                            <input v-model="form.shipping" type="number" step="0.01" class="w-full bg-gray-50 border-none rounded-[16px] py-5 px-8 text-sm font-black text-gray-900 shadow-inner focus:ring-2 focus:ring-gray-900 transition-all" placeholder="0.00">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-2">
                            <label class="text-[11px] font-black uppercase tracking-widest text-gray-500 px-1 block">{{ cart.language === 'ar' ? 'المدفوع فوراً' : 'Instant Liquidity' }}</label>
                            <input v-model="form.amount_paid" type="number" step="0.01" class="w-full bg-emerald-50 border-none rounded-[16px] py-5 px-8 text-sm font-black text-emerald-600 shadow-inner focus:ring-2 focus:ring-emerald-500/20 transition-all" placeholder="0.00" required>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[11px] font-black uppercase tracking-widest text-gray-500 px-1 block">{{ cart.language === 'ar' ? 'الحالة الائتمانية' : 'Settlement Strategy' }}</label>
                            <select v-model="form.status" class="w-full bg-gray-50 border-none rounded-[16px] py-5 px-8 text-sm font-black text-gray-900 shadow-inner focus:ring-2 focus:ring-gray-900 appearance-none cursor-pointer transition-all" required>
                                <option value="pending">{{ cart.language === 'ar' ? 'قيد الانتظار' : 'Pending Logic' }}</option>
                                <option value="partial">{{ cart.language === 'ar' ? 'سداد جزئي' : 'Partial Flux' }}</option>
                                <option value="paid">{{ cart.language === 'ar' ? 'تمت التسوية' : 'Settled' }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="pt-4">
                        <div class="flex items-center justify-between mb-8 p-8 bg-gray-900 rounded-[20px] shadow-2xl">
                            <span class="text-[11px] font-black uppercase tracking-[0.3em] text-white/40">{{ cart.language === 'ar' ? 'صافي قيمة الفاتورة' : 'Aggregated Net' }}</span>
                            <span class="text-3xl font-black text-white tracking-tighter">{{ formatCurrency(Number(form.subtotal) + Number(form.shipping)) }}</span>
                        </div>
                        <button type="submit" class="w-full py-6 bg-gray-900 text-white rounded-[20px] font-black uppercase tracking-[0.3em] text-xs hover:bg-gray-800 transition-all active:scale-95 shadow-2xl shadow-gray-900/40" :disabled="form.processing">
                            {{ form.processing ? (cart.language === 'ar' ? 'جاري التنفيذ...' : 'Persisting...') : (cart.language === 'ar' ? 'تثبيت الفاتورة وإدراجها' : 'Deploy Statement') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Statement Flux Modification Modal -->
        <div v-if="showUpdateModal" class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-gray-900/60 backdrop-blur-md" :dir="cart.language === 'ar' ? 'rtl' : 'ltr'">
            <div class="bg-white w-full max-w-xl rounded-[28px] shadow-2xl border border-gray-100 p-12 relative animate-in zoom-in-95 duration-500">
                <div class="absolute top-0 left-0 w-full h-2 bg-emerald-500"></div>
                <button @click="showUpdateModal = false" class="absolute top-8 left-8 p-3 text-gray-400 hover:text-gray-900 bg-gray-50 rounded-full hover:rotate-90 transition-all">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
                <h3 class="text-3xl font-black text-gray-900 tracking-tighter mb-2">{{ cart.language === 'ar' ? 'تحديث السيولة النقدية' : 'Synchronize Statement Flux' }}</h3>
                <p class="text-[11px] font-black uppercase tracking-[0.2em] text-gray-400 mb-10">#{{ updatingInvoice?.invoice_number }} — {{ cart.language === 'ar' ? 'المستحق الكلي:' : 'Full Statement:' }} {{ formatCurrency(updatingInvoice?.total) }}</p>

                <form @submit.prevent="submitUpdate" class="space-y-8">
                    <div class="space-y-2">
                        <label class="text-[11px] font-black uppercase tracking-widest text-gray-500 px-1 block">{{ cart.language === 'ar' ? 'إجمالي المبالغ المحصلة فعلياً' : 'Aggregate Liquid Collected' }}</label>
                        <input v-model="updateForm.amount_paid" type="number" step="0.01" class="w-full bg-emerald-50 border-none rounded-[16px] py-6 px-8 text-xl font-black text-emerald-600 shadow-inner focus:ring-4 focus:ring-emerald-500/10 transition-all" required>
                        <div v-if="(updatingInvoice?.total || 0) - updateForm.amount_paid > 0" class="flex items-center gap-3 mt-4 p-4 bg-red-50 rounded-[12px] border border-red-100">
                            <div class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></div>
                            <span class="text-[11px] font-black text-red-500 uppercase tracking-widest">{{ cart.language === 'ar' ? 'صافي المديونية المتبقية:' : 'Exposure Remaining:' }} {{ formatCurrency((updatingInvoice?.total || 0) - updateForm.amount_paid) }}</span>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[11px] font-black uppercase tracking-widest text-gray-500 px-1 block">{{ cart.language === 'ar' ? 'الحالة التنظيمية للسداد' : 'Settlement Protocol' }}</label>
                        <select v-model="updateForm.status" class="w-full bg-gray-50 border-none rounded-[16px] py-5 px-8 text-sm font-black text-gray-900 shadow-inner focus:ring-2 focus:ring-gray-900 transition-all outline-none cursor-pointer appearance-none" required>
                            <option value="pending">{{ cart.language === 'ar' ? 'قيد الانتظار' : 'Pending' }}</option>
                            <option value="partial">{{ cart.language === 'ar' ? 'سداد جزئي' : 'Partial' }}</option>
                            <option value="paid">{{ cart.language === 'ar' ? 'تم التحصيل بالكامل' : 'Fully Settled' }}</option>
                            <option value="cancelled">{{ cart.language === 'ar' ? 'إلغاء المعاملة' : 'Abort Transaction' }}</option>
                        </select>
                    </div>

                    <button type="submit" class="w-full py-6 bg-gray-900 text-white rounded-[20px] font-black uppercase tracking-[0.3em] text-xs hover:bg-emerald-600 transition-all active:scale-95 shadow-2xl shadow-gray-900/30" :disabled="updateForm.processing">
                        {{ updateForm.processing ? (cart.language === 'ar' ? 'جاري المزامنة...' : 'Syncing...') : (cart.language === 'ar' ? 'تثبيت التحديثات الائتمانية' : 'Commit Fiscal Protocol') }}
                    </button>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
