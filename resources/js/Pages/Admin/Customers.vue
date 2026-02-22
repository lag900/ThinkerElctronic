<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref, watch, computed } from 'vue';
import { Head, router, useForm, Link } from '@inertiajs/vue3';
import { cart } from '@/Stores/CartStore';
import { system } from '@/Stores/SystemStore';
import debounce from 'lodash/debounce';

const props = defineProps({
    customers: Object,
    stats: Object,
    filters: Object
});

const search = ref(props.filters?.search || '');
const showAddModal = ref(false);
const showSlidePanel = ref(false);
const selectedCustomer = ref(null);
const isEditing = ref(false);

const filteredCustomers = computed(() => props.customers.data);

watch(search, debounce((val) => {
    router.get(route('admin.customers.index'), { search: val }, {
        preserveState: true,
        replace: true,
        preserveScroll: true
    });
}, 500));

const totalDebt = computed(() => props.stats?.total_debt || 0);

const customerForm = useForm({
    id: null,
    name: '',
    email: '',
    phone: '',
    password: ''
});

const openAddModal = () => {
    isEditing.value = false;
    customerForm.reset();
    showAddModal.value = true;
};

const openEditModal = (customer) => {
    isEditing.value = true;
    customerForm.id = customer.id;
    customerForm.name = customer.name;
    customerForm.email = customer.email;
    customerForm.phone = customer.phone;
    customerForm.password = '';
    showAddModal.value = true;
};

const openDetails = (customer) => {
    selectedCustomer.value = customer;
    showSlidePanel.value = true;
};

