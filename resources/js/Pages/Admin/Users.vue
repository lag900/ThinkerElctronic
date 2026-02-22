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
const statusFilter = ref(props.filters.status || '');
const showForm = ref(false);
const editingUser = ref(null);

const form = useForm({
    name: '',
    email: '',
    password: '',
    role_id: '',
    status: 'active',
});

const openCreateForm = () => {
    editingUser.value = null;
    form.reset();
    form.status = 'active';
    showForm.value = true;
};

const openEditForm = (user) => {
    editingUser.value = user;
    form.name = user.name;
    form.email = user.email;
    form.role_id = user.role_id;
    form.status = user.status;
    form.password = '';
    showForm.value = true;
};

const submitForm = () => {
    if (editingUser.value) {
        form.put(route('admin.users.update', editingUser.value.id), {
            onSuccess: () => {
                showForm.value = false;
                system.notify(cart.language === 'ar' ? 'تم تحديث بيانات المستخدم' : 'User profile updated');
            },
        });
    } else {
        form.post(route('admin.users.store'), {
            onSuccess: () => {
                showForm.value = false;
                system.notify(cart.language === 'ar' ? 'تم إضافة المستخدم الجديد' : 'New user added successfully');
            },
        });
    }
};

const deleteUser = (user) => {
    if (confirm(cart.language === 'ar' ? `هل أنت متأكد من حذف (${user.name})؟` : `Delete user ${user.name}?`)) {
        router.delete(route('admin.users.destroy', user.id), {
            onSuccess: () => system.notify(cart.language === 'ar' ? 'تم حذف المستخدم' : 'User deleted', 'error'),
            onError: (err) => system.notify(err.error || 'Unauthorized', 'error')
        });
    }
};

watch([search, roleFilter, statusFilter], debounce(() => {
    router.get(route('admin.users.index'), { 
        search: search.value, 
        role_id: roleFilter.value,
        status: statusFilter.value 
    }, { 
        preserveState: true, 
        replace: true 
    });
}, 300));

