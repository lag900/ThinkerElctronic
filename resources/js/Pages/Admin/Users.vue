<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { cart } from '@/Stores/CartStore';
import { system } from '@/Stores/SystemStore';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    users: Object,
    roles: Array,
    filters: Object
});

const search = ref(props.filters.search || '');
const roleFilter = ref(props.filters.role_id || '');
const showForm = ref(false);
const editingUser = ref(null);

const form = useForm({
    name: '',
    email: '',
    password: '',
    role_id: '',
});

const openCreateForm = () => {
    editingUser.value = null;
    form.reset();
    showForm.value = true;
};

const openEditForm = (user) => {
    editingUser.value = user;
    form.name = user.name;
    form.email = user.email;
    form.role_id = user.role_id;
    form.password = '';
    showForm.value = true;
};

const submitForm = () => {
    if (editingUser.value) {
        form.put(route('admin.users.update', editingUser.value.id), {
            onSuccess: () => {
                showForm.value = false;
                system.notify('User updated successfully');
            },
        });
    } else {
        form.post(route('admin.users.store'), {
            onSuccess: () => {
                showForm.value = false;
                system.notify('User created successfully');
            },
        });
    }
};

const deleteUser = (user) => {
    if (confirm(`Terminate user node ${user.name}? This action is permanent.`)) {
        router.delete(route('admin.users.destroy', user.id), {
            onSuccess: () => system.notify('User node terminated', 'error'),
        });
    }
};

watch([search, roleFilter], debounce(() => {
    router.get(route('admin.users.index'), { 
        search: search.value, 
        role_id: roleFilter.value 
    }, { 
        preserveState: true, 
        replace: true 
    });
}, 300));
</script>

