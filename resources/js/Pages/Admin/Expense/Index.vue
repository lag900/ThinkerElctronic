<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { cart } from '@/Stores/CartStore';
import { system } from '@/Stores/SystemStore';

const props = defineProps({
    expenses: Array,
    total_expenses: Number,
});

const showModal = ref(false);
const editingExpense = ref(null);
const filterTab = ref('This Month');

const form = useForm({
    title: '',
    amount: '',
    category: 'Operational',
    date: new Date().toISOString().split('T')[0],
    notes: '',
});

const openCreateModal = () => {
    editingExpense.value = null;
    form.reset();
    form.date = new Date().toISOString().split('T')[0];
    showModal.value = true;
};

const openEditModal = (expense) => {
    editingExpense.value = expense;
    form.title = expense.title;
    form.amount = expense.amount;
    form.category = expense.category;
    form.date = expense.date;
    form.notes = expense.notes;
    showModal.value = true;
};

const submit = () => {
    if (editingExpense.value) {
        form.put(route('admin.expenses.update', editingExpense.value.id), {
            onSuccess: () => {
                showModal.value = false;
                system.notify(cart.language === 'ar' ? 'تم تحديث البيانات' : 'Record updated');
            },
        });
    } else {
        form.post(route('admin.expenses.store'), {
            onSuccess: () => {
                showModal.value = false;
                system.notify(cart.language === 'ar' ? 'تم تسجيل المصروف' : 'Expense recorded');
            },
        });
    }
};

const deleteExpense = (id) => {
    if (confirm(cart.language === 'ar' ? 'حذف هذا المصروف؟' : 'Delete this record?')) {
        router.delete(route('admin.expenses.destroy', id), {
            onSuccess: () => system.notify(cart.language === 'ar' ? 'تم الحذف' : 'Deleted', 'error'),
        });
    }
};

const categories = [
    { key: 'Operational', ar: 'تشغيلي', en: 'Operational', icon: '⚙️' },
    { key: 'Maintenance', ar: 'صيانة', en: 'Maintenance', icon: '🧰' },
    { key: 'Rent', ar: 'إيجار', en: 'Rent', icon: '🏢' },
    { key: 'Utilities', ar: 'مرافق', en: 'Utilities', icon: '💧' },
    { key: 'Salaries', ar: 'رواتب', en: 'Salaries', icon: '👨‍💼' },
    { key: 'Ads', ar: 'إعلانات', en: 'Ads', icon: '📢' },
    { key: 'Shipping', ar: 'شحن', en: 'Shipping', icon: '🚚' },
    { key: 'Others', ar: 'أخرى', en: 'Others', icon: '📦' }
];

const formatCurrency = (val) => {
    return new Intl.NumberFormat(cart.language === 'ar' ? 'ar-EG' : 'en-US', { 
        style: 'currency', 
        currency: 'EGP',
        maximumFractionDigits: 0
    }).format(val || 0);
};

const stats = computed(() => {
    const todayStr = new Date().toISOString().split('T')[0];
    const todayExpenses = props.expenses.filter(e => e.date === todayStr).reduce((acc, curr) => acc + parseFloat(curr.amount), 0);
    
    // Most expensive category
    const catMap = {};
    props.expenses.forEach(e => {
        catMap[e.category] = (catMap[e.category] || 0) + parseFloat(e.amount);
    });
    let maxCat = '';
    let maxVal = 0;
    Object.keys(catMap).forEach(k => {
        if (catMap[k] > maxVal) { maxVal = catMap[k]; maxCat = k; }
    });

    return {
        today: todayExpenses,
        month: props.total_expenses, // Assuming props.total_expenses is for the month or total
        biggestCategory: categories.find(c => c.key === maxCat) || categories[0]
    };
});
</script>

