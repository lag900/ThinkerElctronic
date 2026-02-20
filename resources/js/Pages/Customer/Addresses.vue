<script setup>
import CustomerLayout from '@/Layouts/CustomerLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Addresses" />
    <CustomerLayout>
        <div class="space-y-8">
            <div>
                <h3 class="text-xl font-black text-slate-900 tracking-tight">Deployment Locations</h3>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Manage your delivery vector nodes</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white p-8 rounded-[32px] border-2 border-dashed border-slate-100 flex flex-col items-center justify-center text-slate-300 hover:border-brand-500 hover:text-brand-500 transition-all cursor-pointer group">
                    <span class="text-4xl mb-4 group-hover:scale-125 transition-transform">+</span>
                    <p class="text-[10px] font-black uppercase tracking-widest">Add New Node</p>
                </div>
            </div>
        </div>
    </CustomerLayout>
</template>
