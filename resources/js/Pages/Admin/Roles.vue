<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { cart } from '@/Stores/CartStore';
import { system } from '@/Stores/SystemStore';
import { ref, computed } from 'vue';

const props = defineProps({
    roles: Array,
    permissions: Object // Grouped
});

const page = usePage();
const currentUserRole = computed(() => page.props.auth.user.roleNode?.name || page.props.auth.user.role);
const isSuperAdmin = computed(() => currentUserRole.value === 'super_admin');

const showForm = ref(false);
const editingRole = ref(null);
const searchPerm = ref('');

const expandedGroups = ref(Object.keys(props.permissions));

const toggleGroup = (group) => {
    if (expandedGroups.value.includes(group)) {
        expandedGroups.value = expandedGroups.value.filter(g => g !== group);
    } else {
        expandedGroups.value.push(group);
    }
};

const form = useForm({
    name: '',
    label: '',
    description: '',
    permissions: [],
});

const openCreateForm = () => {
    if (!isSuperAdmin.value) {
        system.notify(cart.language === 'ar' ? 'عفواً، لا تملك الصلاحيات الكافية لتعريف أدوار جديدة.' : 'Access Denied. Insufficient clearance.', 'error');
        return;
    }
    editingRole.value = null;
    form.reset();
    searchPerm.value = '';
    showForm.value = true;
};

const openEditForm = (role) => {
    if (!isSuperAdmin.value) {
        system.notify(cart.language === 'ar' ? 'عفواً، لا تملك الصلاحيات الكافية لتعديل أدوار النظام.' : 'Access Denied. Critical node lock.', 'error');
        return;
    }
    editingRole.value = role;
    form.name = role.name;
    form.label = role.label;
    form.description = role.description || '';
    form.permissions = role.permissions ? role.permissions.map(p => p.id) : [];
    searchPerm.value = '';
    showForm.value = true;
};

const submitForm = () => {
    if (editingRole.value) {
        form.put(route('admin.roles.update', editingRole.value.id), {
            onSuccess: () => {
                showForm.value = false;
                system.notify(cart.language === 'ar' ? 'تم تحديث مصفوفة الصلاحيات الأمنية بنجاح' : 'Security role updated');
            },
        });
    } else {
        form.post(route('admin.roles.store'), {
            onSuccess: () => {
                showForm.value = false;
                system.notify(cart.language === 'ar' ? 'تم تعريف دور أمني جديد بالنظام' : 'Security role created');
            },
        });
    }
};

const isDangerous = (name) => {
    return name.includes('delete') || name.includes('refund') || name.includes('cancel') || name.includes('withdraw') || name.includes('money');
};