<template>
    <Head :title="cart.language === 'ar' ? 'المصروفات' : 'Expense Control'" />

    <AdminLayout>
        <template #header>{{ cart.language === 'ar' ? 'الإدارة المالية والمصروفات' : 'Financial & Expense Management' }}</template>

        <div class="space-y-8 font-inter bg-[#fdfdfd] -m-12 p-12 min-h-screen" :dir="cart.language === 'ar' ? 'rtl' : 'ltr'">
            <!-- Modern Header Section -->
            <div class="flex flex-col md:flex-row items-center justify-between gap-6 p-8 bg-white border border-[#f1f1f1] shadow-[0_8px_25px_rgba(0,0,0,0.03)] rounded-[24px]">
                <div>
                    <h2 class="text-[22px] font-bold text-[#ff2b2b] tracking-tight">{{ cart.language === 'ar' ? 'سجل المصروفات والتدفقات' : 'Expense & Cash Flow Ledger' }}</h2>
                    <p class="text-xs font-semibold text-gray-400 mt-1 uppercase tracking-widest">{{ cart.language === 'ar' ? 'تتبع دقيق لكافة المصروفات التشغيلية' : 'Comprehensive tracking of operational outflows' }}</p>
                </div>
                <button @click="openCreateModal" class="px-8 py-4 bg-gradient-to-br from-[#ff2b2b] to-[#ff4d4d] text-white rounded-[14px] font-bold shadow-[0_10px_25px_rgba(255,43,43,0.25)] hover:scale-[1.02] transition-all active:scale-95">
                    + {{ cart.language === 'ar' ? 'إضافة مصروف جديد' : 'New Expense Entry' }}
                </button>
            </div>

            <!-- Smart Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white p-6 rounded-[20px] border border-[#f1f1f1] shadow-[0_10px_25px_rgba(0,0,0,0.02)] transition-all hover:-translate-y-1">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-3">Total Expenses</p>
                    <h1 class="text-3xl font-extrabold text-[#ff2b2b] tracking-tighter">{{ formatCurrency(total_expenses) }}</h1>
                </div>
                <div class="bg-white p-6 rounded-[20px] border border-[#f1f1f1] shadow-[0_10px_25px_rgba(0,0,0,0.02)] transition-all hover:-translate-y-1">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-3">Today Expenses</p>
                    <h1 class="text-3xl font-extrabold text-[#111] tracking-tighter">{{ formatCurrency(stats.today) }}</h1>
                </div>
                <div class="bg-white p-6 rounded-[20px] border border-[#f1f1f1] shadow-[0_10px_25px_rgba(0,0,0,0.02)] transition-all hover:-translate-y-1">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-3">Biggest Category</p>
                    <div class="flex items-center gap-2">
                        <span class="text-xl">{{ stats.biggestCategory.icon }}</span>
                        <h1 class="text-xl font-bold text-[#111] truncate">{{ cart.language === 'ar' ? stats.biggestCategory.ar : stats.biggestCategory.en }}</h1>
                    </div>
                </div>
                <!-- Chart Container Placeholder -->
                <div class="bg-white p-6 rounded-[20px] border border-[#f1f1f1] shadow-[0_10px_25px_rgba(0,0,0,0.02)] flex flex-col justify-between">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">Net Flow</p>
                        <div class="flex gap-1">
                            <div class="w-1.5 h-1.5 bg-red-400 rounded-full"></div>
                            <div class="w-1.5 h-1.5 bg-emerald-400 rounded-full"></div>
                        </div>
                    </div>
                    <div class="h-8 flex items-end gap-1 px-1">
                        <div class="flex-1 bg-red-50 h-[60%] rounded-t-sm"></div>
                        <div class="flex-1 bg-red-100 h-[80%] rounded-t-sm"></div>
                        <div class="flex-1 bg-red-500 h-[40%] rounded-t-sm"></div>
                        <div class="flex-1 bg-red-200 h-[90%] rounded-t-sm"></div>
                    </div>
                </div>
            </div>

            <!-- Filter Bar -->
            <div class="flex items-center gap-2 overflow-x-auto pb-2 no-scrollbar">
                <button v-for="tab in ['Today', 'This Week', 'This Month', 'Custom Date']" :key="tab" @click="filterTab = tab" 
                    class="px-6 py-2 rounded-full text-[11px] font-black uppercase tracking-widest transition-all border shrink-0"
                    :class="filterTab === tab ? 'bg-[#ff2b2b] border-[#ff2b2b] text-white shadow-lg' : 'bg-white border-[#f1f1f1] text-gray-400 hover:text-[#ff2b2b]'">
                    {{ tab }}
                </button>
            </div>

            <!-- Table Section -->
            <div class="bg-white rounded-[24px] border border-[#f2f2f2] shadow-[0_10px_35px_rgba(0,0,0,0.02)] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left" :class="cart.language === 'ar' ? 'text-right' : 'text-left'">
                        <thead>
                            <tr class="bg-[#fcfcfc] border-b border-[#f2f2f2] text-[10px] font-black uppercase tracking-widest text-gray-400">
                                <th class="px-10 py-6">{{ cart.language === 'ar' ? 'التاريخ' : 'Process Date' }}</th>
                                <th class="px-10 py-6">{{ cart.language === 'ar' ? 'البند والوصف' : 'Description' }}</th>
                                <th class="px-10 py-6 text-center">{{ cart.language === 'ar' ? 'الفئة' : 'Classification' }}</th>
                                <th class="px-10 py-6 text-center">{{ cart.language === 'ar' ? 'القيمة' : 'Amount' }}</th>
                                <th class="px-10 py-6"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#f9f9f9]">
                            <tr v-for="expense in expenses" :key="expense.id" class="group hover:bg-[#fff4f4] transition-all duration-300">
                                <td class="px-10 py-6">
                                    <p class="text-[11px] font-bold text-gray-400 leading-none mb-1">{{ new Date(expense.date).toDateString() }}</p>
                                    <p class="text-[9px] font-black text-[#ff2b2b] uppercase tracking-widest">Authorized</p>
                                </td>
                                <td class="px-10 py-6">
                                    <p class="text-sm font-bold text-[#1e1e1e] tracking-tight leading-none mb-1.5">{{ expense.title }}</p>
                                    <p v-if="expense.notes" class="text-[10px] text-gray-400 font-semibold line-clamp-1 italic">{{ expense.notes }}</p>
                                </td>
                                <td class="px-10 py-6 text-center">
                                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-[#f1f1f1] rounded-full text-[10px] font-black uppercase tracking-widest text-gray-500 shadow-sm transition-all group-hover:border-[#ff2b2b] group-hover:text-[#ff2b2b]">
                                        <span>{{ categories.find(c => c.key === expense.category)?.icon || '📦' }}</span>
                                        {{ categories.find(c => c.key === expense.category)?.[cart.language] || expense.category }}
                                    </div>
                                </td>
                                <td class="px-10 py-6 text-center">
                                    <p class="text-base font-black text-[#ff2b2b] tracking-tighter">{{ formatCurrency(expense.amount) }}</p>
                                </td>
                                <td class="px-10 py-6" :class="cart.language === 'ar' ? 'text-left' : 'text-right'">
                                    <div class="flex items-center gap-2 justify-end opacity-0 group-hover:opacity-100 transition-all scale-95 group-hover:scale-100">
                                        <button @click="openEditModal(expense)" class="p-2.5 bg-white border border-[#f1f1f1] text-gray-400 hover:text-[#ff2b2b] hover:border-[#ff2b2b] rounded-xl transition-all shadow-sm">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                        </button>
                                        <button @click="deleteExpense(expense.id)" class="p-2.5 bg-white border border-[#f1f1f1] text-gray-400 hover:text-red-600 hover:border-red-600 rounded-xl transition-all shadow-sm">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!expenses || expenses.length === 0">
                                <td colspan="5" class="px-10 py-32 text-center text-gray-300">
                                    <div class="text-5xl mb-6 grayscale opacity-20">📊</div>
                                    <p class="text-[11px] font-black uppercase tracking-[0.3em] font-cairo">{{ cart.language === 'ar' ? 'لا يوجد مصروفات حالياً' : 'Zero Expenses Found' }}</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modern Red Modal -->
        <div v-if="showModal" class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-gray-900/40 backdrop-blur-sm" :dir="cart.language === 'ar' ? 'rtl' : 'ltr'" @click.self="showModal = false">
            <div class="bg-white w-full max-w-xl rounded-[32px] shadow-2xl border border-[#f1f1f1] relative animate-in zoom-in-95 duration-500 overflow-hidden">
                <div class="h-2 bg-gradient-to-r from-[#ff2b2b] to-[#ff4d4d]"></div>
                <div class="p-10">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h3 class="text-2xl font-black text-[#1e1e1e] tracking-tighter">{{ editingExpense ? (cart.language === 'ar' ? 'تعديل المعاملة' : 'Update Transaction') : (cart.language === 'ar' ? 'قيد مصروف جديد' : 'New Expenditure Entry') }}</h3>
                            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-[#ff2b2b] mt-1">{{ cart.language === 'ar' ? 'إدارة التدفقات المالية للمؤسسة' : 'Manage organizational financial outflows' }}</p>
                        </div>
                        <button @click="showModal = false" class="p-3 text-gray-400 hover:text-[#ff2b2b] bg-[#fcfcfc] rounded-2xl transition-all">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <form @submit.prevent="submit" class="grid grid-cols-2 gap-6">
                        <div class="col-span-2 space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 px-1">{{ cart.language === 'ar' ? 'بيان الصرف / العنوان' : 'Transaction Identifier' }}</label>
                            <input v-model="form.title" type="text" class="w-full bg-[#fcfcfc] border border-[#f1f1f1] rounded-[18px] py-4.5 px-6 text-sm font-bold text-[#1e1e1e] focus:ring-4 focus:ring-[#ff2b2b]/5 focus:border-[#ff2b2b] outline-none transition-all" required>
                        </div>

                        <div class="space-y-2 text-red-500">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 px-1">{{ cart.language === 'ar' ? 'القيمة المالية' : 'Amount' }}</label>
                            <input v-model.number="form.amount" type="number" step="0.01" class="w-full bg-[#fff3f3] border border-red-50 rounded-[18px] py-4.5 px-6 text-xl font-black text-[#ff2b2b] focus:ring-4 focus:ring-red-500/10 outline-none transition-all placeholder:text-red-200" placeholder="0.00" required>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 px-1">{{ cart.language === 'ar' ? 'الفئة المصلحية' : 'Classification' }}</label>
                            <select v-model="form.category" class="w-full bg-[#fcfcfc] border border-[#f1f1f1] rounded-[18px] py-4.5 px-6 text-sm font-bold text-[#1e1e1e] focus:ring-4 focus:ring-[#ff2b2b]/5 focus:border-[#ff2b2b] outline-none appearance-none cursor-pointer">
                                <option v-for="cat in categories" :key="cat.key" :value="cat.key">{{ cart.language === 'ar' ? cat.ar : cat.en }}</option>
                            </select>
                        </div>

                        <div class="space-y-2 col-span-2 md:col-span-1">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 px-1">{{ cart.language === 'ar' ? 'التاريخ' : 'Effective Date' }}</label>
                            <input v-model="form.date" type="date" class="w-full bg-[#fcfcfc] border border-[#f1f1f1] rounded-[18px] py-4.5 px-6 text-sm font-bold text-[#1e1e1e] focus:ring-4 focus:ring-[#ff2b2b]/5 focus:border-[#ff2b2b] outline-none transition-all" required>
                        </div>

                        <div class="col-span-2 space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 px-1">{{ cart.language === 'ar' ? 'ملاحظات إضافية' : 'Contextual Notes' }}</label>
                            <textarea v-model="form.notes" class="w-full bg-[#fcfcfc] border border-[#f1f1f1] rounded-[18px] py-4 px-6 text-sm font-bold text-[#1e1e1e] focus:ring-4 focus:ring-[#ff2b2b]/5 focus:border-[#ff2b2b] outline-none min-h-[100px] transition-all"></textarea>
                        </div>

                        <button type="submit" class="col-span-2 py-5 bg-gradient-to-r from-[#ff2b2b] to-[#ff4d4d] text-white rounded-[20px] font-black uppercase tracking-[0.3em] text-xs shadow-2xl shadow-red-500/20 hover:scale-[1.01] transition-all active:scale-95 disabled:opacity-50" :disabled="form.processing">
                            {{ form.processing ? '...' : (editingExpense ? (cart.language === 'ar' ? 'حفظ البيانات' : 'Update Ledger') : (cart.language === 'ar' ? 'تأكيد التسجيل' : 'Commit Entry')) }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
