<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { cart } from '@/Stores/CartStore';
import { system } from '@/Stores/SystemStore';
import { ref } from 'vue';

const props = defineProps({
    packages: Array,
});

const showForm = ref(false);
const editingPackage = ref(null);

const form = useForm({
    name: '',
    points: '',
    price: '',
    payment_link: '',
    active: true,
    sort_order: 0,
});

const openCreateForm = () => {
    editingPackage.value = null;
    form.reset();
    form.sort_order = props.packages.length > 0 ? Math.max(...props.packages.map(p => p.sort_order)) + 1 : 1;
    showForm.value = true;
};

const openEditForm = (pkg) => {
    editingPackage.value = pkg;
    form.name = pkg.name || '';
    form.points = pkg.points;
    form.price = pkg.price;
    form.payment_link = pkg.payment_link || '';
    form.active = pkg.active ? true : false;
    form.sort_order = pkg.sort_order;
    showForm.value = true;
};

const submitForm = () => {
    if (editingPackage.value) {
        form.put(route('lms-packages.update', editingPackage.value.id), {
            onSuccess: () => {
                showForm.value = false;
                system.notify(cart.language === 'ar' ? 'تم تحديث معايير باقة النقاط بنجاح' : 'LMS Package updated');
            },
        });
    } else {
        form.post(route('lms-packages.store'), {
            onSuccess: () => {
                showForm.value = false;
                system.notify(cart.language === 'ar' ? 'تم حقن باقة النقاط الجديدة في النظام' : 'LMS Package created');
            },
        });
    }
};

const deletePackage = (pkg) => {
    if (confirm(cart.language === 'ar' ? `هل أنت متأكد من حذف باقة (${pkg.points} نقطة) بشكل نهائي؟` : `Remove package node ${pkg.points} PTS?`)) {
        router.delete(route('lms-packages.destroy', pkg.id), {
            onSuccess: () => system.notify(cart.language === 'ar' ? 'تم حذف عقدة الباقة' : 'Package removed', 'error'),
        });
    }
};

const toggleActive = (pkg) => {
    const nextState = !pkg.active;
    router.put(route('lms-packages.update', pkg.id), {
        ...pkg,
        active: nextState,
        _method: 'PUT'
    }, {
        onSuccess: () => system.notify(cart.language === 'ar' ? `تم ${nextState ? 'تفعيل' : 'تعطيل'} الباقة بنجاح` : `Package ${nextState ? 'activated' : 'deactivated'}`),
        preserveScroll: true
    });
};
</script>

