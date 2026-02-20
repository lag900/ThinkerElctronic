<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref, onMounted } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { cart } from '@/Stores/CartStore';
import axios from 'axios';

const props = defineProps({
    orders: Object
});

const processingId = ref(null);
const notification = ref(null);

const showNotification = (message, type = 'success') => {
    notification.value = { message, type };
    setTimeout(() => {
        notification.value = null;
    }, 3000);
};

const getStatusColor = (status) => {
    switch (status) {
        case 'processing': return 'text-indigo-600 bg-indigo-50 border-indigo-100';
        case 'shipped': return 'text-blue-600 bg-blue-50 border-blue-100';
        case 'delivered': return 'text-emerald-600 bg-emerald-50 border-emerald-100';
        case 'pending': return 'text-amber-600 bg-amber-50 border-amber-100';
        default: return 'text-slate-500 bg-slate-50 border-slate-100';
    }
};

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

const generateInvoice = async (orderId) => {
    processingId.value = orderId;
    try {
        const response = await axios.post(`/admin/orders/${orderId}/invoice`);
        if (response.data.success) {
            showNotification(cart.language === 'ar' ? 'تم إنشاء الفاتورة بنجاح' : 'Invoice generated successfully');
            // Refresh the page to show latest invoice info
            router.reload({ only: ['orders'] });
        }
    } catch (error) {
        console.error(error);
        showNotification(cart.language === 'ar' ? 'حدث خطأ أثناء إنشاء الفاتورة' : 'Error generating invoice', 'error');
    } finally {
        processingId.value = null;
    }
};

const downloadPDF = (token) => {
    window.open(`/admin/invoices/download/${token}`, '_blank');
};

const sendEmail = async (invoiceId) => {
    try {
        const response = await axios.post(`/admin/invoices/${invoiceId}/send-email`);
        if (response.data.success) {
            showNotification(cart.language === 'ar' ? 'تم إرسال الفاتورة للبريد الإلكتروني' : 'Invoice sent to email');
        }
    } catch (error) {
        console.error(error);
        showNotification(cart.language === 'ar' ? 'فشل إرسال البريد الإلكتروني' : 'Failed to send email', 'error');
    }
};

const previewInvoice = (orderId) => {
    window.open(`/admin/orders/${orderId}/invoice/preview`, '_blank');
};
</script>

