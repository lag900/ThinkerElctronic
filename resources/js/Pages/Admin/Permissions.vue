<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { cart } from '@/Stores/CartStore';
import { system } from '@/Stores/SystemStore';
import { ref } from 'vue';

const props = defineProps({
    permissions: Object
});

const showForm = ref(false);
const form = useForm({
    name: '',
    label: '',
    group: '',
});

const submitForm = () => {
    form.post(route('admin.permissions.store'), {
        onSuccess: () => {
            showForm.value = false;
            form.reset();
            system.notify('Hardware protocol permission granted.');
        },
    });
};
</script>

<template>
    <Head title="Security Matrix Protocols" />

    <AdminLayout>
        <template #header>{{ cart.language === 'ar' ? 'تصاريح النظام' : 'Security Matrix Protocols' }}</template>

        <div class="space-y-12">
            <div class="flex justify-between items-center bg-white p-10 rounded-[50px] border border-slate-50 shadow-[0_20px_60px_rgba(0,0,0,0.02)]">
                <div>
                    <h3 class="text-3xl font-black text-[#222] tracking-tighter">Protocol Definitions</h3>
                    <p class="text-[10px] font-black text-slate-300 uppercase tracking-[0.4em] mt-2">Core System Permissions</p>
                </div>
                <button 
                    @click="showForm = true"
                    class="bg-[#222] hover:bg-brand-500 text-white px-8 py-4 rounded-[28px] font-black uppercase tracking-widest text-[11px] transition-all shadow-xl shadow-black/10 active:scale-95"
                >
                    + Define New Permission
                </button>
            </div>

            <div class="space-y-12">
                <div v-for="(groupPerms, group) in permissions" :key="group" class="space-y-6">
                    <div class="flex items-center gap-4">
                        <h4 class="text-xl font-black text-[#222] tracking-tight uppercase">{{ group }}</h4>
                        <div class="h-[1px] bg-slate-100 flex-1"></div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div v-for="perm in groupPerms" :key="perm.id" class="bg-white p-6 rounded-[32px] border border-slate-50 shadow-sm hover:shadow-md transition-all group">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-8 h-8 bg-slate-50 rounded-xl flex items-center justify-center font-black text-[10px] text-brand-500 group-hover:bg-brand-500 group-hover:text-white transition-all">
                                    P
                                </div>
                                <span class="text-[8px] font-black uppercase text-slate-300 tracking-[0.2em]">Matrix Node</span>
                            </div>
                            <h5 class="text-[13px] font-black text-[#222]">{{ perm.label }}</h5>
                            <code class="text-[9px] font-bold text-slate-400 mt-2 block">{{ perm.name }}</code>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Permission Definition Modal -->
        <div v-if="showForm" class="fixed inset-0 z-[100] flex items-center justify-center p-8">
            <div class="absolute inset-0 bg-[#222]/80 backdrop-blur-md" @click="showForm = false"></div>
            <div class="bg-white w-full max-w-xl rounded-[60px] relative z-10 overflow-hidden shadow-2xl border border-white/20 animate-slide-in">
                <div class="p-12">
                    <h3 class="text-3xl font-black text-[#222] tracking-tighter mb-10">Define Core Permission</h3>

                    <form @submit.prevent="submitForm" class="space-y-8">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-300 ml-6">Key Mapping (e.g. users.edit)</label>
                            <input v-model="form.name" type="text" class="w-full bg-slate-50 border-none rounded-[30px] px-8 py-5 text-sm font-bold focus:ring-2 focus:ring-brand-500/20" required>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-300 ml-6">Protocol Label</label>
                            <input v-model="form.label" type="text" placeholder="e.g. Modify User Nodes" class="w-full bg-slate-50 border-none rounded-[30px] px-8 py-5 text-sm font-bold focus:ring-2 focus:ring-brand-500/20" required>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-300 ml-6">System Module Group</label>
                            <input v-model="form.group" type="text" placeholder="e.g. Identity" class="w-full bg-slate-50 border-none rounded-[30px] px-8 py-5 text-sm font-bold focus:ring-2 focus:ring-brand-500/20" required>
                        </div>

                        <div class="flex gap-4 pt-4">
                            <button type="button" @click="showForm = false" class="flex-1 bg-slate-100 rounded-[30px] py-5 font-black uppercase tracking-widest text-[11px] text-[#222] hover:bg-slate-200 transition-all">Cancel</button>
                            <button 
                                type="submit" 
                                :disabled="form.processing"
                                class="flex-1 bg-brand-500 text-white rounded-[30px] py-5 font-black uppercase tracking-widest text-[11px] shadow-xl shadow-brand-500/20 hover:bg-brand-600 transition-all disabled:opacity-50"
                            >
                                Grant Permission
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
