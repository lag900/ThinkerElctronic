<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    stats: Object
});

const activeFilter = ref('month');
const showExportMenu = ref(false);

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-EG', {
        style: 'currency',
        currency: 'EGP',
        minimumFractionDigits: 2
    }).format(value);
};

const handleExport = (filter) => {
    window.location.href = route('admin.finances.export', { filter });
    showExportMenu.value = false;
};

// Advanced Status Analytics
const analyticsItems = computed(() => {
    if (!props.stats || !props.stats.today || !props.stats.expected) return [];
    
    return [
        { label: 'Completed Revenue Today', value: props.stats.today.revenue || 0, color: 'text-emerald-500', icon: '💰' },
        { label: 'Pending Orders Value', value: props.stats.expected.pending_revenue || 0, color: 'text-amber-500', icon: '🟡' },
        { label: 'Net Profit Today', value: props.stats.today.net_profit || 0, color: 'text-brand-500', icon: '🟢' },
        { label: 'Expenses Today', value: props.stats.today.expenses || 0, color: 'text-red-500', icon: '🔴' },
        { label: 'COD Expected Cash', value: props.stats.expected.cod_expected || 0, color: 'text-blue-500', icon: '💵' },
        { label: 'Online Paid Total', value: props.stats.expected.online_paid || 0, color: 'text-indigo-500', icon: '🌐' },
    ];
});

</script>

