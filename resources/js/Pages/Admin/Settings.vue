<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import { cart } from '@/Stores/CartStore';

// Strategic role definition nodes
const roles = ref([
    { 
        id: 1, 
        name: cart.language === 'ar' ? 'مدير النظام (SuperAdmin)' : 'SuperAdmin', 
        description: cart.language === 'ar' ? 'مستوى الأمان السيادي - جميع صلاحيات الوصول والتعديل وتحسين معايير النظام متاحة.' : 'Full system authorization with strategic clearance level 10.',
        permissions: ['inventory', 'finance', 'roles', 'orders', 'customers', 'settings']
    },
    { 
        id: 2, 
        name: cart.language === 'ar' ? 'مدير عمليات (Manager)' : 'Manager', 
        description: cart.language === 'ar' ? 'صلاحيات التحكم الإداري في دورة العمليات، المخزون، وسجلات العملاء.' : 'Operational control for orders and inventory management nodes.',
        permissions: ['inventory', 'orders', 'customers']
    },
    { 
        id: 3, 
        name: cart.language === 'ar' ? 'موظف عمليات (Employee)' : 'Employee', 
        description: cart.language === 'ar' ? 'صلاحيات تنفيذية محدودة لمعالجة الطلبات الفورية وتحديثات الكميات المخزنية.' : 'Limited access for processing transmissions and stock updates.',
        permissions: ['inventory', 'orders']
    },
]);

const modules = [
    { key: 'inventory', nameAr: 'محرك المخزون', nameEn: 'Inventory Engine' },
    { key: 'finance', nameAr: 'التحليلات المالية', nameEn: 'Finance Analytics' },
    { key: 'roles', nameAr: 'إدارة الهوية', nameEn: 'Identity Management' },
    { key: 'orders', nameAr: 'اللوجستيات', nameEn: 'Logistics Protocol' },
    { key: 'customers', nameAr: 'ملفات العملاء', nameEn: 'Subject Dossiers' },
    { key: 'settings', nameAr: 'الإعدادات الأساسية', nameEn: 'Core Parameters' },
];

const togglePermission = (roleId, permission) => {
    const role = roles.value.find(r => r.id === roleId);
    if (!role) return;
    
    if (role.permissions.includes(permission)) {
        role.permissions = role.permissions.filter(p => p !== permission);
    } else {
        role.permissions.push(permission);
    }
};
</script>

<template>
    <Head :title="cart.language === 'ar' ? 'إعدادات النظام' : 'System Parameters'" />

    <AdminLayout>
        <template #header>{{ cart.language === 'ar' ? 'إعدادات النظام والبارامترات التشغيلية' : 'Advanced System Configuration' }}</template>

        <div class="space-y-12 font-inter" :dir="cart.language === 'ar' ? 'rtl' : 'ltr'">
            <!-- Governance Matrix Management -->
            <div class="max-w-7xl mx-auto">
                <div class="mb-12 flex flex-col md:flex-row md:items-end justify-between gap-6 border-b border-gray-100 pb-10">
                    <div :class="cart.language === 'ar' ? 'text-right' : 'text-left'">
                        <h3 class="text-3xl font-black text-gray-900 tracking-tighter">{{ cart.language === 'ar' ? 'مصفوفة الأدوار والصلاحيات' : 'Strategic Role Matrix' }}</h3>
                        <p class="text-xs font-bold text-gray-400 mt-2">{{ cart.language === 'ar' ? 'تهيئة مستويات الوصول للنظام وتوزيع التصاريح البرمجية للمجموعات' : 'Configure structural access levels and functional module authorizations' }}</p>
                    </div>
                    <div class="px-6 py-3 bg-gray-50 rounded-[12px] border border-gray-100">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ cart.language === 'ar' ? 'إجمالي الأدوار النشطة:' : 'Active Nodes:' }}</span>
                        <span class="text-sm font-black text-gray-900 ml-2">{{ roles.length }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-10">
                    <div v-for="role in roles" :key="role.id" class="bg-white p-10 rounded-[28px] border border-gray-100 shadow-sm hover:shadow-2xl hover:shadow-gray-900/5 transition-all group relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-full h-1 bg-gray-900 opacity-10 group-hover:opacity-100 transition-opacity"></div>
                        
                        <div class="mb-10">
                            <h4 class="text-2xl font-black text-gray-900 tracking-tighter mb-3">{{ role.name }}</h4>
                            <p class="text-xs font-bold text-gray-400 leading-relaxed min-h-[40px]">{{ role.description }}</p>
                        </div>

                        <div class="space-y-4">
                            <div v-for="mod in modules" :key="mod.key" class="flex items-center justify-between p-5 bg-gray-50/50 hover:bg-white rounded-[16px] border border-transparent hover:border-gray-100 transition-all group/item shadow-sm">
                                <span class="text-[11px] font-black uppercase tracking-widest text-gray-400 group-hover/item:text-gray-900 transition-colors">{{ cart.language === 'ar' ? mod.nameAr : mod.nameEn }}</span>
                                <div 
                                    @click="togglePermission(role.id, mod.key)"
                                    class="w-12 h-6.5 rounded-full transition-all cursor-pointer p-1.5 shadow-inner"
                                    :class="role.permissions.includes(mod.key) ? 'bg-gray-900' : 'bg-gray-200'"
                                >
                                    <div class="w-3.5 h-3.5 bg-white rounded-full shadow-lg transition-all" :class="role.permissions.includes(mod.key) ? (cart.language === 'ar' ? '-translate-x-5.5' : 'translate-x-5.5') : 'translate-x-0'"></div>
                                </div>
                            </div>
                        </div>

                        <button class="w-full mt-12 py-5 bg-gray-900 text-white rounded-[20px] text-xs font-black uppercase tracking-[0.2em] shadow-2xl shadow-gray-900/30 hover:bg-gray-800 hover:-translate-y-1 transition-all active:scale-95">
                            {{ cart.language === 'ar' ? 'حفظ التغييرات للصلاحية' : 'Persist Node Config' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
