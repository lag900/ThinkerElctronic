<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { cart } from '@/Stores/CartStore';
import { system } from '@/Stores/SystemStore';

const props = defineProps({
    page: Object,
});

const form = useForm({
    title_en: props.page.title_en || '',
    title_ar: props.page.title_ar || '',
    content_en: props.page.slug === 'contact' ? (tryJson(props.page.content_en) || props.page.content_en) : (props.page.content_en || ''),
    content_ar: props.page.slug === 'contact' ? (tryJson(props.page.content_ar) || props.page.content_ar) : (props.page.content_ar || ''),
});

function tryJson(str) {
    try { return JSON.parse(str); } catch (e) { return null; }
}

const contactFields = [
    { key: 'phone', labelAr: 'رقم الهاتف الرئيسي', labelEn: 'Primary Phone' },
    { key: 'whatsapp', labelAr: 'رابط الواتساب المباشر', labelEn: 'Direct WhatsApp' },
    { key: 'email', labelAr: 'البريد الإلكتروني الرسمي', labelEn: 'Official Email' },
    { key: 'description', labelAr: 'النص التعريفي للقسم', labelEn: 'Section Narrative', type: 'textarea' },
    { key: 'facebook', labelAr: 'رابط الفيسبوك', labelEn: 'Facebook Source' },
    { key: 'instagram', labelAr: 'رابط الانستجرام', labelEn: 'Instagram Source' },
    { key: 'address', labelAr: 'العنوان الجغرافي / المقر', labelEn: 'HQ Physical Address' },
    { key: 'map_embed', labelAr: 'كود تضمين خريطة جوجل', labelEn: 'Google Maps Logic', type: 'textarea' },
];

const submit = () => {
    form.transform((data) => ({
        ...data,
        content_en: props.page.slug === 'contact' ? JSON.stringify(form.content_en) : form.content_en,
        content_ar: props.page.slug === 'contact' ? JSON.stringify(form.content_ar) : form.content_ar,
    })).put(route('admin.content.pages.update', props.page.id), {
        onSuccess: () => system.notify(cart.language === 'ar' ? 'تمت مزامنة محتوى الصفحة بنجاح' : 'Page content synchronized'),
    });
};

// Initialize contact fields if empty
if (props.page.slug === 'contact') {
    if (typeof form.content_en !== 'object' || form.content_en === null) {
        form.content_en = { phone: '', whatsapp: '', email: '', description: '', facebook: '', instagram: '', address: '', map_embed: '' };
    }
    if (typeof form.content_ar !== 'object' || form.content_ar === null) {
        form.content_ar = { phone: '', whatsapp: '', email: '', description: '', facebook: '', instagram: '', address: '', map_embed: '' };
    }
}
</script>