<template>
    <Head :title="cart.t('orders')" />

    <AdminLayout>
        <template #header>{{ cart.t('orders') }}</template>

        <!-- Notification Toast -->
        <Transition name="fade">
            <div v-if="notification" class="fixed top-6 right-6 z-50 px-6 py-4 rounded-2xl shadow-2xl border text-sm font-black flex items-center gap-3 backdrop-blur-xl" :class="notification.type === 'error' ? 'bg-red-50/90 border-red-100 text-red-600' : 'bg-emerald-50/90 border-emerald-100 text-emerald-600'">
                <div class="w-2 h-2 rounded-full animate-pulse" :class="notification.type === 'error' ? 'bg-red-500' : 'bg-emerald-500'"></div>
                {{ notification.message }}
            </div>
        </Transition>

        <div class="space-y-8">
            <!-- Tabs -->
            <div class="flex gap-8 border-b border-slate-200 pb-px">
                <button v-for="tab in (cart.language === 'ar' ? ['الكل', 'قيد الانتظار', 'قيد التنفيذ', 'فاشل'] : ['All Transmissions', 'Pending', 'Processing', 'Failed'])" :key="tab" class="text-[10px] font-black uppercase tracking-widest pb-4 border-b-2 transition-all" :class="tab === (cart.language === 'ar' ? 'الكل' : 'All Transmissions') ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-400 hover:text-slate-600'">
                    {{ tab }}
                </button>
            </div>

            <!-- Orders Listing -->
            <div class="bg-white rounded-[40px] border border-slate-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left" :class="{'text-right': cart.language === 'ar'}" :dir="cart.language === 'ar' ? 'rtl' : 'ltr'">
                        <thead>
                            <tr class="bg-slate-50/50 text-[10px] font-black uppercase tracking-widest text-slate-400">
                                <th class="px-8 py-6">{{ cart.t('orderId') }}</th>
                                <th class="px-8 py-6">{{ cart.language === 'ar' ? 'العميل' : 'Customer Subject' }}</th>
                                <th class="px-8 py-6">{{ cart.t('date') }}</th>
                                <th class="px-8 py-6">{{ cart.language === 'ar' ? 'القيمة' : 'Value' }}</th>
                                <th class="px-8 py-6">{{ cart.t('status') }}</th>
                                <th class="px-8 py-6">{{ cart.language === 'ar' ? 'رقم الفاتورة' : 'Invoice No.' }}</th>
                                <th class="px-8 py-6" :class="cart.language === 'ar' ? 'text-left' : 'text-right'">{{ cart.t('action') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <tr v-for="order in orders.data" :key="order.id" class="hover:bg-slate-50/30 transition-colors group">
                                <td class="px-8 py-6">
                                    <span class="text-xs font-black text-indigo-600 bg-indigo-50/50 px-2 py-1 rounded-md">#{{ order.order_number }}</span>
                                </td>
                                <td class="px-8 py-6">
                                    <p class="text-sm font-black text-slate-900">{{ order.customer?.name }}</p>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tight">{{ order.customer?.email }}</p>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="text-[11px] font-bold text-slate-400">{{ new Date(order.created_at).toLocaleDateString(cart.language === 'ar' ? 'ar-EG' : 'en-US') }}</span>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="text-sm font-black text-slate-900">{{ formatCurrency(order.total) }}</span>
                                </td>
                                <td class="px-8 py-6">
                                    <span 
                                        class="inline-flex px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest border"
                                        :class="getStatusColor(order.status)"
                                    >
                                        {{ order.status }}
                                    </span>
                                </td>
                                <td class="px-8 py-6">
                                    <span v-if="order.invoice" class="text-[10px] font-black text-slate-500 bg-slate-100 px-2 py-1 rounded-md">
                                        {{ order.invoice.invoice_number }}
                                    </span>
                                    <span v-else class="text-[10px] font-bold text-slate-300 italic">
                                        {{ cart.language === 'ar' ? 'لم يتم إنشاؤها' : 'Not Generated' }}
                                    </span>
                                </td>
                                <td class="px-8 py-6" :class="cart.language === 'ar' ? 'text-left' : 'text-right'">
                                    <div class="flex items-center gap-2" :class="cart.language === 'ar' ? 'justify-start' : 'justify-end'">
                                        <!-- Actions -->
                                        <button 
                                            v-if="!order.invoice"
                                            @click="generateInvoice(order.id)"
                                            :disabled="processingId === order.id"
                                            class="p-2 hover:bg-red-50 rounded-xl text-slate-300 hover:text-red-600 transition-all flex items-center gap-2 group"
                                            title="Generate Invoice"
                                        >
                                            <svg v-if="processingId === order.id" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                                            <svg v-else class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                            <span class="text-[10px] font-black uppercase hidden group-hover:inline">{{ cart.language === 'ar' ? 'إنشاء' : 'Generate' }}</span>
                                        </button>

                                        <template v-else>
                                            <button @click="downloadPDF(order.invoice.secure_token)" class="p-2 hover:bg-slate-50 rounded-xl text-slate-300 hover:text-indigo-600 transition-all group" title="Download">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                            </button>
                                            <button @click="previewInvoice(order.id)" class="p-2 hover:bg-slate-50 rounded-xl text-slate-300 hover:text-emerald-600 transition-all" title="Preview">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                            </button>
                                            <button @click="sendEmail(order.invoice.id)" class="p-2 hover:bg-slate-50 rounded-xl text-slate-300 hover:text-amber-600 transition-all" title="Send to Email">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                            </button>
                                            <button @click="generateInvoice(order.id)" class="p-2 hover:bg-red-50 rounded-xl text-slate-300 hover:text-red-600 transition-all font-black text-[10px]" title="Regenerate">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                                            </button>
                                        </template>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination Placeholder -->
            <div class="flex justify-center pt-6">
                <!-- Simple pagination for now since we are using Laravel pagination -->
                <div class="flex gap-2">
                    <button 
                        v-for="link in orders.links" 
                        :key="link.label"
                        @click="router.visit(link.url)"
                        v-html="link.label"
                        :disabled="!link.url || link.active"
                        class="px-4 py-2 rounded-xl text-xs font-black transition-all"
                        :class="link.active ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-100' : 'bg-white border border-slate-100 text-slate-400 hover:bg-slate-50'"
                    ></button>
                </div>
            </div>
        </div>
</AdminLayout>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.5s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
