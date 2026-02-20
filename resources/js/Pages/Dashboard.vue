<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    orders: Array
});

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};
</script>

<template>
    <Head title="Orders Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-2xl font-black text-gray-900 tracking-tight">
                My Orders / طلباتي
            </h2>
        </template>

        <div class="py-12 bg-gray-50/50 min-h-screen">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-8">
                
                <!-- Welcome Card -->
                <div class="bg-white overflow-hidden shadow-premium sm:rounded-[32px] border border-gray-100 p-10">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-3xl font-black text-gray-900 mb-2">Welcome back!</h3>
                            <p class="text-gray-500 font-medium">Manage your orders and download invoices.</p>
                        </div>
                        <div class="hidden md:block">
                            <div class="w-16 h-16 rounded-2xl bg-brand-50 flex items-center justify-center">
                                <svg class="w-8 h-8 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Orders Table -->
                <div class="bg-white shadow-premium sm:rounded-[40px] border border-gray-100 overflow-hidden">
                    <div class="p-8 border-b border-gray-50 flex items-center justify-between">
                        <h4 class="text-lg font-black text-gray-900 uppercase tracking-widest">Order History</h4>
                        <span class="px-4 py-1.5 bg-gray-100 text-gray-600 text-[10px] font-black uppercase tracking-widest rounded-full">
                            {{ orders.length }} Orders Found
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50/50">
                                <tr>
                                    <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-gray-400">Order ID</th>
                                    <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-gray-400">Date</th>
                                    <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-gray-400">Status</th>
                                    <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-gray-400">Total</th>
                                    <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-gray-400">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <tr v-for="order in orders" :key="order.id" class="hover:bg-gray-50/30 transition-colors">
                                    <td class="px-8 py-6">
                                        <span class="text-sm font-black text-gray-900">#{{ order.order_number }}</span>
                                    </td>
                                    <td class="px-8 py-6">
                                        <span class="text-sm font-bold text-gray-500">{{ formatDate(order.created_at) }}</span>
                                    </td>
                                    <td class="px-8 py-6">
                                        <span class="inline-flex px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest"
                                              :class="order.status === 'pending' ? 'bg-amber-50 text-amber-600' : 'bg-green-50 text-green-600'">
                                            {{ order.status }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-6">
                                        <span class="text-sm font-black text-brand-500">{{ order.total }} EGP</span>
                                    </td>
                                    <td class="px-8 py-6">
                                        <a :href="route('order.invoice', order.order_number)" target="_blank"
                                           class="inline-flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-brand-500 transition-colors"
                                        >
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                            Invoice
                                        </a>
                                    </td>
                                </tr>
                                <tr v-if="orders.length === 0">
                                    <td colspan="5" class="px-8 py-20 text-center">
                                        <p class="text-sm font-bold text-gray-400 uppercase tracking-widest">No orders yet.</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.shadow-premium {
    box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.02), 0 8px 10px -6px rgb(0 0 0 / 0.02);
}
</style>