<template>
    <Head :title="`CMS: ${page.slug}`" />

    <AdminLayout>
        <template #header>{{ cart.language === 'ar' ? 'محرر محتوى الصفحات التعريفية (CMS)' : 'Dynamic CMS Page Orchestrator' }}</template>

        <div class="max-w-7xl mx-auto font-inter" :dir="cart.language === 'ar' ? 'rtl' : 'ltr'">
            <form @submit.prevent="submit" class="space-y-10">
                <!-- Strategic Command Bar -->
                <div class="flex flex-col md:flex-row justify-between items-center bg-white p-10 rounded-[24px] border border-gray-100 shadow-sm gap-10 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-gray-50 rounded-full -mr-16 -mt-16 opacity-50 group-hover:bg-gray-100 transition-colors"></div>
                    <div class="relative z-10" :class="cart.language === 'ar' ? 'text-right' : 'text-left'">
                        <h3 class="text-3xl font-black text-gray-900 tracking-tighter uppercase">{{ page.slug.replace('-', ' ') }}</h3>
                        <p class="text-xs font-bold text-gray-400 mt-2">{{ cart.language === 'ar' ? 'إدارة النصوص والترجمات والروابط للصفحات العامة بالموقع الرسمي' : 'Manage strategic narratives, translations, and endpoints for public pages' }}</p>
                    </div>
                    <div class="flex gap-4 w-full md:w-auto relative z-10">
                         <a :href="page.slug === 'contact' ? '/contact' : '/#p-'+page.id" target="_blank" class="flex-1 md:flex-none justify-center bg-white border border-gray-200 text-gray-700 px-10 py-5 rounded-[16px] font-black text-[11px] uppercase tracking-widest transition-all hover:bg-gray-50 flex items-center gap-3 shadow-sm active:scale-95">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                            {{ cart.language === 'ar' ? 'معاينة العرض' : 'Live Preview' }}
                        </a>
                        <button 
                            type="submit" 
                            :disabled="form.processing"
                            class="flex-1 md:flex-none bg-gray-900 text-white px-12 py-5 rounded-[16px] font-black text-[11px] uppercase tracking-[0.2em] transition-all shadow-2xl shadow-gray-900/40 hover:bg-gray-800 disabled:opacity-50 active:scale-95"
                        >
                            {{ form.processing ? (cart.language === 'ar' ? 'جاري المزامنة...' : 'Syncing...') : (cart.language === 'ar' ? 'حفظ التغييرات' : 'Persist Content') }}
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                    <!-- English Node -->
                    <div class="space-y-8 bg-white p-12 rounded-[28px] border border-gray-100 shadow-sm relative overflow-hidden" dir="ltr">
                        <div class="absolute top-0 right-0 w-1.5 h-full bg-blue-500 opacity-10"></div>
                        <div class="flex items-center gap-4 pb-6 border-b border-gray-100 mb-4">
                            <div class="w-10 h-10 rounded-xl bg-gray-900 text-white flex items-center justify-center text-xs font-black shadow-lg">EN</div>
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em]">English Narrative Source</span>
                        </div>
                        
                        <div class="space-y-3">
                            <label class="text-[11px] font-black uppercase tracking-widest text-gray-500 px-1">Structural Title</label>
                            <input v-model="form.title_en" type="text" class="w-full bg-gray-50 border-none rounded-[16px] px-8 py-5 text-sm font-black text-gray-900 shadow-inner focus:ring-2 focus:ring-gray-900 transition-all" placeholder="Enter English Title...">
                        </div>

                        <template v-if="page.slug !== 'contact'">
                            <div class="space-y-3">
                                <label class="text-[11px] font-black uppercase tracking-widest text-gray-500 px-1">Narrative Content (HTML Logic Supported)</label>
                                <textarea v-model="form.content_en" rows="20" class="w-full bg-gray-50 border-none rounded-[16px] px-8 py-8 text-sm font-medium leading-relaxed shadow-inner focus:ring-2 focus:ring-gray-900 text-gray-900" placeholder="Enter English content nodes..."></textarea>
                            </div>
                        </template>
                        <template v-else>
                            <div v-for="field in contactFields" :key="field.key" class="space-y-3">
                                <label class="text-[11px] font-black uppercase tracking-widest text-gray-500 px-1">{{ field.labelEn }}</label>
                                <textarea v-if="field.type === 'textarea'" v-model="form.content_en[field.key]" rows="4" class="w-full bg-gray-50 border-none rounded-[16px] px-8 py-5 text-sm font-black shadow-inner focus:ring-2 focus:ring-gray-900 text-gray-900"></textarea>
                                <input v-else v-model="form.content_en[field.key]" type="text" class="w-full bg-gray-50 border-none rounded-[16px] px-8 py-5 text-sm font-black shadow-inner focus:ring-2 focus:ring-gray-900 text-gray-900">
                            </div>
                        </template>
                    </div>

                    <!-- Arabic Node -->
                    <div class="space-y-8 bg-white p-12 rounded-[28px] border border-gray-100 shadow-sm relative overflow-hidden" dir="rtl">
                        <div class="absolute top-0 right-0 w-1.5 h-full bg-emerald-500 opacity-10"></div>
                        <div class="flex items-center gap-4 pb-6 border-b border-gray-100 mb-4">
                            <div class="w-10 h-10 rounded-xl bg-gray-900 text-white flex items-center justify-center text-xs font-black shadow-lg">AR</div>
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em]">مصدر المحتوى العربي</span>
                        </div>

                        <div class="space-y-3">
                            <label class="text-[11px] font-black uppercase tracking-widest text-gray-500 px-1 text-right">عنوان الصفحة الهيكلي</label>
                            <input v-model="form.title_ar" type="text" class="w-full bg-gray-50 border-none rounded-[16px] px-8 py-5 text-sm font-black text-gray-900 shadow-inner focus:ring-2 focus:ring-gray-900 transition-all text-right" placeholder="أدخل العنوان بالعربية للتصنيف...">
                        </div>

                        <template v-if="page.slug !== 'contact'">
                            <div class="space-y-3">
                                <label class="text-[11px] font-black uppercase tracking-widest text-gray-500 px-1 text-right">متن الصفحة الأصلي (يدعم وسوم HTML)</label>
                                <textarea v-model="form.content_ar" rows="20" class="w-full bg-gray-50 border-none rounded-[16px] px-8 py-8 text-sm font-medium leading-relaxed shadow-inner focus:ring-2 focus:ring-gray-900 text-gray-900 text-right" placeholder="أدخل المحتوى العربي المخصص للعرض..."></textarea>
                            </div>
                        </template>
                        <template v-else>
                            <div v-for="field in contactFields" :key="field.key" class="space-y-3">
                                <label class="text-[11px] font-black uppercase tracking-widest text-gray-500 px-1 text-right">{{ field.labelAr }}</label>
                                <textarea v-if="field.type === 'textarea'" v-model="form.content_ar[field.key]" rows="4" class="w-full bg-gray-50 border-none rounded-[16px] px-8 py-5 text-sm font-black shadow-inner focus:ring-2 focus:ring-gray-900 text-gray-900 text-right"></textarea>
                                <input v-else v-model="form.content_ar[field.key]" type="text" class="w-full bg-gray-50 border-none rounded-[16px] px-8 py-5 text-sm font-black shadow-inner focus:ring-2 focus:ring-gray-900 text-gray-900 text-right">
                            </div>
                        </template>
                    </div>
                </div>
                
                <div class="text-center p-12 bg-gray-50/50 rounded-[28px] border border-dashed border-gray-200">
                    <p class="text-[11px] font-black text-gray-400 uppercase tracking-[0.3em] flex items-center justify-center gap-4">
                        <span>NODE ID: {{ page.id }}</span>
                        <span class="w-1.5 h-1.5 rounded-full bg-gray-300"></span>
                        <span>LAST SYNC: {{ new Date(page.updated_at).toLocaleString(cart.language === 'ar' ? 'ar-EG' : 'en-US') }}</span>
                    </p>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
