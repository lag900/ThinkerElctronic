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
                system.notify(cart.language === 'ar' ? 'تم تحديث مصفوفة بيانات المستخدم بنجاح' : 'User node parameters updated');
            },
        });
    } else {
        form.post(route('admin.users.store'), {
            onSuccess: () => {
                showForm.value = false;
                system.notify(cart.language === 'ar' ? 'تم حقن حساب المستخدم الجديد في النظام' : 'New user node deployed safely');
            },
        });
    }
};

const deleteUser = (user) => {
    if (confirm(cart.language === 'ar' ? `هل أنت متأكد من حذف حساب (${user.name}) بشكل نهائي؟` : `Terminate user node ${user.name}? This action is permanent.`)) {
        router.delete(route('admin.users.destroy', user.id), {
            onSuccess: () => system.notify(cart.language === 'ar' ? 'تم إنهاء صلاحية المستخدم بنجاح' : 'User node terminated', 'error'),
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
    <Head :title="cart.language === 'ar' ? 'إدارة الكوادر والمستخدمين' : 'User Force Management'" />
    <AdminLayout>
        <template #header>{{ cart.language === 'ar' ? 'إدارة الكوادر وصلاحيات الوصول' : 'Operational User Force Management' }}</template>

        <div class="space-y-10 font-inter" :dir="cart.language === 'ar' ? 'rtl' : 'ltr'">
            <!-- Strategic Force Filtering -->
            <div class="flex flex-col md:flex-row justify-between items-center bg-white p-10 rounded-[20px] border border-gray-100 shadow-sm gap-8 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-gray-50 rounded-full -mr-16 -mt-16 opacity-50"></div>
                
                <div class="flex flex-col md:flex-row gap-6 items-center flex-1 w-full relative z-10">
                    <div class="relative flex-1 w-full max-w-lg">
                        <svg class="absolute w-5 h-5 text-gray-400 top-1/2 -translate-y-1/2" :class="cart.language === 'ar' ? 'right-6' : 'left-6'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        <input 
                            v-model="search"
                            type="text" 
                            :placeholder="cart.language === 'ar' ? 'البحث عن عضو أو موظف...' : 'Scan Member Archives...'"
                            class="w-full bg-gray-50 border-none rounded-[16px] py-4.5 px-8 text-sm font-black text-gray-900 shadow-inner focus:ring-2 focus:ring-gray-900 transition-all placeholder:text-gray-400"
                            :class="cart.language === 'ar' ? 'pr-16 pl-8' : 'pl-16 pr-8'"
                        >
                    </div>
                    <select 
                        v-model="roleFilter"
                        class="w-full md:w-auto bg-gray-50 border-none rounded-[16px] px-10 py-4.5 text-sm font-black text-gray-700 shadow-inner focus:ring-2 focus:ring-gray-900 cursor-pointer appearance-none outline-none transition-all"
                    >
                        <option value="">{{ cart.language === 'ar' ? 'جميع المستويات' : 'All Hierarchy Levels' }}</option>
                        <option v-for="role in roles" :key="role.id" :value="role.id">{{ role.label }}</option>
                    </select>
                </div>
                <button 
                    @click="openCreateForm"
                    class="w-full md:w-auto bg-gray-900 text-white px-12 py-4.5 rounded-[16px] font-black text-xs uppercase tracking-[0.2em] shadow-2xl shadow-gray-900/10 hover:bg-gray-800 transition-all active:scale-95 relative z-10"
                >
                    + {{ cart.language === 'ar' ? 'إضافة عضو جديد' : 'Initialize Member' }}
                </button>
            </div>

            <!-- Force Registry Ledger -->
            <div class="bg-white rounded-[24px] border border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full" :class="cart.language === 'ar' ? 'text-right' : 'text-left'">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-100 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">
                                <th class="px-10 py-8">{{ cart.language === 'ar' ? 'بيانات العضو' : 'Member Identity' }}</th>
                                <th class="px-10 py-8 text-center">{{ cart.language === 'ar' ? 'طبيعة الصلاحيات' : 'Auth Protocol' }}</th>
                                <th class="px-10 py-8 text-center">{{ cart.language === 'ar' ? 'حالة التواجد' : 'Connectivity' }}</th>
                                <th class="px-10 py-8" :class="cart.language === 'ar' ? 'text-left' : 'text-right'">{{ cart.language === 'ar' ? 'إدارة' : 'Modify' }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <tr v-for="user in users.data" :key="user.id" class="group hover:bg-gray-50/30 transition-all duration-300">
                                <td class="px-10 py-8">
                                    <div class="flex items-center gap-5">
                                        <div class="w-14 h-14 bg-gray-900 text-white rounded-[16px] flex items-center justify-center font-black text-lg shadow-2xl group-hover:scale-105 transition-transform">
                                            {{ user.name.charAt(0) }}
                                        </div>
                                        <div>
                                            <h4 class="text-[14px] font-black text-gray-900 tracking-tight leading-none mb-2">{{ user.name }}</h4>
                                            <p class="text-[9px] font-black text-gray-400 tracking-widest uppercase">{{ user.email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-10 py-8 text-center">
                                    <span class="inline-flex px-5 py-2.5 bg-gray-50 border border-gray-100 rounded-full text-[10px] font-black uppercase tracking-widest text-gray-900 shadow-sm transition-all group-hover:bg-white group-hover:border-gray-900">
                                        {{ user.role_node?.label || '---' }}
                                    </span>
                                </td>
                                <td class="px-10 py-8 text-center">
                                    <div class="flex items-center justify-center gap-3">
                                        <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                                        <span class="text-[10px] font-black text-emerald-600 uppercase tracking-widest">{{ cart.language === 'ar' ? 'نشط بالنظام' : 'Active Flow' }}</span>
                                    </div>
                                </td>
                                <td class="px-10 py-8" :class="cart.language === 'ar' ? 'text-left' : 'text-right'">
                                    <div class="flex gap-4" :class="cart.language === 'ar' ? 'justify-start' : 'justify-end'">
                                        <button @click="openEditForm(user)" class="p-3.5 bg-gray-900 text-white rounded-[12px] hover:bg-gray-800 transition-all shadow-lg shadow-gray-900/10 active:scale-90">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                        </button>
                                        <button 
                                            v-if="user.id !== $page.props.auth.user.id"
                                            @click="deleteUser(user)" 
                                            class="p-3.5 bg-white border border-gray-100 text-gray-400 hover:text-red-600 hover:border-red-600 rounded-[12px] transition-all shadow-sm active:scale-90"
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
        </div>

        <!-- Identity Configuration Modal -->
        <div v-if="showForm" class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-gray-900/60 backdrop-blur-md" :dir="cart.language === 'ar' ? 'rtl' : 'ltr'">
            <div class="bg-white w-full max-w-xl rounded-[28px] relative shadow-2xl border border-gray-100 overflow-hidden animate-in zoom-in-95 duration-500">
                <div class="absolute top-0 left-0 w-full h-2 bg-gray-900"></div>
                <div class="p-12">
                    <button @click="showForm = false" class="absolute top-8 left-8 p-3 text-gray-400 hover:text-gray-900 bg-gray-50 rounded-full hover:rotate-90 transition-all duration-300">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>

                    <h3 class="text-3xl font-black text-gray-900 tracking-tighter mb-2">
                        {{ editingUser ? (cart.language === 'ar' ? 'تعديل المعايير الشخصية' : 'Synchronize Identity') : (cart.language === 'ar' ? 'قيد عضوية جديدة' : 'Member Induction') }}
                    </h3>
                    <p class="text-[11px] font-black uppercase tracking-[0.2em] text-gray-400 mb-10">{{ editingUser ? (cart.language === 'ar' ? 'تحديث بيانات الحساب ومستوى الصلاحيات العملياتية' : 'Update identity credentials and operational flux') : (cart.language === 'ar' ? 'تسجيل عضو جديد في فريق العمل بمصفوفة صلاحيات محددة' : 'Induct a new force member with specific access protocols') }}</p>

                    <form @submit.prevent="submitForm" class="space-y-8">
                        <div class="space-y-2">
                            <label class="text-[11px] font-black uppercase tracking-widest text-gray-500 px-1">{{ cart.language === 'ar' ? 'الاسم بالكامل' : 'Full Identity Name' }}</label>
                            <input v-model="form.name" type="text" class="w-full bg-gray-50 border-none rounded-[16px] px-8 py-5 text-sm font-black text-gray-900 shadow-inner focus:ring-2 focus:ring-gray-900 transition-all" :placeholder="cart.language === 'ar' ? 'أدخل اسم العضو الثلاثي...' : 'e.g. Nathan Drake'" required>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[11px] font-black uppercase tracking-widest text-gray-500 px-1">{{ cart.language === 'ar' ? 'الإزاحة الرقمية (البريد)' : 'Digital Endpoint (Email)' }}</label>
                            <input v-model="form.email" type="email" class="w-full bg-gray-50 border-none rounded-[16px] px-8 py-5 text-sm font-black text-gray-900 shadow-inner focus:ring-2 focus:ring-gray-900 transition-all" placeholder="name@thinker.net" required>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[11px] font-black uppercase tracking-widest text-gray-500 px-1">{{ cart.language === 'ar' ? 'الدور والمسؤوليات' : 'Assigned Responsibility' }}</label>
                            <select v-model="form.role_id" class="w-full bg-gray-50 border-none rounded-[16px] px-8 py-5 text-sm font-black text-gray-700 shadow-inner focus:ring-2 focus:ring-gray-900 appearance-none cursor-pointer transition-all" required>
                                <option value="" disabled>{{ cart.language === 'ar' ? '--- اختر مستوى الصلاحيات ---' : '--- Select Access Strategy ---' }}</option>
                                <option v-for="role in roles" :key="role.id" :value="role.id">{{ role.label }}</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[11px] font-black uppercase tracking-widest text-gray-500 px-1">{{ cart.language === 'ar' ? 'كلمة المرور المشفرة' : 'Encrypted Key (Password)' }}</label>
                            <input v-model="form.password" type="password" class="w-full bg-gray-50 border-none rounded-[16px] px-8 py-5 text-sm font-black text-gray-900 shadow-inner focus:ring-2 focus:ring-gray-900 transition-all" :required="!editingUser" :placeholder="editingUser ? (cart.language === 'ar' ? 'اتركها فارغة للحفاظ على التشفير الحالي' : 'Leave null to preserve layer') : '••••••••'">
                        </div>

                        <div class="flex gap-6 pt-6">
                            <button type="button" @click="showForm = false" class="flex-1 bg-gray-100 rounded-[16px] py-5 font-black uppercase tracking-widest text-[11px] text-gray-700 hover:bg-gray-200 transition-all">{{ cart.language === 'ar' ? 'إلغاء العمليات' : 'Abort Session' }}</button>
                            <button 
                                type="submit" 
                                :disabled="form.processing"
                                class="flex-1 bg-gray-900 text-white rounded-[16px] py-5 font-black uppercase tracking-widest text-[11px] shadow-2xl shadow-gray-900/30 hover:bg-gray-800 transition-all disabled:opacity-50"
                            >
                                {{ editingUser ? (cart.language === 'ar' ? 'حفظ التعديلات' : 'Commit Changes') : (cart.language === 'ar' ? 'تثبيت الحساب' : 'Deploy Identity') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
