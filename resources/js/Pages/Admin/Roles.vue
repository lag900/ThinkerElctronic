<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { cart } from '@/Stores/CartStore';
import { system } from '@/Stores/SystemStore';
import { computed } from 'vue';

const props = defineProps({
    roles: Array,
});

const isRoleDanger = (name) => name === 'super_admin';
</script>

<template>
    <Head :title="cart.language === 'ar' ? 'أدوار النظام' : 'Access Roles'" />
    <AdminLayout>
        <template #header>{{ cart.language === 'ar' ? 'إدارة الأدوار والصلاحيات' : 'Roles & Permissions Matrix' }}</template>

        <div class="space-y-12 font-inter" :dir="cart.language === 'ar' ? 'rtl' : 'ltr'">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row justify-between items-center bg-white p-10 rounded-[32px] border border-gray-100 shadow-sm gap-8">
                <div>
                    <h2 class="text-3xl font-black text-gray-900 tracking-tighter uppercase">{{ cart.language === 'ar' ? 'هيكل الصلاحيات' : 'Permission Architecture' }}</h2>
                    <p class="text-sm font-bold text-gray-400 mt-2 uppercase tracking-widest">{{ cart.language === 'ar' ? 'تحديد مستويات الوصول والتحكم في وظائف النظام' : 'Define access levels and functional authorizations' }}</p>
                </div>
                <Link 
                    :href="route('admin.roles.create')"
                    class="bg-gradient-to-r from-[#ff0033] to-[#cc0029] text-white px-12 py-5 rounded-2xl font-bold text-sm hover:shadow-lg hover:shadow-red-500/30 transition-all active:scale-95"
                >
                    + {{ cart.language === 'ar' ? 'إضافة دور جديد' : 'Create New Role' }}
                </Link>
            </div>

            <!-- Roles Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                <Link 
                    v-for="role in roles" 
                    :key="role.id"
                    :href="route('admin.roles.edit', role.id)"
                    class="group bg-white p-10 rounded-[32px] border border-gray-100 shadow-sm hover:border-[#ff0033] hover:shadow-[0_20px_40px_rgba(255,0,51,0.1)] transition-all duration-500 flex flex-col relative overflow-hidden active:scale-[0.98]"
                >
                    <div class="absolute top-0 inset-x-0 h-1.5 bg-[#ff0033] opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    
                    <div class="flex justify-between items-start mb-8">
                        <div class="w-16 h-16 rounded-2xl flex items-center justify-center font-bold text-2xl border transition-all"
                             :class="isRoleDanger(role.name) ? 'bg-red-50 text-[#ff0033] border-red-100 group-hover:bg-[#ff0033] group-hover:text-white' : 'bg-gray-50 text-gray-900 border-gray-100 group-hover:bg-[#ff0033] group-hover:text-white group-hover:border-[#ff0033]'">
                            {{ role.name.charAt(0).toUpperCase() }}
                        </div>
                        <div class="bg-gray-50 px-4 py-2 rounded-full border border-gray-100 group-hover:bg-red-50 group-hover:border-red-100 transition-all">
                            <span class="text-[10px] font-black uppercase tracking-widest text-gray-400 group-hover:text-[#ff0033]">
                                {{ role.users_count || 0 }} {{ cart.language === 'ar' ? 'مستخدم' : 'Users' }}
                            </span>
                        </div>
                    </div>

                    <h4 class="text-2xl font-black text-gray-900 tracking-tighter mb-2 group-hover:text-[#ff0033] transition-colors">{{ role.label }}</h4>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-6">{{ role.name }}</p>
                    
                    <p class="text-sm font-medium text-gray-500 leading-relaxed line-clamp-2 min-h-[3em]">
                        {{ role.description || (cart.language === 'ar' ? 'لا يوجد وصف متاح لهذا الدور.' : 'No description available for this role.') }}
                    </p>

                    <div class="mt-8 pt-8 border-t border-gray-50 flex items-center justify-between">
                        <span class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 group-hover:text-[#ff0033]">
                            {{ cart.language === 'ar' ? 'عرض التفاصيل' : 'View Configuration' }}
                        </span>
                        <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center text-gray-400 group-hover:bg-[#ff0033] group-hover:text-white transition-all">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                        </div>
                    </div>
                </Link>
            </div>
        </div>
    </AdminLayout>
</template>

