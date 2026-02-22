<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref, onMounted, computed, watch } from 'vue';
import { Head, router, Link } from '@inertiajs/vue3';
import { cart } from '@/Stores/CartStore';
import { system } from '@/Stores/SystemStore';
import axios from 'axios';
import debounce from 'lodash/debounce';

const props = defineProps({
    orders: Object,
    employees: Array,
    filters: Object
});

const search = ref(props.filters?.search || '');
const statusFilter = ref(props.filters?.status || '');
const employeeFilter = ref(props.filters?.employee_id || '');
const startDate = ref(props.filters?.start_date || '');
const endDate = ref(props.filters?.end_date || '');
const processingId = ref(null);
const showQuickView = ref(false);
const selectedOrder = ref(null);

const formatCurrency = (val) => {
    return new Intl.NumberFormat(cart.language === 'ar' ? 'ar-EG' : 'en-US', { 
        style: 'currency', 
        currency: 'EGP',
        maximumFractionDigits: 0
    }).format(val || 0);
};

const openQuickView = (order) => {
    selectedOrder.value = order;
    showQuickView.ref = true;
};

const updateStatus = (orderId, newStatus) => {
    processingId.value = orderId;
    router.put(route('admin.orders.status', orderId), {
        status: newStatus
    }, {
        preserveScroll: true,
        onSuccess: () => {
            system.notify(cart.language === 'ar' ? 'تم تحديث حالة الطلب بنجاح' : 'Order status updated successfully');
        },
        onFinish: () => {
            processingId.value = null;
        }
    });
};

const previewInvoice = (orderId) => {
    window.open(`/admin/orders/${orderId}/invoice/preview`, '_blank');
};

const applyFilters = () => {
    router.get(route('admin.orders.index'), { 
        search: search.value,
        status: statusFilter.value,
        employee_id: employeeFilter.value,
        start_date: startDate.value,
        end_date: endDate.value
    }, { 
        preserveState: true, 
        replace: true,
        preserveScroll: true 
    });
};

const resetFilters = () => {
    search.value = '';
    statusFilter.value = '';
    employeeFilter.value = '';
    startDate.value = '';
    endDate.value = '';
    applyFilters();
};

const exportOrders = () => {
    const params = new URLSearchParams({
        search: search.value,
        status: statusFilter.value,
        employee_id: employeeFilter.value,
        start_date: startDate.value,
        end_date: endDate.value
    });
    window.location.href = route('admin.orders.export') + '?' + params.toString();
};

const deleteOrder = (orderId) => {
    if (confirm(cart.language === 'ar' ? 'هل أنت متأكد من حذف هذه الفاتورة نهائياً؟' : 'Delete this invoice permanently?')) {
        router.delete(route('admin.orders.destroy', orderId), {
            preserveScroll: true,
            onSuccess: () => {
                system.notify(cart.language === 'ar' ? 'تم حذف الطلب' : 'Order deleted');
            }
        });
    }
};

const bulkDelete = (status = 'all') => {
    let msg = '';
    if (status === 'all') msg = cart.language === 'ar' ? 'حذف كافة الفواتير وملفات PDF من الخادم؟' : 'Delete ALL invoices and PDFs from server?';
    if (status === 'failed') msg = cart.language === 'ar' ? 'حذف كافة الفواتير الفاشلة؟' : 'Delete all Failed orders?';
    if (status === 'pending') msg = cart.language === 'ar' ? 'حذف كافة الفواتير قيد الانتظار؟' : 'Delete all Pending orders?';

    if (confirm(msg)) {
        router.delete(route('admin.orders.bulkDelete'), {
            data: { status },
            preserveScroll: true,
            onSuccess: () => {
                system.notify(cart.language === 'ar' ? 'تمت العملية بنجاح' : 'Bulk deletion successful');
            }
        });
    }
};

watch([search, statusFilter, employeeFilter, startDate, endDate], debounce(() => {
    applyFilters();
}, 500));

