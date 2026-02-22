<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, Link, router } from '@inertiajs/vue3';
import { cart } from '@/Stores/CartStore';
import { system } from '@/Stores/SystemStore';
import { ref, computed } from 'vue';

const props = defineProps({
    role: Object,
    permissions: Object // Grouped
});

const isLocked = computed(() => {
    return props.role && props.role.name === 'super_admin';
});

const form = useForm({
    name: props.role ? props.role.name : '',
    label: props.role ? props.role.label : '',
    description: props.role ? props.role.description : '',
    permissions: props.role ? props.role.permissions.map(p => p.id) : [],
});

const togglePermission = (id) => {
    if (isLocked.value) return;
    const index = form.permissions.indexOf(id);
    if (index > -1) {
        form.permissions.splice(index, 1);
    } else {
        form.permissions.push(id);
    }
};

const toggleAllGroup = (groupPerms) => {
    if (isLocked.value) return;
    const allIds = groupPerms.map(p => p.id);
    const hasAll = allIds.every(id => form.permissions.includes(id));
    if (hasAll) {
        form.permissions = form.permissions.filter(id => !allIds.includes(id));
    } else {
        allIds.forEach(id => {
            if (!form.permissions.includes(id)) form.permissions.push(id);
        });
    }
};

const submitForm = () => {
    if (props.role) {
        form.put(route('admin.roles.update', props.role.id), {
            onSuccess: () => system.notify(cart.language === 'ar' ? 'تم تحديث الدور بنجاح' : 'Role updated successfully'),
        });
    } else {
        form.post(route('admin.roles.store'), {
            onSuccess: () => system.notify(cart.language === 'ar' ? 'تم إنشاء الدور بنجاح' : 'Role created successfully'),
        });
    }
};

const deleteRole = () => {
    if (isLocked.value) return;
    if (confirm(cart.language === 'ar' ? 'هل أنت متأكد من حذف هذا الدور؟ هذا الإجراء قد يسبب خللاً في صلاحيات المستخدمين.' : 'Delete this role? This might affect users assigned to it.')) {
        router.delete(route('admin.roles.destroy', props.role.id), {
            onSuccess: () => system.notify(cart.language === 'ar' ? 'تم حذف الدور' : 'Role deleted', 'error'),
        });
    }
};

const isDangerous = (name) => {
    return name.includes('delete') || name.includes('remove') || name.includes('refund');
};

const groupIcons = {
    'Users Management': `<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>`,
    'Orders & Invoices': `<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>`,
    'Products & Categories': `<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>`,
    'Customers': `<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>`,
    'Finance': `<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`,
};
</script>

