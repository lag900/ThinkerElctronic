<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { cart } from '@/Stores/CartStore';
import { system } from '@/Stores/SystemStore';
import { ref } from 'vue';

const props = defineProps({
    packages: Array,
});

const showForm = ref(false);
const editingPackage = ref(null);

const form = useForm({
    name: '',
    points: '',
    price: '',
    payment_link: '',
    active: true,
    sort_order: 0,
});

const openCreateForm = () => {
    editingPackage.value = null;
    form.reset();
    form.sort_order = props.packages.length > 0 ? Math.max(...props.packages.map(p => p.sort_order)) + 1 : 1;
    showForm.value = true;
};

const openEditForm = (pkg) => {
    editingPackage.value = pkg;
    form.name = pkg.name || '';
    form.points = pkg.points;
    form.price = pkg.price;
    form.payment_link = pkg.payment_link || '';
    form.active = pkg.active ? true : false;
    form.sort_order = pkg.sort_order;
    showForm.value = true;
};

const submitForm = () => {
    if (editingPackage.value) {
        form.put(route('lms-packages.update', editingPackage.value.id), {
            onSuccess: () => {
                showForm.value = false;
                system.notify('LMS Package updated');
            },
        });
    } else {
        form.post(route('lms-packages.store'), {
            onSuccess: () => {
                showForm.value = false;
                system.notify('LMS Package created');
            },
        });
    }
};

const deletePackage = (pkg) => {
    if (confirm(`Remove package node ${pkg.points} PTS?`)) {
        router.delete(route('lms-packages.destroy', pkg.id), {
            onSuccess: () => system.notify('Package removed', 'error'),
        });
    }
};

const toggleActive = (pkg) => {
    router.put(route('lms-packages.update', pkg.id), {
        ...pkg,
        active: !pkg.active,
        _method: 'PUT'
    }, {
        onSuccess: () => system.notify(`Package ${!pkg.active ? 'activated' : 'deactivated'}`),
        preserveScroll: true
    });
};
</script>

