<script setup>
import CustomerLayout from '@/Layouts/CustomerLayout.vue';
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';

// Mock order data
const orders = [
    { id: 1, date: '20 Feb 2026', total: 1540.00, status: 'Delivered', number: '#ORD-9912A' },
    { id: 2, date: '15 Jan 2026', total: 420.00, status: 'Delivered', number: '#ORD-8821Z' },
    { id: 3, date: '10 Dec 2025', total: 8900.00, status: 'Delivered', number: '#ORD-7711X' },
];
</script>

<template>
    <Head title="Order History" />

    <CustomerLayout>
        <div class="space-y-8">
            <div>
                <h3 class="text-xl font-black text-slate-900 tracking-tight">Transmission History</h3>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Review your resource acquisitions</p>
            </div>

            <div class="space-y-4">
                <div v-for="order in orders" :key="order.id" class="bg-white p-6 rounded-[32px] border border-slate-100 shadow-sm hover:shadow-xl transition-all group flex items-center justify-between">
                    <div class="flex items-center gap-6">
                        <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-400">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase text-brand-500 tracking-widest mb-1">{{ order.number }}</p>
                            <h4 class="font-black text-slate-900 tracking-tight">{{ order.date }}</h4>
                        </div>
                    </div>

                    <div class="flex items-center gap-12">
                        <div class="text-right">
                            <p class="text-sm font-black text-slate-900">{{ order.total.toFixed(2) }} EGP</p>
                            <span class="text-[9px] font-black uppercase tracking-widest text-emerald-500">{{ order.status }}</span>
                        </div>
                        
                        <button class="bg-slate-900 text-white px-6 py-3 rounded-2xl text-[9px] font-black uppercase tracking-widest hover:bg-brand-500 transition-all shadow-lg shadow-slate-200">
                            Download Invoice
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </CustomerLayout>
</template>
