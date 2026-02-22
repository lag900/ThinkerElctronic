<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { cart } from '@/Stores/CartStore';
import { system } from '@/Stores/SystemStore';

const props = defineProps({
    settings: Object
});

const form = useForm({
    google_verification_code: props.settings.google_verification_code,
    global_meta_title: props.settings.global_meta_title,
    global_meta_description: props.settings.global_meta_description,
    global_meta_keywords: props.settings.global_meta_keywords,
});

const submit = () => {
    form.post(route('admin.seo.settings.update'), {
        onSuccess: () => {
             system.notify('SEO Matrix Synchronized Successfully!', 'success');
        }
    });
};
</script>

<template>
    <Head title="SEO Optimization Control" />

    <AdminLayout>
        <template #header>{{ cart.language === 'ar' ? 'تحسين محركات البحث' : 'SEO Optimization' }}</template>

        <div class="max-w-4xl mx-auto space-y-12 pb-20" :dir="cart.language === 'ar' ? 'rtl' : 'ltr'">
            <!-- Header Section -->
            <div class="flex items-center justify-between">
                <div class="space-y-2">
                    <h3 class="text-3xl font-black text-[#1e1e1e] tracking-tight">{{ cart.language === 'ar' ? 'مصفوفة محركات البحث العالمية' : 'Global SEO Matrix' }}</h3>
                    <p class="text-xs font-black text-gray-400 uppercase tracking-widest">{{ cart.language === 'ar' ? 'إدارة التواجد الرقمي والظهور في جوجل' : 'Configure root indexing and search engine visibility' }}</p>
                </div>
                <div class="w-16 h-16 bg-[#fff3f3] rounded-[24px] flex items-center justify-center text-[#ff2b2b] shadow-sm">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
            </div>

            <form @submit.prevent="submit" class="space-y-10">
                <!-- Google Console Verification -->
                <div class="bg-white p-10 rounded-[40px] border border-[#f1f1f1] shadow-[0_20px_50px_rgba(0,0,0,0.02)] space-y-8 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-brand-50/50 rounded-bl-full -mr-12 -mt-12 group-hover:scale-110 transition-transform duration-700"></div>
                    
                    <div class="flex items-center gap-4 pb-6 border-b border-[#f8f8f8]">
                         <div class="w-10 h-10 bg-brand-500 rounded-xl flex items-center justify-center text-white">
                             <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                         </div>
                         <h4 class="text-sm font-black text-[#1e1e1e] uppercase tracking-tighter">{{ cart.language === 'ar' ? 'توثيق جوجل كونسول' : 'Google Console Verification' }}</h4>
                    </div>

                    <div class="space-y-3">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 px-1">{{ cart.language === 'ar' ? 'رمز التحقق (Verification Tag)' : 'Google Site Verification Tag' }}</label>
                        <input v-model="form.google_verification_code" type="text" placeholder="e.g. wXyZ123..." class="w-full bg-[#fcfcfc] border border-[#f1f1f1] rounded-[20px] py-5 px-8 text-sm font-bold text-gray-900 focus:ring-4 focus:ring-brand-500/5 focus:border-brand-500 outline-none transition-all shadow-sm">
                        <p class="text-[9px] font-bold text-gray-400 leading-relaxed max-w-lg mt-2">
                             {{ cart.language === 'ar' ? 'أدخل الرمز الموجود في وسم content الخاص بـ google-site-verification' : 'Enter the verification string from your Google Search Console meta tag' }}
                        </p>
                    </div>
                </div>

                <!-- Global Brand SEO -->
                <div class="bg-white p-10 rounded-[40px] border border-[#f1f1f1] shadow-[0_20px_50px_rgba(0,0,0,0.02)] space-y-8">
                    <div class="flex items-center gap-4 pb-6 border-b border-[#f8f8f8]">
                         <div class="w-10 h-10 bg-[#1e90ff] rounded-xl flex items-center justify-center text-white shadow-[0_10px_20px_rgba(30,144,255,0.2)]">
                             <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                         </div>
                         <h4 class="text-sm font-black text-[#1e1e1e] uppercase tracking-tighter">{{ cart.language === 'ar' ? 'بيانات البحث الافتراضية' : 'Default Global Search Tags' }}</h4>
                    </div>

                    <div class="grid grid-cols-1 gap-10">
                        <div class="space-y-3">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 px-1">{{ cart.language === 'ar' ? 'عنوان الموقع بالكامل' : 'Global Title Tail' }}</label>
                            <input v-model="form.global_meta_title" type="text" placeholder="Thinker Technology Store" class="w-full bg-[#fcfcfc] border border-[#f1f1f1] rounded-[20px] py-5 px-8 text-sm font-bold text-gray-900 focus:ring-4 focus:ring-[#1e90ff]/5 focus:border-[#1e90ff] outline-none transition-all shadow-sm">
                        </div>

                        <div class="space-y-3">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 px-1">{{ cart.language === 'ar' ? 'وصف الموقع العام' : 'Global Meta Description' }}</label>
                            <textarea v-model="form.global_meta_description" rows="4" placeholder="Description for home page and fallbacks..." class="w-full bg-[#fcfcfc] border border-[#f1f1f1] rounded-[24px] py-6 px-8 text-sm font-bold text-gray-900 focus:ring-4 focus:ring-[#1e90ff]/5 focus:border-[#1e90ff] outline-none transition-all shadow-sm resize-none"></textarea>
                        </div>

                        <div class="space-y-3">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 px-1">{{ cart.language === 'ar' ? 'الكلمات الافتتاحية' : 'Global Keywords' }}</label>
                            <input v-model="form.global_meta_keywords" type="text" placeholder="electronics, smartphones, egypt..." class="w-full bg-[#fcfcfc] border border-[#f1f1f1] rounded-[20px] py-5 px-8 text-sm font-bold text-gray-900 focus:ring-4 focus:ring-[#1e90ff]/5 focus:border-[#1e90ff] outline-none transition-all shadow-sm">
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-10 pt-10 border-t border-[#f8f8f8]">
                    <button type="submit" class="w-full sm:w-auto px-20 py-5 bg-gradient-to-r from-brand-500 to-brand-600 text-white rounded-[20px] font-black uppercase tracking-[0.2em] text-xs shadow-[0_20px_40px_rgba(255,43,43,0.3)] hover:scale-[1.02] hover:-translate-y-1 active:scale-95 transition-all disabled:opacity-50" :disabled="form.processing">
                        {{ form.processing ? 'Persisting...' : (cart.language === 'ar' ? 'تحديث مصفوفة SEO' : 'Commit SEO Matrix') }}
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
