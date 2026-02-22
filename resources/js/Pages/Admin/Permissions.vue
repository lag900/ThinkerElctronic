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
            system.notify(cart.language === 'ar' ? 'تم منح تصريح برمجي جديد لمنظومة الأمان' : 'Hardware protocol permission granted.');
        },
    });
};
</script>

<template>
    <Head :title="cart.language === 'ar' ? 'سجل تصاريح النظام' : 'System Logic Permissions'" />
    
    <AdminLayout>
        <template #header>{{ cart.language === 'ar' ? 'سجل تصاريح النظام والرموز البرمجية' : 'System Logic Auth Permissions' }}</template>

        <div class="space-y-12 font-inter" :dir="cart.language === 'ar' ? 'rtl' : 'ltr'">
            <!-- Command & Strategy Hub -->
            <div class="flex flex-col md:flex-row justify-between items-center bg-white p-10 rounded-[24px] border border-gray-100 shadow-sm gap-10 relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-32 h-32 bg-gray-50 rounded-full -mr-16 -mt-16 opacity-50 group-hover:bg-gray-100 transition-colors"></div>
                <div class="relative z-10" :class="cart.language === 'ar' ? 'text-right' : 'text-left'">
                    <h3 class="text-3xl font-black text-gray-900 tracking-tighter">{{ cart.language === 'ar' ? 'تعريف التصاريح البنيوية' : 'Structural Permission Definitions' }}</h3>
                    <p class="text-xs font-bold text-gray-400 mt-2">{{ cart.language === 'ar' ? 'مفاتيح الوصول الأساسية المشغلة للأدوار الإدارية والتحكم في تدفق البيانات' : 'Core system access keys for strategic role assignments and data flow control' }}</p>
                </div>
                <button 
                    @click="showForm = true"
                    class="w-full md:w-auto bg-gray-900 text-white px-12 py-5 rounded-[16px] font-black text-xs uppercase tracking-[0.2em] shadow-2xl shadow-gray-900/10 hover:bg-gray-800 transition-all active:scale-95 relative z-10"
                >
                    + {{ cart.language === 'ar' ? 'إضافة تصريح جديد' : 'Induct Permission' }}
                </button>
            </div>

            <!-- Permissions Registry Grid -->
            <div class="space-y-12">
                <div v-for="(groupPerms, group) in permissions" :key="group" class="space-y-8">
                    <div class="flex items-center gap-6">
                        <h4 class="text-lg font-black text-gray-900 tracking-tighter uppercase px-6 py-2 bg-gray-900 text-white rounded-xl shadow-lg">{{ group }}</h4>
                        <div class="h-[1px] bg-gray-100 flex-1"></div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                        <div v-for="perm in groupPerms" :key="perm.id" class="bg-white p-8 rounded-[24px] border border-gray-100 shadow-sm hover:shadow-2xl hover:shadow-gray-900/5 transition-all group relative overflow-hidden">
                            <div class="absolute top-0 left-0 w-full h-1 bg-gray-900/5 group-hover:bg-gray-900 transition-colors"></div>
                            <div class="flex items-center justify-between mb-6">
                                <div class="w-10 h-10 bg-gray-50 rounded-[12px] flex items-center justify-center font-black text-[12px] text-gray-400 border border-gray-100 group-hover:bg-gray-900 group-hover:text-white transition-all shadow-inner">
                                    P
                                </div>
                                <span class="text-[9px] font-black uppercase text-gray-300 tracking-[0.2em]">{{ cart.language === 'ar' ? 'مفتاح النظام' : 'System Node' }}</span>
                            </div>
                            <h5 class="text-sm font-black text-gray-900 tracking-tight transition-colors">{{ perm.label }}</h5>
                            <code class="text-[10px] font-bold text-gray-400 mt-4 block break-all bg-gray-50 p-3 rounded-lg border border-gray-100 uppercase tracking-tighter group-hover:border-gray-200 transition-all">{{ perm.name }}</code>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Structural Permission Injection Modal -->
        <div v-if="showForm" class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-gray-900/60 backdrop-blur-md" :dir="cart.language === 'ar' ? 'rtl' : 'ltr'">
            <div class="bg-white w-full max-w-xl rounded-[32px] relative shadow-2xl border border-gray-100 animate-in zoom-in-95 duration-500 overflow-hidden">
                <div class="h-2 bg-gray-900"></div>
                <div class="p-12">
                    <button @click="showForm = false" class="absolute top-10 left-10 p-3 text-gray-400 hover:text-gray-900 bg-gray-50 rounded-full hover:rotate-90 transition-all duration-300">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>

                    <h3 class="text-3xl font-black text-gray-900 tracking-tighter mb-2">{{ cart.language === 'ar' ? 'إنشاء فجوة تصريح' : 'Logic Permission Induction' }}</h3>
                    <p class="text-[11px] font-black uppercase tracking-[0.2em] text-gray-400 mb-12">{{ cart.language === 'ar' ? 'تعريف مفتاح وصول برمجي جديد للوظائف الأساسية للنظام' : 'Define a new system access key for structural logic control' }}</p>

                    <form @submit.prevent="submitForm" class="space-y-8">
                        <div class="space-y-3">
                            <label class="text-[11px] font-black uppercase tracking-widest text-gray-500 px-1">{{ cart.language === 'ar' ? 'رمز التصريح الفريد (مثال: users.edit)' : 'Hardware Logic Key' }}</label>
                            <input v-model="form.name" type="text" class="w-full bg-gray-50 border-none rounded-[16px] px-8 py-5 text-sm font-black text-gray-900 shadow-inner focus:ring-2 focus:ring-gray-900 transition-all" placeholder="module.action" required>
                        </div>

                        <div class="space-y-3">
                            <label class="text-[11px] font-black uppercase tracking-widest text-gray-500 px-1">{{ cart.language === 'ar' ? 'الاسم المعروض للتصنيف' : 'Human-Readable Sector' }}</label>
                            <input v-model="form.label" type="text" :placeholder="cart.language === 'ar' ? 'مثال: تعديل بيانات الموظفين' : 'e.g. Modify Personnel Data'" class="w-full bg-gray-50 border-none rounded-[16px] px-8 py-5 text-sm font-black text-gray-900 shadow-inner focus:ring-2 focus:ring-gray-900 transition-all" required>
                        </div>

                        <div class="space-y-3">
                            <label class="text-[11px] font-black uppercase tracking-widest text-gray-500 px-1">{{ cart.language === 'ar' ? 'قطاع النظام / المجموعة' : 'Strategic Cluster' }}</label>
                            <input v-model="form.group" type="text" :placeholder="cart.language === 'ar' ? 'مثال: التسويق، الموارد البشرية...' : 'e.g. Logistics, Core Security...'" class="w-full bg-gray-50 border-none rounded-[16px] px-8 py-5 text-sm font-black text-gray-900 shadow-inner focus:ring-2 focus:ring-gray-900 transition-all" required>
                        </div>

                        <div class="flex gap-6 pt-6">
                            <button type="button" @click="showForm = false" class="flex-1 bg-gray-100 rounded-[16px] py-6 font-black uppercase tracking-[0.3em] text-[11px] text-gray-400 hover:bg-gray-200 transition-all active:scale-95">{{ cart.language === 'ar' ? 'إلغاء' : 'Abort' }}</button>
                            <button 
                                type="submit" 
                                :disabled="form.processing"
                                class="flex-1 bg-gray-900 text-white rounded-[16px] py-6 font-black uppercase tracking-[0.3em] text-[11px] shadow-2xl shadow-gray-900/30 hover:bg-emerald-600 transition-all disabled:opacity-50 active:scale-95"
                            >
                                {{ cart.language === 'ar' ? 'إنشاء التصريح' : 'Commit Gate' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