const submitCustomer = () => {
    if (isEditing.value) {
        customerForm.put(route('admin.customers.update', customerForm.id), {
            onSuccess: () => {
                showAddModal.value = false;
                system.notify(cart.language === 'ar' ? 'تم تحديث البيانات' : 'Customer updated');
            }
        });
    } else {
        customerForm.post(route('admin.customers.store'), {
            onSuccess: () => {
                showAddModal.value = false;
                customerForm.reset();
                system.notify(cart.language === 'ar' ? 'تمت إضافة العميل' : 'Customer added');
            }
        });
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

const getCustomerTier = (customer) => {
    const spent = parseFloat(customer.total_spent || 0);
    if (spent > 50000) return { label: cart.language === 'ar' ? 'مميز (VIP)' : 'VIP Gold', color: 'text-amber-600 bg-amber-50 border-amber-100', dot: 'bg-amber-500' };
    if (spent > 10000) return { label: cart.language === 'ar' ? 'قيمة عالية' : 'High Value', color: 'text-emerald-600 bg-emerald-50 border-emerald-100', dot: 'bg-emerald-500' };
    return { label: cart.language === 'ar' ? 'اعتيادي' : 'Standard', color: 'text-gray-400 bg-gray-50 border-gray-100', dot: 'bg-gray-400' };
};
</script>

<template>
    <Head :title="cart.language === 'ar' ? 'العملاء' : 'Customers CRM'" />

    <AdminLayout>
        <template #header>{{ cart.language === 'ar' ? 'نظام إدارة العملاء' : 'Customer CRM Dashboard' }}</template>

        <div class="space-y-8 font-inter bg-white -m-8 p-8 min-h-screen" :dir="cart.language === 'ar' ? 'rtl' : 'ltr'">
            <!-- Metrics Section -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-[24px] border border-[#f2f2f2] shadow-[0_10px_25px_rgba(0,0,0,0.03)] hover:shadow-[0_15px_35px_rgba(255,43,43,0.08)] hover:-translate-y-1 transition-all group">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-[#fff3f3] text-[#ff2b2b] rounded-2xl flex items-center justify-center border border-red-50">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 leading-none mb-1.5">{{ cart.language === 'ar' ? 'إجمالي العملاء' : 'Metric: Population' }}</p>
                            <h4 class="text-2xl font-black text-[#ff2b2b] tracking-tighter leading-none">{{ formatNumber(customers.total) }}</h4>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-[24px] border border-[#f2f2f2] shadow-[0_10px_25px_rgba(0,0,0,0.03)] hover:shadow-[0_15px_35px_rgba(255,43,43,0.08)] hover:-translate-y-1 transition-all group">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center border border-emerald-100">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 leading-none mb-1.5">{{ cart.language === 'ar' ? 'الاحتفاظ بالعملاء' : 'Metric: Retention' }}</p>
                            <h4 class="text-2xl font-black text-emerald-600 tracking-tighter leading-none">88.4%</h4>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-[24px] border border-[#f2f2f2] shadow-[0_10px_25px_rgba(0,0,0,0.03)] hover:shadow-[0_15px_35px_rgba(255,43,43,0.08)] hover:-translate-y-1 transition-all group">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center border border-amber-100">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 leading-none mb-1.5">{{ cart.language === 'ar' ? 'المستحقات المعلقة' : 'Total Receivables' }}</p>
                            <h4 class="text-2xl font-black text-amber-600 tracking-tighter leading-none">{{ formatCurrency(totalDebt) }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Controls -->
            <div class="flex flex-col md:flex-row items-center justify-between gap-4 pb-6 border-b border-[#f2f2f2]">
                <div class="relative w-full max-w-md">
                    <svg class="absolute w-4 h-4 text-gray-400 top-1/2 -translate-y-1/2" :class="cart.language === 'ar' ? 'right-4' : 'left-4'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    <input 
                        v-model="search"
                        type="text" 
                        :placeholder="cart.language === 'ar' ? 'ابحث باسم العميل أو رقم الهاتف...' : 'Search by Name, Contact, ID...'"
                        class="w-full bg-[#fcfcfc] border border-[#f2f2f2] rounded-[14px] py-4 px-10 text-sm font-medium transition-all focus:ring-4 focus:ring-[#ff2b2b]/5 focus:border-[#ff2b2b] placeholder:text-gray-300"
                        :class="cart.language === 'ar' ? 'pr-12' : 'pl-12'"
                    >
                </div>
                <button 
                    @click="openAddModal"
                    class="w-full md:w-auto px-8 py-4 bg-gradient-to-r from-[#ff2b2b] to-[#ff4d4d] text-white rounded-[14px] text-xs font-black uppercase tracking-widest shadow-[0_10px_25px_rgba(255,43,43,0.25)] hover:scale-[1.02] active:scale-95 transition-all"
                >
                    + {{ cart.language === 'ar' ? 'إضافة عميل جديد' : 'Onboard Client' }}
                </button>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-[20px] border border-[#f2f2f2] shadow-[0_5px_15px_rgba(0,0,0,0.02)] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-[#fcfcfc] border-b border-[#f2f2f2] text-[11px] font-black uppercase tracking-widest text-gray-400">
                                <th class="px-8 py-5">{{ cart.language === 'ar' ? 'العميل والهوية' : 'Client Profile' }}</th>
                                <th class="px-8 py-5 text-center">{{ cart.language === 'ar' ? 'تصنيف الحساب' : 'Account Tier' }}</th>
                                <th class="px-8 py-5 text-center">{{ cart.language === 'ar' ? 'صافي المشتريات' : 'LTV Total' }}</th>
                                <th class="px-8 py-5 text-center">{{ cart.language === 'ar' ? 'المديونية' : 'Balance' }}</th>
                                <th class="px-8 py-5 text-center">{{ cart.language === 'ar' ? 'آخر تواصل' : 'Last Seen' }}</th>
                                <th class="px-8 py-5"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#f8f8f8]">
                            <tr v-for="c in customers.data" :key="c.id" class="group hover:bg-[#fff6f6] transition-all cursor-pointer" @click="openDetails(c)">
                                <td class="px-8 py-5">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-xl bg-[#fff3f3] text-[#ff2b2b] flex items-center justify-center font-black text-xs border border-red-50 group-hover:scale-110 transition-transform">
                                            {{ c.name ? c.name.charAt(0) : 'C' }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-[#1e1e1e] tracking-tight leading-none mb-1.5">{{ c.name }}</p>
                                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">{{ c.phone || 'NO PHONE' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-5 text-center" @click.stop>
                                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest border transition-all"
                                        :class="getCustomerTier(c).color">
                                        <div class="w-1.5 h-1.5 rounded-full" :class="getCustomerTier(c).dot"></div>
                                        {{ getCustomerTier(c).label }}
                                    </span>
                                </td>
                                <td class="px-8 py-5 text-center text-[13px] font-black text-[#1e1e1e] tracking-tighter">{{ formatCurrency(c.total_spent) }}</td>
                                <td class="px-8 py-5 text-center">
                                    <span class="px-2 py-1 rounded-md text-[11px] font-black tracking-tighter" :class="c.debt > 0 ? 'bg-red-50 text-red-500' : 'bg-emerald-50 text-emerald-600'">
                                        {{ c.debt > 0 ? '-' : '' }}{{ formatCurrency(c.debt) }}
                                    </span>
                                </td>
                                <td class="px-8 py-5 text-center">
                                    <p class="text-[11px] font-bold text-gray-900 leading-none mb-1">{{ c.last_order_at ? new Date(c.last_order_at).toLocaleDateString() : '--' }}</p>
                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">via POS Terminal</p>
                                </td>
                                <td class="px-8 py-5" :class="cart.language === 'ar' ? 'text-left' : 'text-right'" @click.stop>
                                    <div class="flex items-center gap-2 justify-end">
                                        <button @click="openEditModal(c)" class="p-2.5 bg-white border border-[#f2f2f2] rounded-xl text-gray-400 hover:text-[#ff2b2b] hover:border-[#ff2b2b] hover:shadow-lg transition-all">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                        </button>
                                        <Link :href="route('admin.customers.show', c.id)" class="px-4 py-2.5 bg-[#fcfcfc] text-[#1e1e1e] rounded-xl text-[10px] font-black uppercase tracking-widest border border-[#f2f2f2] hover:bg-white transition-all shadow-sm">
                                            {{ cart.language === 'ar' ? 'عرض' : 'Panel' }}
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="flex justify-center pt-8">
                <div v-if="customers.links" class="flex items-center gap-1.5 p-1.5 bg-[#fcfcfc] rounded-2xl border border-[#f2f2f2] shadow-sm">
                    <button 
                        v-for="(link, idx) in customers.links" 
                        :key="idx"
                        @click="link.url && router.visit(link.url)"
                        v-html="link.label"
                        :disabled="!link.url || link.active"
                        class="px-4 py-2.5 rounded-xl text-[11px] font-black transition-all"
                        :class="[
                            link.active ? 'bg-[#ff2b2b] text-white shadow-xl shadow-red-500/10' : 'text-gray-400 hover:text-[#ff2b2b] hover:bg-white',
                            !link.url && 'opacity-20'
                        ]"
                    ></button>
                </div>
            </div>
        </div>

        <!-- Add/Edit Modal -->
        <div v-if="showAddModal" class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-gray-900/40 backdrop-blur-sm animate-in fade-in duration-300" @click.self="showAddModal = false">
            <div class="bg-white w-full max-w-lg rounded-[28px] shadow-2xl border border-[#f2f2f2] overflow-hidden animate-in zoom-in-95 duration-500">
                <div class="p-8 border-b border-[#f2f2f2] flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-black text-[#1e1e1e] tracking-tighter">{{ isEditing ? (cart.language === 'ar' ? 'تعديل بيانات العميل' : 'Modify Client Parameters') : (cart.language === 'ar' ? 'تسجيل عميل جديد' : 'Architectural Onboarding') }}</h3>
                        <p class="text-[10px] font-bold text-[#ff2b2b] uppercase tracking-widest mt-1">Configure client governance node</p>
                    </div>
                    <button @click="showAddModal = false" class="p-2 text-gray-400 hover:text-[#ff2b2b] transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <form @submit.prevent="submitCustomer" class="p-8 space-y-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 px-1">{{ cart.language === 'ar' ? 'الاسم الكامل' : 'Legal Identity Name' }}</label>
                        <input v-model="customerForm.name" type="text" class="w-full bg-[#fcfcfc] border border-[#f2f2f2] rounded-2xl py-4.5 px-6 text-sm font-bold text-[#1e1e1e] focus:ring-4 focus:ring-[#ff2b2b]/5 focus:border-[#ff2b2b] outline-none transition-all" required>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 px-1">{{ cart.language === 'ar' ? 'رقم الهاتف' : 'Contact Frequency' }}</label>
                            <input v-model="customerForm.phone" type="text" class="w-full bg-[#fcfcfc] border border-[#f2f2f2] rounded-2xl py-4.5 px-6 text-sm font-bold text-[#1e1e1e] focus:ring-4 focus:ring-[#ff2b2b]/5 focus:border-[#ff2b2b] outline-none transition-all" required>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 px-1">{{ cart.language === 'ar' ? 'البريد الإلكتروني' : 'Digital Mail' }}</label>
                            <input v-model="customerForm.email" type="email" class="w-full bg-[#fcfcfc] border border-[#f2f2f2] rounded-2xl py-4.5 px-6 text-sm font-bold text-[#1e1e1e] focus:ring-4 focus:ring-[#ff2b2b]/5 focus:border-[#ff2b2b] outline-none transition-all" required>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 px-1">{{ cart.language === 'ar' ? 'كلمة المرور' : 'System Credentials' }}</label>
                        <input v-model="customerForm.password" type="password" class="w-full bg-[#fcfcfc] border border-[#f2f2f2] rounded-2xl py-4.5 px-6 text-sm font-bold text-[#1e1e1e] focus:ring-4 focus:ring-[#ff2b2b]/5 focus:border-[#ff2b2b] outline-none transition-all" placeholder="Leave blank to keep current">
                    </div>

                    <button type="submit" class="w-full py-5 bg-gradient-to-r from-[#ff2b2b] to-[#ff4d4d] text-white rounded-[18px] font-black uppercase tracking-[0.2em] text-xs shadow-[0_15px_30px_rgba(255,43,43,0.3)] hover:scale-[1.01] hover:-translate-y-1 transition-all active:scale-95 disabled:opacity-50" :disabled="customerForm.processing">
                        {{ customerForm.processing ? '...' : (isEditing ? (cart.language === 'ar' ? 'حفظ البيانات' : 'Commit Changes') : (cart.language === 'ar' ? 'إتمام التسجيل' : 'Onboard Now')) }}
                    </button>
                </form>
            </div>
        </div>

        <!-- Details Slide Panel -->
        <Transition name="slide">
            <div v-if="showSlidePanel" class="fixed inset-y-0 right-0 z-[150] w-full max-w-md bg-white shadow-2xl border-l border-[#f2f2f2] overflow-hidden flex flex-col" :dir="cart.language === 'ar' ? 'rtl' : 'ltr'">
                <div class="p-8 border-b border-[#f2f2f2] flex items-center justify-between bg-[#fff3f3]">
                    <div>
                        <h3 class="text-xl font-black text-[#1e1e1e] tracking-tighter">{{ selectedCustomer?.name }}</h3>
                        <p class="text-[10px] font-bold text-[#ff2b2b] uppercase tracking-widest mt-1">Customer Profile & Intelligence</p>
                    </div>
                    <button @click="showSlidePanel = false" class="p-2 text-gray-400 hover:text-[#ff2b2b] bg-white rounded-xl shadow-sm transition-all">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <!-- Content -->
                <div class="p-8 space-y-8 overflow-y-auto flex-1 custom-scrollbar">
                    <!-- Status Cards -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-5 bg-[#fcfcfc] border border-[#f2f2f2] rounded-2xl">
                            <p class="text-[9px] font-black uppercase tracking-widest text-gray-400 mb-2">Life Value</p>
                            <p class="text-xl font-black text-[#1e1e1e] tracking-tighter">{{ formatCurrency(selectedCustomer?.total_spent) }}</p>
                        </div>
                        <div class="p-5 bg-[#fcfcfc] border border-[#f2f2f2] rounded-2xl">
                            <p class="text-[9px] font-black uppercase tracking-widest text-gray-400 mb-2">Net Balance</p>
                            <p class="text-xl font-black tracking-tighter" :class="selectedCustomer?.debt > 0 ? 'text-[#ff2b2b]' : 'text-emerald-500'">{{ formatCurrency(selectedCustomer?.debt) }}</p>
                        </div>
                    </div>

                    <!-- Contact Info -->
                    <div class="space-y-4">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 block px-1">Network Contact Nodes</label>
                        <div class="space-y-2">
                            <div class="flex items-center gap-4 p-4 bg-white border border-[#f2f2f2] rounded-2xl hover:border-[#ff2b2b] transition-all">
                                <div class="w-10 h-10 bg-[#fff3f3] text-[#ff2b2b] rounded-xl flex items-center justify-center shrink-0"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg></div>
                                <div><p class="text-[10px] text-gray-400 font-black uppercase leading-none mb-1">Mobile</p><p class="text-sm font-bold text-[#1e1e1e]">{{ selectedCustomer?.phone }}</p></div>
                            </div>
                            <div class="flex items-center gap-4 p-4 bg-white border border-[#f2f2f2] rounded-2xl hover:border-[#ff2b2b] transition-all">
                                <div class="w-10 h-10 bg-[#fff3f3] text-[#ff2b2b] rounded-xl flex items-center justify-center shrink-0"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg></div>
                                <div><p class="text-[10px] text-gray-400 font-black uppercase leading-none mb-1">Email</p><p class="text-sm font-bold text-[#1e1e1e]">{{ selectedCustomer?.email }}</p></div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick History Placeholder -->
                    <div class="space-y-4">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 block px-1">Engagement History</label>
                        <div class="bg-[#fcfcfc] rounded-2xl border border-[#f2f2f2] p-8 text-center">
                            <p class="text-[11px] font-black text-gray-300 uppercase tracking-widest">Advanced analytics incoming</p>
                        </div>
                    </div>
                </div>
                <!-- Actions -->
                <div class="p-8 border-t border-[#f2f2f2] flex gap-3">
                    <button @click="openEditModal(selectedCustomer)" class="flex-1 py-4 bg-[#fcfcfc] text-[#1e1e1e] border border-[#f2f2f2] rounded-2xl text-[11px] font-black uppercase tracking-widest shadow-sm hover:bg-white transition-all">Quick Edit</button>
                    <Link :href="route('admin.customers.show', selectedCustomer?.id)" class="flex-1 py-4 bg-[#ff2b2b] text-white rounded-2xl text-[11px] font-black uppercase tracking-widest text-center shadow-lg shadow-red-500/20 hover:scale-[1.02] transition-all">Full Intelligence</Link>
                </div>
            </div>
        </Transition>
    </AdminLayout>
</template>

<style scoped>
.slide-enter-active, .slide-leave-active { transition: transform 0.4s cubic-bezier(0.19, 1, 0.22, 1); }
.slide-enter-from, .slide-leave-to { transform: translateX(100%); }

.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #f2f2f2; border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #ff2b2b; }
</style>