<template>
    <Head :title="cart.language === 'ar' ? 'إدارة نقاط الطباعة' : 'LMS Infrastructure'" />

    <AdminLayout>
        <template #header>{{ cart.language === 'ar' ? 'إدارة باقات نقاط الطباعة (LMS)' : 'LMS Dynamic Point Packaging' }}</template>

        <div class="space-y-10 font-inter" :dir="cart.language === 'ar' ? 'rtl' : 'ltr'">
            <!-- Management Control Hub -->
            <div class="flex flex-col md:flex-row justify-between items-center bg-white p-10 rounded-[24px] border border-gray-100 shadow-sm gap-10 relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-32 h-32 bg-gray-50 rounded-full -mr-16 -mt-16 opacity-50 group-hover:bg-gray-100 transition-colors"></div>
                <div class="relative z-10" :class="cart.language === 'ar' ? 'text-right' : 'text-left'">
                    <h3 class="text-3xl font-black text-gray-900 tracking-tighter">{{ cart.language === 'ar' ? 'التحكم في باقات النقاط' : 'Atomic Package Control' }}</h3>
                    <p class="text-xs font-bold text-gray-400 mt-2">{{ cart.language === 'ar' ? 'تهيئة باقات النقاط المخصصة لخدمات الطباعة الرقمية وتوزيعها عبر واجهة المستخدم' : 'Configure point packages for printing services and user-facing distribution' }}</p>
                </div>
                <button 
                    @click="openCreateForm"
                    class="w-full md:w-auto bg-gray-900 text-white px-12 py-5 rounded-[16px] font-black text-xs uppercase tracking-[0.2em] shadow-2xl shadow-gray-900/10 hover:bg-gray-800 transition-all active:scale-95 relative z-10"
                >
                    + {{ cart.language === 'ar' ? 'إضافة باقة جديدة' : 'Initialize Package' }}
                </button>
            </div>

            <!-- Packages Visualization Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                <div v-for="pkg in packages" :key="pkg.id" 
                    class="bg-white rounded-[24px] border border-gray-100 shadow-sm overflow-hidden group hover:shadow-2xl hover:shadow-gray-900/5 transition-all duration-500 relative flex flex-col"
                    :class="!pkg.active && 'opacity-60 saturate-50 bg-gray-50'"
                >
                    <div class="p-10 text-center flex-1">
                        <div class="w-24 h-24 bg-gray-50 rounded-[28px] mx-auto flex items-center justify-center font-black text-gray-900 text-3xl mb-6 shadow-inner group-hover:bg-gray-900 group-hover:text-white transition-all duration-500 transform group-hover:scale-110">
                            {{ pkg.points }}
                        </div>
                        <h4 class="text-xl font-black text-gray-900 tracking-tighter uppercase">{{ pkg.points }} {{ cart.language === 'ar' ? 'نقطة' : 'Pts' }}</h4>
                        <p class="text-emerald-600 font-black mt-2 text-sm tracking-tight bg-emerald-50 inline-block px-4 py-1.5 rounded-full">{{ pkg.price }}</p>
                        <p v-if="pkg.name" class="text-[9px] uppercase font-black tracking-widest text-gray-400 mt-4 px-3 py-1 bg-gray-50 rounded-md border border-gray-100">{{ pkg.name }}</p>
                        
                        <div class="mt-10 pt-8 border-t border-gray-50 space-y-5 text-right" :dir="cart.language === 'ar' ? 'rtl' : 'ltr'">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">{{ cart.language === 'ar' ? 'حالة الباقة' : 'Node Status' }}</span>
                                <button @click="toggleActive(pkg)" :class="pkg.active ? 'text-emerald-500 bg-emerald-50' : 'text-gray-300 bg-gray-50'" class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest border border-current shadow-sm hover:scale-105 transition-all">
                                    {{ pkg.active ? (cart.language === 'ar' ? 'نشطة' : 'Online') : (cart.language === 'ar' ? 'غير مفعلة' : 'Offline') }}
                                </button>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">{{ cart.language === 'ar' ? 'بوابة الدفع' : 'Payment Link' }}</span>
                                <span class="px-3 py-1 rounded-md text-[9px] font-black tracking-widest uppercase border" :class="pkg.payment_link ? 'text-blue-500 bg-blue-50 border-blue-100' : 'text-red-400 bg-red-50 border-red-100'">
                                    {{ pkg.payment_link ? (cart.language === 'ar' ? 'مفعل' : 'Ready') : (cart.language === 'ar' ? 'مفقود' : 'Void') }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">{{ cart.language === 'ar' ? 'ترتيب الظهور' : 'Registry Priority' }}</span>
                                <span class="text-sm font-black text-gray-900 bg-gray-50 w-8 h-8 flex items-center justify-center rounded-lg border border-gray-100">{{ pkg.sort_order }}</span>
                            </div>
                        </div>
                    </div>
                    <!-- Action Overlay -->
                    <div class="p-6 bg-gray-50 border-t border-gray-100 flex gap-4">
                        <button @click="openEditForm(pkg)" class="flex-1 bg-white border border-gray-200 py-3 rounded-[12px] text-gray-900 hover:bg-gray-900 hover:text-white hover:border-gray-900 transition-all font-black text-[10px] uppercase tracking-[0.2em] shadow-sm">{{ cart.language === 'ar' ? 'تعديل' : 'Modify' }}</button>
                        <button @click="deletePackage(pkg)" class="p-3 bg-white border border-gray-200 rounded-[12px] text-gray-400 hover:text-red-600 hover:border-red-600 transition-all shadow-sm active:scale-90">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </button>
                    </div>
                </div>
            </div>

            <div v-if="!packages || packages.length === 0" class="bg-gray-50 border-2 border-dashed border-gray-200 rounded-[32px] py-40 text-center">
                <div class="text-6xl mb-8 grayscale opacity-20">🏷️</div>
                <p class="text-gray-400 font-black uppercase tracking-[0.3em] text-[11px]">{{ cart.language === 'ar' ? 'لا توجد باقات نقاط معدة حالياً بالنظام' : 'Zero Package Nodes Configured' }}</p>
                <button @click="openCreateForm" class="mt-8 text-gray-900 font-black uppercase tracking-widest text-xs underline decoration-2 underline-offset-8 hover:text-gray-600 transition-all">{{ cart.language === 'ar' ? 'ابدأ بإضافة أول باقة الآن' : 'Initialize First Node' }}</button>
            </div>
        </div>

        <!-- Structural Package Configuration Modal -->
        <div v-if="showForm" class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-gray-900/60 backdrop-blur-md" :dir="cart.language === 'ar' ? 'rtl' : 'ltr'">
            <div class="bg-white w-full max-w-xl rounded-[28px] relative z-10 overflow-hidden shadow-2xl border border-gray-100 animate-in zoom-in-95 duration-500">
                <div class="h-2 bg-gray-900"></div>
                <div class="p-12">
                    <button @click="showForm = false" class="absolute top-10 left-10 p-3 text-gray-400 hover:text-gray-900 bg-gray-50 rounded-full hover:rotate-90 transition-all duration-300">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>

                    <h3 class="text-3xl font-black text-gray-900 tracking-tighter mb-2">
                        {{ editingPackage ? (cart.language === 'ar' ? 'تعديل معايير الباقة' : 'Modify Package Cluster') : (cart.language === 'ar' ? 'حقن باقة نقاط جديدة' : 'Induct New Package Hub') }}
                    </h3>
                    <p class="text-[11px] font-black uppercase tracking-[0.2em] text-gray-400 mb-12">{{ cart.language === 'ar' ? 'أدخل تفاصيل الباقة الرقمية وربطها ببابة الدفع الإلكترونية' : 'Enter digital package details and synchronize with payment gateway integration' }}</p>

                    <form @submit.prevent="submitForm" class="space-y-8 text-right">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-2">
                                <label class="text-[11px] font-black uppercase tracking-widest text-gray-500 px-1">{{ cart.language === 'ar' ? 'عدد النقاط المكتسبة' : 'Point Density' }}</label>
                                <input v-model="form.points" type="number" class="w-full bg-gray-50 border-none rounded-[16px] px-8 py-5 text-lg font-black text-gray-900 shadow-inner focus:ring-2 focus:ring-gray-900 transition-all" placeholder="500" required>
                            </div>
                            <div class="space-y-2">
                                <label class="text-[11px] font-black uppercase tracking-widest text-gray-500 px-1">{{ cart.language === 'ar' ? 'سعر البيع المقترح' : 'Unit Price' }}</label>
                                <input v-model="form.price" type="text" placeholder="e.g. 50 EGP" class="w-full bg-emerald-50 border-none rounded-[16px] px-8 py-5 text-lg font-black text-emerald-600 shadow-inner focus:ring-2 focus:ring-emerald-500/20 transition-all" required>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[11px] font-black uppercase tracking-widest text-gray-500 px-1">{{ cart.language === 'ar' ? 'المسمى التسويقي (اختياري)' : 'Strategic Label (Optional)' }}</label>
                            <input v-model="form.name" type="text" :placeholder="cart.language === 'ar' ? 'مثال: باقة المبتدئين، الباقة الذهبية...' : 'e.g. Starter Pack, Enterprise Node...'" class="w-full bg-gray-50 border-none rounded-[16px] px-8 py-5 text-sm font-black text-gray-900 shadow-inner focus:ring-2 focus:ring-gray-900 transition-all">
                        </div>

                        <div class="space-y-2">
                            <label class="text-[11px] font-black uppercase tracking-widest text-gray-500 px-1">{{ cart.language === 'ar' ? 'رابط بوابة الدفع الإلكتروني' : 'Gateway Endpoint (URL)' }}</label>
                            <input v-model="form.payment_link" type="url" placeholder="https://pay.example.com/flux/..." class="w-full bg-gray-50 border-none rounded-[16px] px-8 py-5 text-sm font-black text-gray-900 shadow-inner focus:ring-2 focus:ring-gray-900 transition-all">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-2">
                                <label class="text-[11px] font-black uppercase tracking-widest text-gray-500 px-1">{{ cart.language === 'ar' ? 'أولوية العرض (ترتيب)' : 'Registry Priority' }}</label>
                                <input v-model="form.sort_order" type="number" class="w-full bg-gray-50 border-none rounded-[16px] px-8 py-5 text-sm font-black text-gray-900 shadow-inner focus:ring-2 focus:ring-gray-900 transition-all" required>
                            </div>
                            <div class="space-y-2">
                                <label class="text-[11px] font-black uppercase tracking-widest text-gray-500 px-1">{{ cart.language === 'ar' ? 'الحالة التشغيلية' : 'Connectivity State' }}</label>
                                <div @click="form.active = !form.active" class="flex items-center justify-between bg-gray-50 rounded-[16px] px-8 py-5 h-[64px] border border-transparent hover:border-gray-200 transition-all cursor-pointer shadow-inner">
                                    <span class="text-xs font-black text-gray-700 uppercase tracking-widest">{{ cart.language === 'ar' ? 'تفعيل العرض في المتجر' : 'Online Presence' }}</span>
                                    <div class="w-10 h-5 rounded-full p-1 transition-all" :class="form.active ? 'bg-gray-900' : 'bg-gray-300'">
                                        <div class="w-3 h-3 bg-white rounded-full transition-all" :class="form.active ? (cart.language === 'ar' ? '-translate-x-5' : 'translate-x-5') : 'translate-x-0'"></div>
                                    </div>
                                    <input v-model="form.active" type="checkbox" class="hidden">
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-6 pt-6">
                            <button type="button" @click="showForm = false" class="flex-1 bg-gray-100 rounded-[16px] py-6 font-black uppercase tracking-[0.3em] text-[11px] text-gray-600 hover:bg-gray-200 transition-all active:scale-95">{{ cart.language === 'ar' ? 'إلغاء' : 'Abort' }}</button>
                            <button 
                                type="submit" 
                                :disabled="form.processing"
                                class="flex-1 bg-gray-900 text-white rounded-[16px] py-6 font-black uppercase tracking-[0.3em] text-[11px] shadow-2xl shadow-gray-900/30 hover:bg-gray-800 transition-all disabled:opacity-50 active:scale-95"
                            >
                                {{ editingPackage ? (cart.language === 'ar' ? 'حفظ التغييرات' : 'Commit Hub') : (cart.language === 'ar' ? 'إنشاء الباقة' : 'Induct Hub') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