<template>
    <Head title="LMS Points Management" />

    <AdminLayout>
        <template #header>{{ cart.language === 'ar' ? 'إدارة نقاط LMS' : 'LMS Points Infrastructure' }}</template>

        <div class="space-y-12">
            <!-- Tactical Controls -->
            <div class="flex justify-between items-center bg-white p-10 rounded-[50px] border border-slate-50 shadow-[0_20px_60px_rgba(0,0,0,0.02)]">
                <div>
                    <h3 class="text-2xl font-black text-[#222] tracking-tighter">Package Cluster Management</h3>
                    <p class="text-[10px] font-black text-slate-300 uppercase tracking-[0.3em] mt-2">Configure smart printing value nodes</p>
                </div>
                <button 
                    @click="openCreateForm"
                    class="bg-[#222] hover:bg-brand-500 text-white px-10 py-5 rounded-[28px] font-black uppercase tracking-widest text-[11px] transition-all shadow-xl shadow-black/10 active:scale-95"
                >
                    + Initialize Package
                </button>
            </div>

            <!-- Packages Data Stream -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                <div v-for="pkg in packages" :key="pkg.id" 
                    class="bg-white rounded-[50px] border border-slate-50 shadow-[0_30px_60px_rgba(0,0,0,0.02)] overflow-hidden group hover:shadow-[0_40px_80px_rgba(0,0,0,0.05)] transition-all duration-500 relative"
                    :class="!pkg.active && 'opacity-60 saturate-50 bg-slate-50'"
                >
                    <div class="p-10 text-center">
                        <div class="w-20 h-20 bg-slate-50 rounded-3xl mx-auto flex items-center justify-center font-black text-brand-500 text-2xl mb-6 shadow-inner group-hover:bg-brand-500 group-hover:text-white transition-all duration-500">
                            {{ pkg.points }}
                        </div>
                        <h4 class="text-xl font-black text-[#222] tracking-tight">{{ pkg.points }} PTS</h4>
                        <p class="text-brand-500 font-bold mt-2 text-lg">{{ pkg.price }}</p>
                        <p v-if="pkg.name" class="text-[10px] uppercase font-black tracking-widest text-slate-300 mt-2">{{ pkg.name }}</p>
                        
                        <div class="mt-8 pt-8 border-t border-slate-50 space-y-4 text-right" :dir="cart.language === 'ar' ? 'rtl' : 'ltr'">
                            <div class="flex items-center justify-between text-[10px] font-black uppercase tracking-widest text-slate-400">
                                <span>Status</span>
                                <button @click="toggleActive(pkg)" :class="pkg.active ? 'text-emerald-500' : 'text-slate-300'" class="hover:underline">
                                    {{ pkg.active ? 'Active Node' : 'Suspended' }}
                                </button>
                            </div>
                            <div class="flex items-center justify-between text-[10px] font-black uppercase tracking-widest text-slate-400">
                                <span>Payment Link</span>
                                <span :class="pkg.payment_link ? 'text-blue-500' : 'text-red-400'">
                                    {{ pkg.payment_link ? 'Configured' : 'Link Not Set' }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-[10px] font-black uppercase tracking-widest text-slate-400">
                                <span>Rank</span>
                                <span>{{ pkg.sort_order }}</span>
                            </div>
                        </div>

                        <div class="flex gap-4 mt-8">
                            <button @click="openEditForm(pkg)" class="flex-1 bg-slate-50 hover:bg-white border border-slate-100 p-4 rounded-2xl text-slate-400 hover:text-brand-500 transition-all font-black text-[10px] uppercase tracking-widest">Edit</button>
                            <button @click="deletePackage(pkg)" class="p-4 bg-slate-50 hover:bg-red-50 border border-slate-100 rounded-2xl text-slate-400 hover:text-red-500 transition-all">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Package Modal -->
        <div v-if="showForm" class="fixed inset-0 z-[100] flex items-center justify-center p-8">
            <div class="absolute inset-0 bg-[#222]/80 backdrop-blur-md" @click="showForm = false"></div>
            <div class="bg-white w-full max-w-xl rounded-[60px] relative z-10 overflow-hidden shadow-2xl border border-white/20 animate-slide-in">
                <div class="p-12">
                    <h3 class="text-3xl font-black text-[#222] tracking-tighter mb-10">
                        {{ editingPackage ? 'Update Package Node' : 'Initialize Package Node' }}
                    </h3>

                    <form @submit.prevent="submitForm" class="space-y-8">
                        <div class="grid grid-cols-2 gap-8">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-300 ml-6">Points Value</label>
                                <input v-model="form.points" type="number" class="w-full bg-slate-50 border-none rounded-[30px] px-8 py-5 text-sm font-bold focus:ring-2 focus:ring-brand-500/20 shadow-inner" required>
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-300 ml-6">Monetary Price</label>
                                <input v-model="form.price" type="text" placeholder="e.g. 25 جنيه" class="w-full bg-slate-50 border-none rounded-[30px] px-8 py-5 text-sm font-bold focus:ring-2 focus:ring-brand-500/20 shadow-inner" required>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-300 ml-6">Package Trace Name (Optional)</label>
                            <input v-model="form.name" type="text" placeholder="Internal name for tracking" class="w-full bg-slate-50 border-none rounded-[30px] px-8 py-5 text-sm font-bold focus:ring-2 focus:ring-brand-500/20 shadow-inner">
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-300 ml-6">External Payment Link</label>
                            <input v-model="form.payment_link" type="url" placeholder="https://..." class="w-full bg-slate-50 border-none rounded-[30px] px-8 py-5 text-sm font-bold focus:ring-2 focus:ring-brand-500/20 shadow-inner">
                        </div>

                        <div class="grid grid-cols-2 gap-8">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-300 ml-6">Deployment Rank</label>
                                <input v-model="form.sort_order" type="number" class="w-full bg-slate-50 border-none rounded-[30px] px-8 py-5 text-sm font-bold focus:ring-2 focus:ring-brand-500/20 shadow-inner" required>
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-300 ml-6">Operational Status</label>
                                <div class="flex items-center gap-4 bg-slate-50 rounded-[30px] px-8 py-5 h-[60px]">
                                    <input v-model="form.active" type="checkbox" class="w-6 h-6 rounded-lg border-none bg-white text-brand-500 focus:ring-brand-500 focus:ring-offset-0">
                                    <span class="text-sm font-bold text-[#222]">Active for users</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-4 pt-4">
                            <button type="button" @click="showForm = false" class="flex-1 bg-slate-100 rounded-[30px] py-5 font-black uppercase tracking-widest text-[11px] text-[#222] hover:bg-slate-200 transition-all">Abort</button>
                            <button 
                                type="submit" 
                                :disabled="form.processing"
                                class="flex-1 bg-brand-500 text-white rounded-[30px] py-5 font-black uppercase tracking-widest text-[11px] shadow-xl shadow-brand-500/20 hover:bg-brand-600 transition-all disabled:opacity-50"
                            >
                                {{ editingPackage ? 'Synchronize Package' : 'Deploy Package' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