const formatDate = (date) => {
    if (!date) return '---';
    return new Date(date).toLocaleDateString('en-GB', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script>

<template>
    <Head :title="cart.language === 'ar' ? 'إدارة المستخدمين' : 'User Management'" />
    <AdminLayout>
        <template #header>{{ cart.language === 'ar' ? 'إدارة مستخدمي النظام' : 'Enterprise User Management' }}</template>

        <div class="space-y-8 font-inter" :dir="cart.language === 'ar' ? 'rtl' : 'ltr'">
            <!-- Search & Filters -->
            <div class="flex flex-col md:flex-row justify-between items-center bg-white p-8 rounded-3xl border border-gray-100 shadow-sm gap-6">
                <div class="flex flex-col md:flex-row gap-4 items-center flex-1 w-full">
                    <div class="relative flex-1 w-full">
                        <svg class="absolute w-5 h-5 text-gray-400 top-1/2 -translate-y-1/2" :class="cart.language === 'ar' ? 'right-5' : 'left-5'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        <input 
                            v-model="search"
                            type="text" 
                            :placeholder="cart.language === 'ar' ? 'بحث بالاسم، البريد...' : 'Search name, email...'"
                            class="w-full bg-gray-50 border-gray-100 rounded-2xl py-4 px-14 text-sm focus:ring-2 focus:ring-brand-500 focus:border-transparent transition-all"
                        >
                    </div>
                    <select v-model="roleFilter" class="w-full md:w-48 bg-gray-50 border-gray-100 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-brand-500 outline-none">
                        <option value="">{{ cart.language === 'ar' ? 'كل الأدوار' : 'All Roles' }}</option>
                        <option v-for="role in roles" :key="role.id" :value="role.id">{{ role.label }}</option>
                    </select>
                    <select v-model="statusFilter" class="w-full md:w-40 bg-gray-50 border-gray-100 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-brand-500 outline-none">
                        <option value="">{{ cart.language === 'ar' ? 'كل الحالات' : 'All Status' }}</option>
                        <option value="active">{{ cart.language === 'ar' ? 'نشط' : 'Active' }}</option>
                        <option value="suspended">{{ cart.language === 'ar' ? 'معطل' : 'Suspended' }}</option>
                    </select>
                </div>
                <button 
                    @click="openCreateForm"
                    class="w-full md:w-auto bg-gradient-to-r from-[#ff0033] to-[#cc0029] text-white px-10 py-4 rounded-2xl font-bold text-sm hover:shadow-lg hover:shadow-red-500/30 transition-all active:scale-95"
                >
                    + {{ cart.language === 'ar' ? 'إضافة مستخدم' : 'Add User' }}
                </button>
            </div>

            <!-- Users Table -->
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full" :class="cart.language === 'ar' ? 'text-right' : 'text-left'">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-100 text-[11px] font-bold uppercase tracking-widest text-gray-400">
                                <th class="px-8 py-6">{{ cart.language === 'ar' ? 'المستخدم' : 'User' }}</th>
                                <th class="px-8 py-6">{{ cart.language === 'ar' ? 'الدور' : 'Role' }}</th>
                                <th class="px-8 py-6">{{ cart.language === 'ar' ? 'الحالة' : 'Status' }}</th>
                                <th class="px-8 py-6">{{ cart.language === 'ar' ? 'آخر ظهور' : 'Last Login' }}</th>
                                <th class="px-8 py-6 text-center">{{ cart.language === 'ar' ? 'الإجراءات' : 'Actions' }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50/50 transition-all group">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-full bg-brand-50 text-brand-500 flex items-center justify-center font-bold text-lg border border-brand-100 uppercase">
                                            {{ user.name.charAt(0) }}
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-bold text-gray-900 leading-none mb-1">{{ user.name }}</h4>
                                            <p class="text-xs text-gray-400">{{ user.email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="text-xs font-bold text-gray-600 bg-gray-100 px-3 py-1 rounded-full uppercase tracking-tighter">
                                        {{ user.role_node?.label || user.role || 'User' }}
                                    </span>
                                </td>
                                <td class="px-8 py-6">
                                    <span 
                                        class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest"
                                        :class="user.status === 'active' ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-600'"
                                    >
                                        {{ user.status === 'active' ? (cart.language === 'ar' ? 'نشط' : 'Active') : (cart.language === 'ar' ? 'معطل' : 'Suspended') }}
                                    </span>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="text-xs text-gray-400 font-medium">{{ formatDate(user.last_login_at) }}</span>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex gap-2 justify-center">
                                        <button @click="openEditForm(user)" class="p-2.5 text-gray-400 hover:text-[#ff0033] hover:bg-red-50 rounded-xl transition-all">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                        </button>
                                        <button 
                                            v-if="user.id !== $page.props.auth.user.id"
                                            @click="deleteUser(user)" 
                                            class="p-2.5 text-gray-300 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all"
                                        >
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- User Form Modal -->
        <div v-if="showForm" class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-slate-900/40 backdrop-blur-sm" :dir="cart.language === 'ar' ? 'rtl' : 'ltr'">
            <div class="bg-white w-full max-w-lg rounded-[32px] shadow-2xl overflow-hidden animate-in zoom-in-95 duration-300">
                <div class="p-10">
                    <div class="flex justify-between items-center mb-8">
                        <h3 class="text-2xl font-bold text-gray-900">
                            {{ editingUser ? (cart.language === 'ar' ? 'تعديل مستخدم' : 'Edit User') : (cart.language === 'ar' ? 'إضافة مستخدم' : 'Add New User') }}
                        </h3>
                        <button @click="showForm = false" class="text-gray-400 hover:text-gray-900 transition-all">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <form @submit.prevent="submitForm" class="space-y-6">
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase tracking-widest text-gray-400">{{ cart.language === 'ar' ? 'الاسم' : 'Name' }}</label>
                            <input v-model="form.name" type="text" class="w-full bg-gray-50 border-gray-100 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-[#ff0033] outline-none transition-all" required>
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase tracking-widest text-gray-400">{{ cart.language === 'ar' ? 'البريد الإلكتروني' : 'Email Address' }}</label>
                            <input v-model="form.email" type="email" class="w-full bg-gray-50 border-gray-100 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-[#ff0033] outline-none transition-all" required>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="text-xs font-bold uppercase tracking-widest text-gray-400">{{ cart.language === 'ar' ? 'الدور' : 'Role' }}</label>
                                <select v-model="form.role_id" class="w-full bg-gray-50 border-gray-100 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-[#ff0033] outline-none appearance-none cursor-pointer" required>
                                    <option v-for="role in roles" :key="role.id" :value="role.id">{{ role.label }}</option>
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold uppercase tracking-widest text-gray-400">{{ cart.language === 'ar' ? 'الحالة' : 'Status' }}</label>
                                <select v-model="form.status" class="w-full bg-gray-50 border-gray-100 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-[#ff0033] outline-none appearance-none cursor-pointer">
                                    <option value="active">{{ cart.language === 'ar' ? 'نشط' : 'Active' }}</option>
                                    <option value="suspended">{{ cart.language === 'ar' ? 'معطل' : 'Suspended' }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase tracking-widest text-gray-400">{{ cart.language === 'ar' ? 'كلمة المرور' : 'Password' }}</label>
                            <input v-model="form.password" type="password" class="w-full bg-gray-50 border-gray-100 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-[#ff0033] outline-none transition-all" :required="!editingUser" :placeholder="editingUser ? (cart.language === 'ar' ? 'اتركه فارغاً لعدم التغيير' : 'Leave empty to keep current') : ''">
                        </div>

                        <div class="flex gap-4 pt-6">
                            <button type="button" @click="showForm = false" class="flex-1 bg-gray-100 text-gray-600 rounded-2xl py-4 font-bold text-sm hover:bg-gray-200 transition-all">
                                {{ cart.language === 'ar' ? 'إلغاء' : 'Cancel' }}
                            </button>
                            <button 
                                type="submit" 
                                :disabled="form.processing"
                                class="flex-1 bg-gradient-to-r from-[#ff0033] to-[#cc0029] text-white rounded-2xl py-4 font-bold text-sm shadow-xl shadow-red-500/20 hover:shadow-red-500/40 transition-all disabled:opacity-50"
                            >
                                {{ editingUser ? (cart.language === 'ar' ? 'حفظ التغييرات' : 'Save Changes') : (cart.language === 'ar' ? 'إضافة' : 'Create User') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

