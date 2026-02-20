<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { cart } from '@/Stores/CartStore';
import { system } from '@/Stores/SystemStore';
import { ref } from 'vue';

const props = defineProps({
    roles: Array,
    permissions: Object // Grouped
});

const showForm = ref(false);
const editingRole = ref(null);

const form = useForm({
    name: '',
    label: '',
    description: '',
    permissions: [],
});

const openCreateForm = () => {
    editingRole.value = null;
    form.reset();
    showForm.value = true;
};

const openEditForm = (role) => {
    editingRole.value = role;
    form.name = role.name;
    form.label = role.label;
    form.description = role.description || '';
    form.permissions = role.permissions ? role.permissions.map(p => p.id) : [];
    showForm.value = true;
};

const submitForm = () => {
    if (editingRole.value) {
        form.put(route('admin.roles.update', editingRole.value.id), {
            onSuccess: () => {
                showForm.value = false;
                system.notify('Security role updated');
            },
        });
    } else {
        form.post(route('admin.roles.store'), {
            onSuccess: () => {
                showForm.value = false;
                system.notify('Security role created');
            },
        });
    }
};

const togglePermission = (id) => {
    const index = form.permissions.indexOf(id);
    if (index > -1) {
        form.permissions.splice(index, 1);
    } else {
        form.permissions.push(id);
    }
};
</script>

<template>
    <Head title="Security Access Matrix" />

    <AdminLayout>
        <template #header>{{ cart.language === 'ar' ? 'أدوار النظام' : 'Security Access Matrix' }}</template>

        <div class="space-y-12">
            <div class="flex justify-between items-center bg-white p-10 rounded-[50px] border border-slate-50 shadow-[0_20px_60px_rgba(0,0,0,0.02)]">
                <div>
                    <h3 class="text-3xl font-black text-[#222] tracking-tighter">Access Protocols</h3>
                    <p class="text-[10px] font-black text-slate-300 uppercase tracking-[0.4em] mt-2">Role Based Access Control</p>
                </div>
                <button 
                    @click="openCreateForm"
                    class="bg-[#222] hover:bg-brand-500 text-white px-8 py-4 rounded-[28px] font-black uppercase tracking-widest text-[11px] transition-all shadow-xl shadow-black/10 active:scale-95"
                >
                    + Define New Protocol
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div v-for="role in roles" :key="role.id" class="bg-white p-8 rounded-[40px] border border-slate-50 shadow-[0_20px_60px_rgba(0,0,0,0.02)] hover:shadow-[0_40px_100px_rgba(0,0,0,0.05)] transition-all group">
                    <div class="flex justify-between items-start mb-6">
                        <div class="w-12 h-12 bg-slate-50 rounded-2xl flex items-center justify-center font-black text-brand-500 group-hover:bg-brand-500 group-hover:text-white transition-all">
                            {{ role.name.charAt(0).toUpperCase() }}
                        </div>
                        <div class="flex gap-2">
                            <button @click="openEditForm(role)" class="text-slate-300 hover:text-brand-500 transition-all">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                            </button>
                        </div>
                    </div>
                    
                    <h4 class="text-xl font-black text-[#222] tracking-tight">{{ role.label }}</h4>
                    <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest mt-2">{{ role.name }}</p>
                    
                    <p class="text-xs text-slate-400 font-medium mt-6 line-clamp-2 min-h-[3em]">{{ role.description || 'No specialized mission description provided.' }}</p>

                    <div class="mt-8 pt-8 border-t border-slate-50 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="text-lg font-black text-[#222]">{{ role.users_count || 0 }}</span>
                            <span class="text-[9px] font-black uppercase text-slate-300 tracking-widest">Nodes Active</span>
                        </div>
                        <span class="text-[9px] font-black uppercase text-brand-500 bg-brand-50 px-3 py-1 rounded-full">{{ role.permissions_count || 0 }} Protocols</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Role Configuration Matrix (Modal) -->
        <div v-if="showForm" class="fixed inset-0 z-[100] flex items-center justify-center p-8">
            <div class="absolute inset-0 bg-[#222]/80 backdrop-blur-md" @click="showForm = false"></div>
            <div class="bg-white w-full max-w-4xl rounded-[60px] relative z-10 overflow-hidden shadow-2xl border border-white/20 animate-slide-in h-[85vh] flex flex-col">
                <div class="p-12 overflow-y-auto flex-1">
                    <h3 class="text-3xl font-black text-[#222] tracking-tighter mb-10">Access Protocol Configuration</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                        <div class="space-y-8">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-300 ml-6">Unique Identifier</label>
                                <input v-model="form.name" type="text" placeholder="e.g. system_admin" class="w-full bg-slate-50 border-none rounded-[30px] px-8 py-5 text-sm font-bold focus:ring-2 focus:ring-brand-500/20" required>
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-300 ml-6">Display Label</label>
                                <input v-model="form.label" type="text" placeholder="e.g. System Administrator" class="w-full bg-slate-50 border-none rounded-[30px] px-8 py-5 text-sm font-bold focus:ring-2 focus:ring-brand-500/20" required>
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-300 ml-6">Mission Description</label>
                                <textarea v-model="form.description" class="w-full bg-slate-50 border-none rounded-[30px] px-8 py-5 text-sm font-bold focus:ring-2 focus:ring-brand-500/20 h-32"></textarea>
                            </div>
                        </div>

                        <div class="space-y-8">
                            <label class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-300 ml-6">Permission Protocols</label>
                            <div class="space-y-6 max-h-[500px] overflow-y-auto pr-4 scrollbar-hide">
                                <div v-for="(groupPerms, group) in permissions" :key="group" class="space-y-3">
                                    <h5 class="text-[9px] font-black uppercase tracking-[0.2em] text-[#222] bg-slate-50 px-4 py-1.5 rounded-full inline-block">{{ group }}</h5>
                                    <div class="grid grid-cols-1 gap-2">
                                        <div 
                                            v-for="perm in groupPerms" 
                                            :key="perm.id"
                                            @click="togglePermission(perm.id)"
                                            class="flex items-center justify-between p-4 rounded-2xl cursor-pointer transition-all border"
                                            :class="form.permissions.includes(perm.id) ? 'bg-brand-50 border-brand-500/20' : 'bg-white border-slate-50 hover:bg-slate-50'"
                                        >
                                            <span class="text-[11px] font-bold text-[#222]">{{ perm.label }}</span>
                                            <div class="w-5 h-5 rounded-md border flex items-center justify-center transition-all" :class="form.permissions.includes(perm.id) ? 'bg-brand-500 border-brand-500' : 'border-slate-200'">
                                                <svg v-if="form.permissions.includes(perm.id)" class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-12 bg-slate-50/50 border-t border-slate-100/50 flex gap-4">
                    <button type="button" @click="showForm = false" class="flex-1 bg-white border border-slate-100 rounded-[30px] py-5 font-black uppercase tracking-widest text-[11px] text-[#222] hover:bg-slate-50 transition-all shadow-sm">Cancel</button>
                    <button 
                        @click="submitForm"
                        :disabled="form.processing"
                        class="flex-1 bg-brand-500 text-white rounded-[30px] py-5 font-black uppercase tracking-widest text-[11px] shadow-xl shadow-brand-500/20 hover:bg-brand-600 transition-all disabled:opacity-50"
                    >
                        {{ editingRole ? 'Update Security Protocol' : 'Deploy Protocol' }}
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