<template>
    <AdminLayout>
        <Head title="ERP Enterprise Dashboard" />

        <div v-if="stats" class="max-w-[1600px] mx-auto space-y-12 pb-20">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div>
                    <h2 class="text-4xl font-black text-[#111] tracking-tighter uppercase">Enterprise Intelligence</h2>
                    <p class="text-xs font-bold uppercase tracking-[0.3em] text-slate-400 mt-2">Real-time Profit & Revenue Monitoring</p>
                </div>

                <div class="relative">
                    <button @click="showExportMenu = !showExportMenu" class="h-14 bg-[#111] text-white px-10 rounded-2xl text-[11px] font-black uppercase tracking-widest shadow-xl flex items-center gap-3 hover:scale-105 transition-all">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        Export Financial Report
                    </button>
                    <!-- Export Dropdown -->
                    <div v-if="showExportMenu" class="absolute right-0 mt-4 w-56 bg-white rounded-2xl shadow-2xl border border-slate-100 z-50 overflow-hidden animate-in fade-in slide-in-from-top-2">
                        <button @click="handleExport('today')" class="w-full text-left px-6 py-4 text-xs font-black uppercase tracking-widest hover:bg-slate-50 transition-colors">Today's Report</button>
                        <button @click="handleExport('week')" class="w-full text-left px-6 py-4 text-xs font-black uppercase tracking-widest hover:bg-slate-50 transition-colors">This Week</button>
                        <button @click="handleExport('month')" class="w-full text-left px-6 py-4 text-xs font-black uppercase tracking-widest hover:bg-slate-50 transition-colors border-b border-slate-50">This Month</button>
                        <div class="p-4 bg-slate-50">
                             <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-2">Detailed XLSX (SAP Standard)</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dashboard Analytics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6">
                <div v-for="item in analyticsItems" :key="item.label" class="bg-white p-8 rounded-[32px] border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all group">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-2xl">{{ item.icon }}</span>
                        <div class="w-2 h-2 rounded-full" :class="item.color.replace('text', 'bg')"></div>
                    </div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">{{ item.label }}</p>
                    <p class="text-2xl font-black tracking-tighter" :class="item.color">{{ formatCurrency(item.value) }}</p>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                
                <!-- Order State Breakdown -->
                <div class="lg:col-span-8 space-y-8">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-brand-500/10 flex items-center justify-center text-brand-500">
                             <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                        </div>
                        <h3 class="text-2xl font-black text-[#111] tracking-tight">Order Lifecycle Financials</h3>
                    </div>

                    <div class="bg-white rounded-[40px] border border-slate-100 shadow-sm overflow-hidden">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50/50">
                                    <th class="p-8 text-[11px] font-black uppercase tracking-[0.2em] text-slate-400">Order Status</th>
                                    <th class="p-8 text-[11px] font-black uppercase tracking-[0.2em] text-slate-400">Orders</th>
                                    <th class="p-8 text-[11px] font-black uppercase tracking-[0.2em] text-slate-400">Total Money</th>
                                    <th class="p-8 text-[11px] font-black uppercase tracking-[0.2em] text-slate-400">Profit (Gross)</th>
                                    <th class="p-8 text-[11px] font-black uppercase tracking-[0.2em] text-slate-400">Payment</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                <tr v-for="row in (stats.breakdown || [])" :key="(row.status || '') + (row.payment_method || '')" class="hover:bg-slate-50/50 transition-colors">
                                    <td class="p-8">
                                        <span class="px-4 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest inline-block" 
                                              :class="{
                                                  'bg-amber-50 text-amber-600': row.status === 'pending',
                                                  'bg-blue-50 text-blue-600': row.status === 'processing',
                                                  'bg-emerald-50 text-emerald-600': row.status === 'completed',
                                                  'bg-red-50 text-red-600': ['cancelled', 'failed'].includes(row.status)
                                              }">
                                            {{ row.status }}
                                        </span>
                                    </td>
                                    <td class="p-8 font-black text-slate-600">{{ row.count }}</td>
                                    <td class="p-8 font-black text-[#111]">{{ formatCurrency(row.total_money) }}</td>
                                    <td class="p-8 font-black" :class="row.status === 'completed' ? 'text-emerald-500' : 'text-slate-400'">
                                        {{ formatCurrency(row.gross_profit) }}
                                    </td>
                                    <td class="p-8">
                                        <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ row.payment_method }}</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Widgets Side -->
                <div class="lg:col-span-4 space-y-10">
                    
                    <!-- Pending Cash Monitor -->
                    <div class="bg-[#111] rounded-[40px] p-10 text-white shadow-2xl relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-bl-[8rem] -mr-10 -mt-10"></div>
                        <h4 class="text-xl font-black uppercase tracking-tight mb-8 flex items-center gap-3">
                            <span class="text-brand-500">●</span> Pending Cash Monitor
                        </h4>
                        
                        <div class="space-y-8">
                            <div class="flex justify-between items-end border-b border-white/10 pb-6">
                                <div>
                                     <p class="text-[10px] font-black uppercase tracking-widest text-white/40 mb-1">Total Pending Value</p>
                                    <p class="text-3xl font-black tracking-tighter">{{ formatCurrency(stats?.expected?.pending_revenue || 0) }}</p>
                                </div>
                                <span class="text-[10px] font-black text-brand-500 uppercase tracking-widest mb-1">Risk Exposure</span>
                            </div>

                            <div class="grid grid-cols-2 gap-8">
                                <div>
                                    <p class="text-[9px] font-black uppercase tracking-widest text-white/30 mb-1">Pending COD</p>
                                    <p class="text-lg font-black">{{ formatCurrency(stats?.expected?.cod_expected || 0) }}</p>
                                </div>
                                <div>
                                    <p class="text-[9px] font-black uppercase tracking-widest text-white/30 mb-1">Expected Profit</p>
                                    <p class="text-lg font-black text-emerald-400">{{ formatCurrency(stats?.expected?.pending_profit || 0) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Type Analytics -->
                    <div class="bg-white rounded-[40px] border border-slate-100 p-10 shadow-sm space-y-8">
                        <h4 class="text-xl font-black uppercase tracking-tight text-[#111]">Channel Analytics</h4>
                        
                        <div class="space-y-6">
                             <div class="flex items-center justify-between">
                                 <div class="flex items-center gap-4">
                                     <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-lg">🚚</div>
                                     <span class="text-xs font-black uppercase tracking-widest text-slate-400">COD Orders</span>
                                 </div>
                                 <span class="font-black text-[#111]">{{ formatCurrency(stats?.expected?.cod_expected || 0) }}</span>
                             </div>

                             <div class="flex items-center justify-between">
                                 <div class="flex items-center gap-4">
                                     <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-lg">💳</div>
                                     <span class="text-xs font-black uppercase tracking-widest text-slate-400">Online Paid</span>
                                 </div>
                                 <span class="font-black text-[#111]">{{ formatCurrency(stats?.expected?.online_paid || 0) }}</span>
                             </div>

                             <div class="pt-6 border-t border-slate-50">
                                 <div class="flex justify-between mb-3">
                                     <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Cash vs Online Ratio</span>
                                     <span class="text-[10px] font-black text-brand-500">
                                         {{ (stats?.expected?.pending_revenue || 0) > 0 ? Math.round(((stats?.expected?.cod_expected || 0) / (stats?.expected?.pending_revenue || 1)) * 100) : 0 }}% COD
                                     </span>
                                 </div>
                                 <div class="h-2 w-full bg-slate-100 rounded-full overflow-hidden flex">
                                     <div class="bg-brand-500 h-full" :style="{ width: ((stats?.expected?.pending_revenue || 0) > 0 ? ((stats?.expected?.cod_expected || 0) / (stats?.expected?.pending_revenue || 1)) * 100 : 0) + '%' }"></div>
                                     <div class="bg-blue-500 h-full flex-1"></div>
                                 </div>
                                 <p class="text-[9px] font-bold text-slate-300 mt-2 uppercase tracking-widest">* Based on current pending cycle</p>
                             </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.animate-in {
    animation: fadeIn 0.5s ease-out;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