<template>
    <Head :title="role ? (cart.language === 'ar' ? 'تعديل الدور' : 'Edit Role') : (cart.language === 'ar' ? 'إضافة دور' : 'Add Role')" />
    <AdminLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link :href="route('admin.roles.index')" class="p-2 hover:bg-gray-100 rounded-xl transition-all">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                </Link>
                <span>{{ role ? (cart.language === 'ar' ? 'تعديل سياسة الصلاحيات' : 'Configure Permission Policy') : (cart.language === 'ar' ? 'تعريف دور أمني جديد' : 'New Security Clearance Role') }}</span>
            </div>
        </template>

        <div class="space-y-8 font-inter pb-20" :dir="cart.language === 'ar' ? 'rtl' : 'ltr'">
            <!-- Role Info Card -->
            <div class="bg-white p-10 rounded-[32px] border border-gray-100 shadow-sm">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div class="space-y-2">
                            <label class="text-xs font-bold uppercase tracking-widest text-gray-400 px-1">{{ cart.language === 'ar' ? 'مفتاح الدور (Unique Key)' : 'Role Identifier (Slug)' }}</label>
                            <input v-model="form.name" type="text" :disabled="isLocked" class="w-full bg-gray-50 border-gray-100 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-[#ff0033] outline-none transition-all" required>
                    </div>
                    <div class="space-y-2">
                            <label class="text-xs font-bold uppercase tracking-widest text-gray-400 px-1">{{ cart.language === 'ar' ? 'اسم الدور' : 'Role Label' }}</label>
                            <input v-model="form.label" type="text" :disabled="isLocked" class="w-full bg-gray-50 border-gray-100 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-[#ff0033] outline-none transition-all" required>
                    </div>
                    <div class="space-y-2">
                            <label class="text-xs font-bold uppercase tracking-widest text-gray-400 px-1">{{ cart.language === 'ar' ? 'الوصف' : 'Description' }}</label>
                            <input v-model="form.description" type="text" :disabled="isLocked" class="w-full bg-gray-50 border-gray-100 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-[#ff0033] outline-none transition-all">
                    </div>
                </div>
            </div>

            <!-- Permissions Matrix -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div v-for="(groupPerms, group) in permissions" :key="group" class="bg-white rounded-[32px] border border-gray-100 shadow-sm overflow-hidden flex flex-col">
                    <div class="px-8 py-6 bg-gray-50/50 border-b border-gray-50 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-white border border-gray-100 shadow-sm flex items-center justify-center text-gray-900" v-html="groupIcons[group] || ''"></div>
                            <h3 class="font-bold text-gray-900">{{ group }}</h3>
                        </div>
                        <button 
                            v-if="!isLocked"
                            @click="toggleAllGroup(groupPerms)"
                            class="text-[10px] font-black uppercase tracking-widest px-4 py-2 rounded-lg border border-gray-200 hover:bg-white hover:border-[#ff0033] hover:text-[#ff0033] transition-all"
                        >
                            {{ groupPerms.every(p => form.permissions.includes(p.id)) ? (cart.language === 'ar' ? 'إلغاء الكل' : 'Deselect All') : (cart.language === 'ar' ? 'تحديد الكل' : 'Select All') }}
                        </button>
                    </div>
                    <div class="p-8 grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div 
                            v-for="perm in groupPerms" 
                            :key="perm.id"
                            @click="togglePermission(perm.id)"
                            class="group relative flex items-center justify-between p-4 rounded-2xl border transition-all cursor-pointer"
                            :class="[
                                form.permissions.includes(perm.id) ? 'bg-red-50 border-[#ff0033]/20' : 'bg-gray-50 border-transparent hover:border-gray-200',
                                isLocked ? 'opacity-50 cursor-not-allowed' : ''
                            ]"
                        >
                            <div class="flex flex-col">
                                <span class="text-xs font-bold transition-colors" :class="form.permissions.includes(perm.id) ? 'text-[#ff0033]' : 'text-gray-700'">{{ perm.label }}</span>
                                <span class="text-[9px] font-medium text-gray-400 uppercase tracking-tighter">{{ perm.name }}</span>
                            </div>
                            <div class="w-5 h-5 rounded-md border-2 transition-all flex items-center justify-center" 
                                 :class="form.permissions.includes(perm.id) ? 'bg-[#ff0033] border-[#ff0033]' : 'bg-white border-gray-200 group-hover:border-gray-300'">
                                <svg v-if="form.permissions.includes(perm.id)" class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="fixed bottom-0 inset-x-0 bg-white/80 backdrop-blur-xl border-t border-gray-100 p-6 z-50">
                <div class="max-w-7xl mx-auto flex justify-between items-center">
                    <div>
                        <button 
                            v-if="role && !isLocked"
                            @click="deleteRole"
                            class="text-red-500 font-bold text-sm px-6 py-3 hover:bg-red-50 rounded-2xl transition-all"
                        >
                            {{ cart.language === 'ar' ? 'حذف هذا الدور' : 'Delete Role Permanently' }}
                        </button>
                    </div>
                    <div class="flex gap-4">
                        <Link :href="route('admin.roles.index')" class="px-10 py-4 text-sm font-bold text-gray-500 hover:text-gray-900 transition-all">
                            {{ cart.language === 'ar' ? 'إلغاء' : 'Discard' }}
                        </Link>
                        <button 
                            v-if="!isLocked"
                            @click="submitForm"
                            :disabled="form.processing"
                            class="bg-gradient-to-r from-[#ff0033] to-[#cc0029] text-white px-14 py-4 rounded-2xl font-bold text-sm shadow-xl shadow-red-500/20 hover:shadow-red-500/40 active:scale-95 transition-all disabled:opacity-50"
                        >
                            {{ cart.language === 'ar' ? 'حفظ السياسة' : 'Deploy Protocol' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