const isLocked = computed(() => {
    if (editingRole.value && editingRole.value.name === 'super_admin') return true;
    return false;
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

const groupIcons = {
    'Users Management': `<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>`,
    'Orders & Invoices': `<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>`,
    'Products & Categories': `<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>`,
    'Customers': `<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>`,
    'POS & Sales': `<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>`,
    'Finance': `<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`,
    'Analytics': `<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>`,
    'Content & Pages': `<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" /></svg>`,
    'QR System': `<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" /></svg>`,
    'Settings & Security': `<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>`,
    'Cash Control': `<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>`
};

const defaultIcon = `<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`;

const filteredPermissions = computed(() => {
    if (!searchPerm.value) return props.permissions;
    const lower = searchPerm.value.toLowerCase();
    const result = {};
    for (const [group, currentPerms] of Object.entries(props.permissions)) {
        const matching = currentPerms.filter(p => p.label.toLowerCase().includes(lower) || p.name.toLowerCase().includes(lower));
        if (matching.length) {
            result[group] = matching;
            if(!expandedGroups.value.includes(group)) {
                 expandedGroups.value.push(group);
            }
        }
    }
    return result;
});

const getGroupIcon = (group) => {
    return groupIcons[group] || defaultIcon;
};

const isRoleDanger = (name) => name === 'super_admin';
</script>

<template>
    <Head :title="cart.language === 'ar' ? 'مصفوفة التصاريح والأمن' : 'Security Clearance Framework'" />

    <AdminLayout>
        <template #header>{{ cart.language === 'ar' ? 'أدوار النظام ومستويات التمكين العملياتي' : 'Strategic Governance & Auth Matrix' }}</template>

        <div class="space-y-12 font-inter" :dir="cart.language === 'ar' ? 'rtl' : 'ltr'">
            <!-- Strategic Governance Command Hub -->
            <div class="flex flex-col md:flex-row justify-between items-center bg-white p-10 rounded-[24px] border border-gray-100 shadow-sm gap-10 relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-32 h-32 bg-gray-50 rounded-full -mr-16 -mt-16 opacity-50 group-hover:bg-gray-100 transition-colors"></div>
                <div class="relative z-10" :class="cart.language === 'ar' ? 'text-right' : 'text-left'">
                    <h3 class="text-3xl font-black text-gray-900 tracking-tighter">{{ cart.language === 'ar' ? 'إدارة مستويات الوصول' : 'Access Stratification Control' }}</h3>
                    <p class="text-xs font-bold text-gray-400 mt-2">{{ cart.language === 'ar' ? 'إدارة مصفوفة صلاحيات الوصول والتحكم في وظائف النظام لكل فئة إدارية' : 'Manage structural access levels and functional module authorizations across the hierarchy' }}</p>
                </div>
                <button 
                    v-if="isSuperAdmin"
                    @click="openCreateForm"
                    class="w-full md:w-auto bg-gray-900 text-white px-12 py-5 rounded-[16px] font-black text-xs uppercase tracking-[0.2em] shadow-2xl shadow-gray-900/10 hover:bg-gray-800 transition-all active:scale-95 relative z-10"
                >
                    + {{ cart.language === 'ar' ? 'تعريف دور أمني جديد' : 'Induct New Role' }}
                </button>
            </div>

            <!-- Role Architecture Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                <div v-for="role in roles" :key="role.id" class="bg-white p-10 rounded-[28px] border border-gray-100 shadow-sm hover:shadow-2xl hover:shadow-gray-900/5 transition-all group duration-500 flex flex-col relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-1 bg-gray-900 opacity-5 group-hover:opacity-100 transition-opacity"></div>
                    <div class="flex justify-between items-start mb-8">
                        <div class="w-14 h-14 rounded-[18px] flex items-center justify-center font-black transition-all border border-gray-100 shadow-sm group-hover:rotate-6"
                             :class="isRoleDanger(role.name) ? 'bg-red-50 text-red-600 border-red-100' : 'bg-gray-50 text-gray-900'">
                            <span v-if="isRoleDanger(role.name)">
                                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                            </span>
                            <span v-else class="text-2xl uppercase">{{ role.name.charAt(0) }}</span>
                        </div>
                        <div class="flex gap-2">
                            <button v-if="isSuperAdmin" @click="openEditForm(role)" class="text-gray-400 hover:text-gray-900 transition-all bg-gray-50 p-3 rounded-[12px] border border-gray-100 active:scale-90 shadow-sm">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                            </button>
                        </div>
                    </div>
                    
                    <h4 class="text-2xl font-black text-gray-900 tracking-tighter">{{ role.label }}</h4>
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mt-1.5">{{ role.name }}</p>
                    
                    <p class="text-xs font-bold text-gray-500 mt-8 line-clamp-3 min-h-[4.5em] leading-relaxed">{{ role.description || (cart.language === 'ar' ? 'لم يتم إدراج وصف تحليلي لهذا الدور الأمني في قاعدة البيانات الحالية.' : 'No descriptive narrative has been inducted for this security node.') }}</p>

                    <div class="mt-auto pt-10 flex items-center justify-between border-t border-gray-50">
                        <div class="flex items-center gap-3">
                            <div class="flex -space-x-2">
                                <div class="w-8 h-8 rounded-full bg-gray-900 border-2 border-white flex items-center justify-center text-[10px] font-black text-white shadow-sm">{{ role.users_count || 0 }}</div>
                            </div>
                            <span class="text-[9px] font-black uppercase text-gray-400 tracking-widest">{{ cart.language === 'ar' ? 'عضو نشط' : 'Active Personnel' }}</span>
                        </div>
                        <span class="text-[9px] font-black uppercase px-4 py-2 rounded-lg border shadow-sm transition-all"
                              :class="isRoleDanger(role.name) ? 'text-red-600 bg-red-50 border-red-100' : 'text-emerald-600 bg-emerald-50 border-emerald-100'">
                            {{ role.permissions_count || 0 }} {{ cart.language === 'ar' ? 'تصريح برمجـي' : 'Logic Gates' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Strategic Role Matrix Configuration Modal -->
        <div v-if="showForm" class="fixed inset-0 z-[100] flex items-center justify-center p-6 lg:p-12 bg-gray-900/60 backdrop-blur-md" :dir="cart.language === 'ar' ? 'rtl' : 'ltr'">
            <div class="bg-white w-full max-w-6xl rounded-[32px] relative shadow-2xl border border-gray-100 animate-in zoom-in-95 duration-500 h-[92vh] flex flex-col overflow-hidden">
                <!-- Modal Elevation Header -->
                <div class="px-12 py-8 border-b border-gray-100 flex items-center justify-between bg-white shrink-0">
                    <div>
                        <h3 class="text-3xl font-black text-gray-900 tracking-tighter flex items-center gap-5">
                            <span v-if="isLocked" class="bg-red-50 text-red-600 p-4 rounded-[16px] shadow-sm animate-pulse">
                                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                            </span>
                            {{ editingRole ? (isLocked ? (cart.language === 'ar' ? 'دور نظام محمي سيادياً (Root)' : 'Sovereign Core Role') : (cart.language === 'ar' ? 'تعديل بروتوكول الصلاحيات' : 'Modify Security Protocol')) : (cart.language === 'ar' ? 'حقن مستوى صلاحية جديد' : 'Induct New Hierarchy Level') }}
                        </h3>
                        <p v-if="isLocked" class="text-[10px] text-red-500 font-black uppercase tracking-[0.2em] mt-2">{{ cart.language === 'ar' ? 'لا يمكن تعديل معايير النظام الافتراضية المحمية' : 'System-critical parameters are locked from manual modification' }}</p>
                    </div>
                    <button @click="showForm = false" class="p-3 text-gray-400 hover:text-gray-900 bg-gray-50 rounded-full hover:rotate-90 transition-all duration-300">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <!-- Modal Operational Body -->
                <div class="flex-1 overflow-hidden grid grid-cols-1 lg:grid-cols-4 bg-gray-50/20">
                    <!-- Section: Role Core Parameters -->
                    <div class="p-10 border-r border-gray-100 space-y-10 bg-white overflow-y-auto" :class="cart.language === 'ar' ? 'lg:border-l lg:border-r-0' : 'lg:border-r'">
                        <div class="space-y-3">
                            <label class="text-[11px] font-black uppercase tracking-widest text-gray-500 px-1">{{ cart.language === 'ar' ? 'مفتاح النظام الفريد' : 'Node Identifier Key' }}</label>
                            <input v-model="form.name" type="text" placeholder="e.g. operational_lead" class="w-full bg-gray-50 border-none rounded-[16px] px-8 py-5 text-sm font-black text-gray-900 shadow-inner focus:ring-2 focus:ring-gray-900 transition-all" :disabled="isLocked" required>
                        </div>
                        <div class="space-y-3">
                            <label class="text-[11px] font-black uppercase tracking-widest text-gray-500 px-1">{{ cart.language === 'ar' ? 'المسمى الوظيفي المعروض' : 'Human-Readable Label' }}</label>
                            <input v-model="form.label" type="text" placeholder="e.g. Logistics Manager" class="w-full bg-gray-50 border-none rounded-[16px] px-8 py-5 text-sm font-black text-gray-900 shadow-inner focus:ring-2 focus:ring-gray-900 transition-all" :disabled="isLocked" required>
                        </div>
                        <div class="space-y-3">
                            <label class="text-[11px] font-black uppercase tracking-widest text-gray-500 px-1">{{ cart.language === 'ar' ? 'السرد الوصفي للصلاحية' : 'Governance Narrative' }}</label>
                            <textarea v-model="form.description" class="w-full bg-gray-50 border-none rounded-[16px] px-8 py-5 text-sm font-black text-gray-900 shadow-inner focus:ring-2 focus:ring-gray-900 h-48 resize-none transition-all" :disabled="isLocked" :placeholder="cart.language === 'ar' ? 'أدخل تفاصيل المسؤوليات الوظيفية لهذا الدور...' : 'Describe the functional impact of this security node...'"></textarea>
                        </div>
                    </div>

                    <!-- Section: Logic Gate Permissions Matrix -->
                    <div class="lg:col-span-3 flex flex-col overflow-hidden">
                        <!-- Command Center -->
                        <div class="px-10 py-6 border-b border-gray-100 bg-white/80 backdrop-blur-sm flex items-center justify-between shrink-0 sticky top-0 z-20">
                            <div class="relative w-full max-w-lg">
                                <svg class="w-5 h-5 absolute top-1/2 -translate-y-1/2 text-gray-400" :class="cart.language === 'ar' ? 'right-6' : 'left-6'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                                <input v-model="searchPerm" type="text" :placeholder="cart.language === 'ar' ? 'البحث عن تصريح برمجي محدد...' : 'Scan Strategy Gates...'" class="w-full bg-gray-50 border-none rounded-[16px] py-4 text-sm font-black text-gray-900 shadow-inner focus:ring-2 focus:ring-gray-900 transition-all" :class="cart.language === 'ar' ? 'pr-14 pl-8' : 'pl-14 pr-8'">
                            </div>
                            <span class="text-[10px] font-black uppercase tracking-widest text-gray-400 bg-gray-50 px-5 py-2.5 rounded-full border border-gray-100 ml-6">{{ Object.keys(filteredPermissions).length }} {{ cart.language === 'ar' ? 'قطاعات أمنية' : 'Force Sectors' }}</span>
                        </div>

                        <!-- Data Flux Flow -->
                        <div class="p-10 overflow-y-auto flex-1 space-y-6 bg-gray-50/10">
                            <div v-for="(groupPerms, group) in filteredPermissions" :key="group" class="bg-white border border-gray-100 rounded-[24px] overflow-hidden shadow-sm hover:shadow-lg transition-all duration-500">
                                
                                <!-- Sector Header Toggle -->
                                <button @click="toggleGroup(group)" class="w-full px-8 py-6 bg-white hover:bg-gray-50 flex items-center justify-between transition-all group/hdr border-b border-transparent" :class="expandedGroups.includes(group) && 'border-gray-50 bg-gray-50/50'">
                                    <div class="flex items-center gap-6 text-right">
                                        <div class="w-14 h-14 rounded-[18px] bg-white border border-gray-100 shadow-sm flex items-center justify-center text-gray-900 group-hover/hdr:scale-110 transition-transform" v-html="getGroupIcon(group)"></div>
                                        <div :class="cart.language === 'ar' ? 'text-right' : 'text-left'">
                                            <h5 class="text-lg font-black text-gray-900 tracking-tighter">{{ group }}</h5>
                                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mt-1">{{ groupPerms.length }} {{ cart.language === 'ar' ? 'بوابة تصريح' : 'Logic Gates' }} • {{ groupPerms.filter(p => form.permissions.includes(p.id)).length }} {{ cart.language === 'ar' ? 'نشطة' : 'Injected' }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-8">
                                        <!-- Strategic Select All -->
                                        <div @click.stop="toggleAllGroup(groupPerms)" class="flex items-center gap-3 px-5 py-2.5 rounded-[12px] bg-white border border-gray-200 hover:border-gray-900 transition-all group/all cursor-pointer shadow-sm active:scale-90" :class="{ 'opacity-50 pointer-events-none': isLocked }">
                                            <div class="w-5 h-5 rounded-md border-2 transition-all flex items-center justify-center" :class="groupPerms.every(p => form.permissions.includes(p.id)) ? 'bg-gray-900 border-gray-900' : 'border-gray-200'">
                                                <svg v-if="groupPerms.every(p => form.permissions.includes(p.id))" class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                            </div>
                                            <span class="text-[10px] font-black uppercase tracking-widest text-gray-700">{{ cart.language === 'ar' ? 'الكل' : 'Total' }}</span>
                                        </div>
                                        <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-500" :class="expandedGroups.includes(group) ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg>
                                    </div>
                                </button>
                                
                                <!-- Permissions Matrix Flux -->
                                <div v-show="expandedGroups.includes(group)" class="p-8 grid grid-cols-1 md:grid-cols-2 gap-4 bg-white animate-in slide-in-from-top-4 duration-500">
                                    <div 
                                        v-for="perm in groupPerms" 
                                        :key="perm.id"
                                        @click="togglePermission(perm.id)"
                                        class="flex items-center justify-between px-6 py-4.5 rounded-[18px] cursor-pointer transition-all border group/perm relative overflow-hidden"
                                        :class="[
                                            form.permissions.includes(perm.id) 
                                                ? (isDangerous(perm.name) ? 'bg-red-50 border-red-200' : 'bg-gray-900 border-gray-900 shadow-lg shadow-gray-900/10') 
                                                : 'bg-white border-gray-100 hover:border-gray-400',
                                            isLocked ? 'opacity-80 pointer-events-none' : ''
                                        ]"
                                    >
                                        <div class="flex flex-col text-right relative z-10" :class="cart.language === 'ar' ? 'text-right' : 'text-left'">
                                            <span class="text-[12px] font-black tracking-tight" :class="form.permissions.includes(perm.id) ? (isDangerous(perm.name) ? 'text-red-700' : 'text-white') : 'text-gray-900'">{{ perm.label }}</span>
                                            <code class="text-[9px] mt-1.5 font-bold uppercase tracking-widest opacity-60 flex gap-3">
                                                <span v-if="isDangerous(perm.name)" class="text-red-600 font-black animate-pulse uppercase">{{ cart.language === 'ar' ? 'خطر' : 'Risk' }}</span>
                                                <span :class="form.permissions.includes(perm.id) ? (isDangerous(perm.name) ? 'text-red-900' : 'text-white/60') : 'text-gray-400'">{{ perm.name }}</span>
                                            </code>
                                        </div>
                                        <div class="w-6 h-6 rounded-lg border-2 flex items-center justify-center transition-all shrink-0 relative z-10 shadow-sm" 
                                             :class="form.permissions.includes(perm.id) 
                                                ? (isDangerous(perm.name) ? 'bg-red-900 border-red-900' : 'bg-white border-white') 
                                                : 'border-gray-100 bg-gray-50'">
                                            <svg v-if="form.permissions.includes(perm.id)" class="w-4 h-4" :class="isDangerous(perm.name) ? 'text-white' : 'text-gray-900'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Strategic Deployment Footer -->
                <div class="px-12 py-8 border-t border-gray-100 bg-white flex items-center justify-end gap-6 shrink-0 z-30">
                    <button type="button" @click="showForm = false" class="px-8 py-3.5 font-black text-xs uppercase tracking-widest text-gray-400 hover:text-gray-900 transition-all">{{ cart.language === 'ar' ? 'إلغاء البروتوكول' : 'Abort Induction' }}</button>
                    <button 
                        v-if="!isLocked"
                        @click="submitForm"
                        :disabled="form.processing"
                        class="bg-gray-900 text-white px-14 py-4.5 rounded-[16px] font-black text-xs uppercase tracking-[0.3em] transition-all shadow-2xl shadow-gray-900/40 hover:bg-emerald-600 hover:shadow-emerald-500/20 disabled:opacity-50 active:scale-95"
                    >
                        {{ editingRole ? (cart.language === 'ar' ? 'حفظ التعديلات' : 'Commit Protocol') : (cart.language === 'ar' ? 'تثبيت الدور الأمني' : 'Deploy Hierarchy') }}
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