const getStatusColor = (status) => {
    switch(status) {
        case 'pending': return 'text-amber-600 bg-amber-50 border-amber-100';
        case 'processing': return 'text-blue-600 bg-blue-50 border-blue-100';
        case 'completed': return 'text-emerald-600 bg-emerald-50 border-emerald-100';
        case 'failed': return 'text-red-600 bg-red-50 border-red-100';
        default: return 'text-gray-600 bg-gray-50 border-gray-100';
    }
};

const statuses = [
    { value: 'pending', label: cart.language === 'ar' ? 'قيد الانتظار' : 'Pending' },
    { value: 'processing', label: cart.language === 'ar' ? 'جاري المعالجة' : 'Processing' },
    { value: 'completed', label: cart.language === 'ar' ? 'مكتمل' : 'Completed' },
    { value: 'failed', label: cart.language === 'ar' ? 'فشل' : 'Failed' }
];

const openQuickOrder = (order) => {
    selectedOrder.value = order;
    showQuickView.value = true;
};
</script>

<template>
    <Head :title="cart.language === 'ar' ? 'إدارة المبيعات' : 'Order Management'" />

    <AdminLayout>
        <template #header>{{ cart.language === 'ar' ? 'إدارة المبيعات' : 'Order Management' }}</template>

        <div class="space-y-6 font-inter bg-white -m-8 p-8 min-h-screen" :dir="cart.language === 'ar' ? 'rtl' : 'ltr'">
            <!-- Management Controls -->
            <div class="space-y-4 pb-6 border-b border-[#f1f1f1]">
                <div class="flex flex-col xl:flex-row items-center justify-between gap-4">
                    <div class="flex flex-col lg:flex-row gap-3 items-center flex-1 w-full">
                        <div class="relative flex-1 w-full max-w-sm">
                            <svg class="absolute w-4 h-4 text-gray-400 top-1/2 -translate-y-1/2" :class="cart.language === 'ar' ? 'right-4' : 'left-4'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                            <input 
                                v-model="search"
                                type="text" 
                                :placeholder="cart.language === 'ar' ? 'رقم الطلب، عميل، هاتف...' : 'Order ID, Customer, Phone...'"
                                class="w-full bg-[#fcfcfc] border border-[#f1f1f1] rounded-[14px] py-3 text-sm font-medium transition-all focus:ring-4 focus:ring-[#ff2b2b]/5 focus:border-[#ff2b2b] placeholder:text-gray-400"
                                :class="cart.language === 'ar' ? 'pr-10' : 'pl-10'"
                            >
                        </div>
                        <div class="flex flex-wrap gap-2 w-full lg:w-auto">
                            <select 
                                v-model="statusFilter"
                                class="flex-1 lg:w-40 bg-[#fcfcfc] border border-[#f1f1f1] rounded-[14px] px-4 py-3 text-sm font-bold text-gray-700 focus:ring-4 focus:ring-[#ff2b2b]/5 focus:border-[#ff2b2b] cursor-pointer appearance-none outline-none"
                            >
                                <option value="">{{ cart.language === 'ar' ? 'كافة الحالات' : 'All Status' }}</option>
                                <option v-for="s in statuses" :key="s.value" :value="s.value">{{ s.label }}</option>
                            </select>
                            <select 
                                v-model="employeeFilter"
                                class="flex-1 lg:w-48 bg-[#fcfcfc] border border-[#f1f1f1] rounded-[14px] px-4 py-3 text-sm font-bold text-gray-700 focus:ring-4 focus:ring-[#ff2b2b]/5 focus:border-[#ff2b2b] cursor-pointer appearance-none outline-none"
                            >
                                <option value="">{{ cart.language === 'ar' ? 'بواسطة الموظف' : 'By Employee' }}</option>
                                <option v-for="emp in employees" :key="emp.id" :value="emp.id">{{ emp.name }}</option>
                            </select>
                            <div class="flex gap-2">
                                <input type="date" v-model="startDate" class="bg-[#fcfcfc] border border-[#f1f1f1] rounded-[14px] px-3 py-3 text-xs font-bold focus:ring-[#ff2b2b]/5">
                                <input type="date" v-model="endDate" class="bg-[#fcfcfc] border border-[#f1f1f1] rounded-[14px] px-3 py-3 text-xs font-bold focus:ring-[#ff2b2b]/5">
                            </div>
                            <button @click="resetFilters" class="p-3 bg-gray-50 text-gray-400 rounded-[14px] hover:text-[#ff2b2b] transition-all">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                            </button>
                        </div>
                    </div>
                    <div class="flex gap-3 w-full xl:w-auto">
                        <button @click="exportOrders" class="flex-1 xl:flex-none px-6 py-3 bg-[#fff1f1] text-[#ff2b2b] rounded-[12px] text-xs font-bold uppercase tracking-widest hover:bg-red-100 transition-all border border-red-50 flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            {{ cart.language === 'ar' ? 'تصدير إكسل' : 'Export Excel' }}
                        </button>
                        <Link :href="route('admin.pos.index')" class="flex-1 xl:flex-none px-8 py-3 bg-gradient-to-r from-[#ff2b2b] to-[#ff4d4d] text-white rounded-[12px] text-xs font-black uppercase tracking-widest hover:scale-[1.02] active:scale-95 transition-all shadow-[0_8px_20px_rgba(255,43,43,0.25)] text-center">
                            + {{ cart.language === 'ar' ? 'فاتورة جديدة' : 'New Order' }}
                        </Link>
                    </div>
                </div>
                
                <!-- Bulk Actions -->
                <div class="flex flex-wrap gap-2 justify-end">
                    <button @click="bulkDelete('pending')" class="px-4 py-2 bg-amber-50 text-amber-600 rounded-xl text-[10px] font-black uppercase tracking-widest border border-amber-100 hover:bg-amber-100 transition-all">
                        {{ cart.language === 'ar' ? 'حذف قيد الانتظار' : 'Delete Pending' }}
                    </button>
                    <button @click="bulkDelete('failed')" class="px-4 py-2 bg-red-50 text-red-600 rounded-xl text-[10px] font-black uppercase tracking-widest border border-red-100 hover:bg-red-100 transition-all">
                        {{ cart.language === 'ar' ? 'حذف الفاشلة' : 'Delete Failed' }}
                    </button>
                    <button @click="bulkDelete('all')" class="px-4 py-2 bg-gray-900 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-black transition-all shadow-lg">
                        {{ cart.language === 'ar' ? 'حذف كافة السجلات' : 'Delete All Orders' }}
                    </button>
                </div>
            </div>

            <!-- Table Section -->
            <div class="bg-white rounded-[18px] border border-[#f1f1f1] shadow-[0_5px_25px_rgba(0,0,0,0.03)] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-[#fcfcfc] border-b border-[#f1f1f1] text-[11px] font-black uppercase tracking-[0.1em] text-gray-400">
                                <th class="px-8 py-5">{{ cart.language === 'ar' ? 'رقم الطلب' : 'Order ID' }}</th>
                                <th class="px-8 py-5">{{ cart.language === 'ar' ? 'العميل والموظف' : 'Client & Staff' }}</th>
                                <th class="px-8 py-5 text-center">{{ cart.language === 'ar' ? 'الحالة' : 'Status' }}</th>
                                <th class="px-8 py-5 text-center text-emerald-600">{{ cart.language === 'ar' ? 'الإجمالي والربح' : 'Total & Profit' }}</th>
                                <th class="px-8 py-5 text-center">{{ cart.language === 'ar' ? 'التوقيت' : 'Time' }}</th>
                                <th class="px-8 py-5" :class="cart.language === 'ar' ? 'text-left' : 'text-right'"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#f8f8f8]">
                            <tr v-for="order in orders?.data ?? []" :key="order.id" class="group hover:bg-[#fff5f5] transition-all cursor-pointer" @click="openQuickOrder(order)">
                                <td class="px-8 py-5">
                                    <span class="text-[13px] font-black text-gray-900 tracking-tighter">#{{ order.id }}</span>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-[10px] bg-[#fff1f1] text-[#ff2b2b] flex items-center justify-center font-black text-xs border border-red-50">
                                            {{ order.customer?.name ? order.customer.name.charAt(0) : 'C' }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-[#1e1e1e] leading-none mb-1.5">{{ order.customer?.name || (cart.language === 'ar' ? 'عميل كاشير' : 'Walk-in') }}</p>
                                            <div class="flex items-center gap-1.5 opacity-60">
                                                <div class="w-1 h-1 rounded-full bg-gray-400"></div>
                                                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider">{{ order.creator?.name || '--' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-5 text-center" @click.stop>
                                    <div class="relative inline-block group/status">
                                        <button 
                                            class="px-4 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest border transition-all flex items-center gap-2 mx-auto"
                                            :class="getStatusColor(order.status)"
                                        >
                                            <span v-if="processingId === order.id" class="w-2.5 h-2.5 border-2 border-current border-t-transparent rounded-full animate-spin"></span>
                                            {{ cart.t(order.status) }}
                                            <svg class="w-3 h-3 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M19 9l-7 7-7-7" stroke-width="3"/></svg>
                                        </button>
                                        <!-- Dropdown -->
                                        <div class="absolute top-full left-1/2 -translate-x-1/2 mt-2 w-40 bg-white shadow-[0_10px_30px_rgba(0,0,0,0.08)] rounded-[14px] border border-[#f1f1f1] opacity-0 invisible group-hover/status:opacity-100 group-hover/status:visible transition-all z-50 p-2">
                                            <button 
                                                v-for="s in statuses" :key="s.value"
                                                @click="updateStatus(order.id, s.value)"
                                                class="w-full text-center py-2 text-[10px] font-bold uppercase tracking-widest rounded-lg hover:bg-[#fff1f1] hover:text-[#ff2b2b] transition-colors mb-1 last:mb-0"
                                                :class="order.status === s.value ? 'bg-[#fff1f1] text-[#ff2b2b]' : 'text-gray-500'"
                                            >
                                                {{ s.label }}
                                            </button>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-5 text-center">
                                    <div class="flex flex-col items-center">
                                        <span class="text-sm font-black text-[#1e1e1e] tracking-tighter">{{ formatCurrency(order.total) }}</span>
                                        <div v-if="order.profit" class="mt-1 px-2 py-0.5 rounded-md text-[9px] font-black tracking-wider uppercase" :class="order.profit >= 0 ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-600'">
                                            {{ order.profit >= 0 ? '+' : '' }}{{ formatCurrency(order.profit) }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-5 text-center">
                                    <p class="text-[11px] font-bold text-gray-900 leading-none mb-1">{{ order.created_at ? new Date(order.created_at).toLocaleDateString() : '--' }}</p>
                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">{{ order.created_at ? new Date(order.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : '' }}</p>
                                </td>
                                <td class="px-8 py-5" :class="cart.language === 'ar' ? 'text-left' : 'text-right'" @click.stop>
                                    <div class="flex items-center gap-2 justify-end">
                                        <span class="px-2 py-1 bg-gray-50 text-gray-400 text-[9px] font-bold uppercase tracking-widest rounded-md border border-[#f1f1f1]">
                                            {{ order.payment_method || 'CASH' }}
                                        </span>
                                        <button @click="previewInvoice(order.id)" class="p-2.5 bg-white border border-[#f1f1f1] rounded-[10px] text-gray-400 hover:text-[#ff2b2b] hover:border-[#ff2b2b] hover:shadow-lg transition-all shadow-sm">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                                        </button>
                                        <button @click="deleteOrder(order.id)" class="p-2.5 bg-white border border-[#f1f1f1] rounded-[10px] text-gray-400 hover:text-red-600 hover:border-red-600 hover:shadow-lg transition-all shadow-sm">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="(orders?.data?.length ?? 0) === 0">
                                <td colspan="6" class="text-center py-20 bg-white">
                                    <p class="font-black text-gray-300 uppercase tracking-widest text-[11px]">{{ cart.language === 'ar' ? 'لا يوجد طلبات مطابقة' : 'No records match filters' }}</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="flex justify-center pt-8">
                <div v-if="orders?.links" class="flex items-center gap-1 p-1 bg-[#fcfcfc] rounded-[14px] border border-[#f1f1f1]">
                    <button 
                        v-for="(link, idx) in orders.links" 
                        :key="idx"
                        @click="link.url && router.visit(link.url)"
                        v-html="link.label"
                        :disabled="!link.url || link.active"
                        class="px-4 py-2 rounded-[10px] text-[11px] font-black transition-all"
                        :class="[
                            link.active ? 'bg-[#ff2b2b] text-white shadow-lg' : 'text-gray-400 hover:text-[#ff2b2b]',
                            !link.url && 'opacity-20'
                        ]"
                    ></button>
                </div>
            </div>
        </div>

        <!-- Quick View Modal -->
        <div v-if="showQuickView" class="fixed inset-0 z-[200] flex items-center justify-center p-8 bg-gray-900/40 backdrop-blur-sm animate-in fade-in duration-300" @click.self="showQuickView = false">
            <div class="bg-white w-full max-w-2xl rounded-[32px] shadow-2xl overflow-hidden border border-[#f1f1f1] animate-in zoom-in-95 duration-500">
                <div class="p-8 border-b border-[#f1f1f1] flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-black text-[#1e1e1e] tracking-tighter">Order #{{ selectedOrder?.id }} Details</h3>
                        <p class="text-[10px] font-black text-[#ff2b2b] uppercase tracking-widest mt-1">{{ selectedOrder?.customer?.name || 'Walk-in Customer' }}</p>
                    </div>
                    <button @click="showQuickView = false" class="p-2 text-gray-400 hover:text-[#ff2b2b] transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M18 6L6 18M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="p-8 space-y-6 max-h-[60vh] overflow-y-auto custom-scrollbar">
                    <div class="grid grid-cols-2 gap-8">
                        <div>
                            <label class="text-[9px] font-black uppercase tracking-[0.2em] text-gray-400 block mb-3">Transaction Info</label>
                            <div class="bg-[#fcfcfc] p-4 rounded-2xl border border-[#f1f1f1] space-y-3">
                                <div class="flex justify-between"><span class="text-[11px] font-bold text-gray-500">Method:</span> <span class="text-[11px] font-black">{{ selectedOrder?.payment_method?.toUpperCase() || 'CASH' }}</span></div>
                                <div class="flex justify-between"><span class="text-[11px] font-bold text-gray-500">Status:</span> <span class="text-[11px] font-black uppercase tracking-widest text-[#ff2b2b]">{{ selectedOrder?.status }}</span></div>
                                <div class="flex justify-between"><span class="text-[11px] font-bold text-gray-500">Staff:</span> <span class="text-[11px] font-black">{{ selectedOrder?.creator?.name }}</span></div>
                            </div>
                        </div>
                        <div>
                            <label class="text-[9px] font-black uppercase tracking-[0.2em] text-gray-400 block mb-3">Financial Value</label>
                            <div class="bg-gradient-to-br from-[#ff2b2b] to-[#ff4d4d] p-5 rounded-2xl text-white shadow-lg shadow-red-500/10">
                                <p class="text-[10px] font-black uppercase tracking-widest opacity-80 mb-1">Market Total</p>
                                <p class="text-2xl font-black tracking-tighter">{{ formatCurrency(selectedOrder?.total) }}</p>
                                <div v-if="selectedOrder?.profit" class="mt-4 pt-4 border-t border-white/10 flex justify-between items-center">
                                    <span class="text-[10px] font-black uppercase tracking-widest">Net Profit</span>
                                    <span class="text-sm font-black">{{ formatCurrency(selectedOrder?.profit) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-8 bg-gray-50 border-t border-[#f1f1f1] flex gap-3">
                    <Link :href="route('admin.orders.show', selectedOrder?.id)" class="flex-1 py-4 bg-white text-gray-900 border border-[#f1f1f1] rounded-2xl text-[11px] font-black uppercase tracking-widest text-center hover:bg-white/50 transition-all">Details Page</Link>
                    <button @click="previewInvoice(selectedOrder?.id)" class="flex-1 py-4 bg-[#ff2b2b] text-white rounded-2xl text-[11px] font-black uppercase tracking-widest shadow-xl shadow-red-500/20 hover:scale-[1.02] active:scale-95 transition-all">Print Invoice</button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #f1f1f1;
  border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #ff2b2b;
}
</style>