<template>
    <Head title="Enterprise User Matrix" />

    <AdminLayout>
        <template #header>{{ cart.language === 'ar' ? 'إدارة المستخدمين' : 'Enterprise User Matrix' }}</template>

        <div class="space-y-12">
            <!-- Tactical Controls -->
            <div class="flex justify-between items-center bg-white p-10 rounded-[50px] border border-slate-50 shadow-[0_20px_60px_rgba(0,0,0,0.02)]">
                <div class="flex gap-8 items-center flex-1">
                    <div class="relative flex-1 max-w-md">
                        <input 
                            v-model="search"
                            type="text" 
                            :placeholder="cart.language === 'ar' ? 'البحث عن مستخدم...' : 'Search hardware operators...'"
                            class="w-full bg-slate-50 border-none rounded-[30px] px-8 py-4 text-sm font-bold focus:ring-2 focus:ring-brand-500/20 transition-all"
                        >
                    </div>
                    <select 
                        v-model="roleFilter"
                        class="bg-slate-50 border-none rounded-[30px] px-8 py-4 text-sm font-bold focus:ring-2 focus:ring-brand-500/20 transition-all cursor-pointer"
                    >
                        <option value="">{{ cart.language === 'ar' ? 'جميع الأدوار' : 'All Designations' }}</option>
                        <option v-for="role in roles" :key="role.id" :value="role.id">{{ role.label }}</option>
                    </select>
                </div>
                <button 
                    @click="openCreateForm"
                    class="bg-[#222] hover:bg-brand-500 text-white px-8 py-4 rounded-[28px] font-black uppercase tracking-widest text-[11px] transition-all shadow-xl shadow-black/10 active:scale-95"
                >
                    + {{ cart.language === 'ar' ? 'مستخدم جديد' : 'Initialize New Node' }}
                </button>
            </div>

            <!-- User Data Stream -->
            <div class="bg-white rounded-[50px] border border-slate-50 shadow-[0_40px_80px_rgba(0,0,0,0.02)] overflow-hidden">
                <table class="w-full" :class="{'text-right': cart.language === 'ar', 'text-left': cart.language !== 'ar'}">
                    <thead>
                        <tr class="bg-slate-50/50 text-[10px] font-black uppercase tracking-[0.3em] text-slate-300">
                            <th class="px-10 py-10">Operator Node</th>
                            <th class="px-10 py-10">Designation</th>
                            <th class="px-10 py-10">Status</th>
                            <th class="px-10 py-10" :class="cart.language === 'ar' ? 'text-left' : 'text-right'">Protocols</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <tr v-for="user in users.data" :key="user.id" class="group hover:bg-slate-50/30 transition-all duration-500">
                            <td class="px-10 py-8">
                                <div class="flex items-center gap-6">
                                    <div class="w-14 h-14 bg-slate-50 rounded-2xl flex items-center justify-center font-black text-brand-500 border border-slate-100 group-hover:bg-white transition-all">
                                        {{ user.name.charAt(0) }}
                                    </div>
                                    <div>
                                        <h4 class="text-[15px] font-black text-[#222]">{{ user.name }}</h4>
                                        <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest mt-1">{{ user.email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-10 py-8">
                                <span class="inline-flex px-4 py-1.5 bg-slate-100 rounded-full text-[10px] font-black uppercase tracking-widest text-[#222]">
                                    {{ user.role_node?.label || 'Unassigned' }}
                                </span>
                            </td>
                            <td class="px-10 py-8">
                                <div class="flex items-center gap-2">
                                    <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
                                    <span class="text-[10px] font-black uppercase tracking-widest text-[#222]">Active</span>
                                </div>
                            </td>
                            <td class="px-10 py-8" :class="cart.language === 'ar' ? 'text-left' : 'text-right'">
                                <div class="flex gap-3 justify-end">
                                    <button @click="openEditForm(user)" class="p-4 bg-white border border-slate-100 hover:border-brand-500/30 rounded-2xl text-slate-400 hover:text-brand-500 transition-all shadow-sm">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                    </button>
                                    <button 
                                        v-if="user.id !== $page.props.auth.user.id"
                                        @click="deleteUser(user)" 
                                        class="p-4 bg-white border border-slate-100 hover:border-red-500/30 rounded-2xl text-slate-400 hover:text-red-500 transition-all shadow-sm"
                                    >
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Enterprise Node Modal -->
        <div v-if="showForm" class="fixed inset-0 z-[100] flex items-center justify-center p-8">
            <div class="absolute inset-0 bg-[#222]/80 backdrop-blur-md" @click="showForm = false"></div>
            <div class="bg-white w-full max-w-xl rounded-[60px] relative z-10 overflow-hidden shadow-2xl border border-white/20 animate-slide-in">
                <div class="p-12">
                    <h3 class="text-3xl font-black text-[#222] tracking-tighter mb-10">
                        {{ editingUser ? 'Modify User Node' : 'Deploy New User Node' }}
                    </h3>

                    <form @submit.prevent="submitForm" class="space-y-8">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-300 ml-6">Full Identity</label>
                            <input v-model="form.name" type="text" class="w-full bg-slate-50 border-none rounded-[30px] px-8 py-5 text-sm font-bold focus:ring-2 focus:ring-brand-500/20" required>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-300 ml-6">Email Address</label>
                            <input v-model="form.email" type="email" class="w-full bg-slate-50 border-none rounded-[30px] px-8 py-5 text-sm font-bold focus:ring-2 focus:ring-brand-500/20" required>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-300 ml-6">Access Designation</label>
                            <select v-model="form.role_id" class="w-full bg-slate-50 border-none rounded-[30px] px-8 py-5 text-sm font-bold focus:ring-2 focus:ring-brand-500/20 appearance-none" required>
                                <option value="" disabled>Select Role...</option>
                                <option v-for="role in roles" :key="role.id" :value="role.id">{{ role.label }}</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-300 ml-6">Encryption Key (Password)</label>
                            <input v-model="form.password" type="password" class="w-full bg-slate-50 border-none rounded-[30px] px-8 py-5 text-sm font-bold focus:ring-2 focus:ring-brand-500/20" :required="!editingUser" :placeholder="editingUser ? 'Leave blank to keep current' : ''">
                        </div>

                        <div class="flex gap-4 pt-4">
                            <button type="button" @click="showForm = false" class="flex-1 bg-slate-100 rounded-[30px] py-5 font-black uppercase tracking-widest text-[11px] text-[#222] hover:bg-slate-200 transition-all">Cancel</button>
                            <button 
                                type="submit" 
                                :disabled="form.processing"
                                class="flex-1 bg-brand-500 text-white rounded-[30px] py-5 font-black uppercase tracking-widest text-[11px] shadow-xl shadow-brand-500/20 hover:bg-brand-600 transition-all disabled:opacity-50"
                            >
                                {{ editingUser ? 'Update Node' : 'Deploy Node' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
