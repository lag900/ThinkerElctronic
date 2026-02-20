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
    { key: 'phone', label: 'Phone Number' },
    { key: 'whatsapp', label: 'WhatsApp' },
    { key: 'email', label: 'Email Address' },
    { key: 'description', label: 'Description Text', type: 'textarea' },
    { key: 'facebook', label: 'Facebook Link' },
    { key: 'instagram', label: 'Instagram Link' },
    { key: 'address', label: 'Physical Address' },
    { key: 'map_embed', label: 'Google Map Embed Code', type: 'textarea' },
];

const submit = () => {
    let data = { ...form };
    if (props.page.slug === 'contact') {
        data.content_en = JSON.stringify(form.content_en);
        data.content_ar = JSON.stringify(form.content_ar);
    }
    form.transform((data) => ({
        ...data,
        content_en: props.page.slug === 'contact' ? JSON.stringify(form.content_en) : form.content_en,
        content_ar: props.page.slug === 'contact' ? JSON.stringify(form.content_ar) : form.content_ar,
    })).put(route('admin.content.pages.update', props.page.id), {
        onSuccess: () => system.notify('Page content synchronized'),
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
        <template #header>{{ cart.language === 'ar' ? 'إدارة المحتوى' : 'Content Intelligence' }} - {{ page.slug }}</template>

        <div class="max-w-7xl mx-auto">
            <form @submit.prevent="submit" class="space-y-12">
                <!-- Header Control -->
                <div class="flex justify-between items-center bg-white p-10 rounded-[50px] border border-slate-50 shadow-[0_20px_60px_rgba(0,0,0,0.02)]">
                    <div>
                        <h3 class="text-2xl font-black text-[#222] tracking-tighter capitalize">{{ page.slug.replace('-', ' ') }}</h3>
                        <p class="text-[10px] font-black text-slate-300 uppercase tracking-[0.3em] mt-2">Dynamic System Page Node</p>
                    </div>
                    <div class="flex gap-4">
                         <a :href="route(page.slug)" target="_blank" class="bg-slate-100 hover:bg-slate-200 text-[#222] px-8 py-5 rounded-[28px] font-black uppercase tracking-widest text-[11px] transition-all active:scale-95 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                            Preview
                        </a>
                        <button 
                            type="submit" 
                            :disabled="form.processing"
                            class="bg-[#222] hover:bg-brand-500 text-white px-10 py-5 rounded-[28px] font-black uppercase tracking-widest text-[11px] transition-all shadow-xl shadow-black/10 active:scale-95 disabled:opacity-50"
                        >
                            {{ form.processing ? 'Synchronizing...' : 'Save Changes' }}
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 text-[10px] font-black uppercase tracking-[0.3em] text-slate-300">
                    <!-- English Version -->
                    <div class="space-y-8 bg-white p-12 rounded-[60px] border border-slate-50 shadow-[0_40px_80px_rgba(0,0,0,0.02)]">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-8 h-8 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400">EN</div>
                            <span class="text-slate-400">English Specifications</span>
                        </div>
                        
                        <div class="space-y-3">
                            <label class="ml-6 uppercase">Page Title</label>
                            <input v-model="form.title_en" type="text" class="w-full bg-slate-50 border-none rounded-[30px] px-8 py-5 text-sm font-bold focus:ring-2 focus:ring-brand-500/20 shadow-inner">
                        </div>

                        <template v-if="page.slug !== 'contact'">
                            <div class="space-y-3">
                                <label class="ml-6 uppercase">Content Body (HTML Supported)</label>
                                <textarea v-model="form.content_en" rows="20" class="w-full bg-slate-50 border-none rounded-[40px] px-8 py-8 text-sm font-medium leading-relaxed focus:ring-2 focus:ring-brand-500/20 shadow-inner"></textarea>
                            </div>
                        </template>
                        <template v-else>
                            <div v-for="field in contactFields" :key="field.key" class="space-y-3">
                                <label class="ml-6 uppercase">{{ field.label }}</label>
                                <textarea v-if="field.type === 'textarea'" v-model="form.content_en[field.key]" rows="3" class="w-full bg-slate-50 border-none rounded-[30px] px-8 py-5 text-sm font-bold focus:ring-2 focus:ring-brand-500/20 shadow-inner"></textarea>
                                <input v-else v-model="form.content_en[field.key]" type="text" class="w-full bg-slate-50 border-none rounded-[30px] px-8 py-5 text-sm font-bold focus:ring-2 focus:ring-brand-500/20 shadow-inner">
                            </div>
                        </template>
                    </div>

                    <!-- Arabic Version -->
                    <div class="space-y-8 bg-white p-12 rounded-[60px] border border-slate-50 shadow-[0_40px_80px_rgba(0,0,0,0.02)]" dir="rtl">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-8 h-8 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400">AR</div>
                            <span class="text-slate-400">المواصفات العربية</span>
                        </div>

                        <div class="space-y-3">
                            <label class="mr-6 font-sans uppercase">عنوان الصفحة</label>
                            <input v-model="form.title_ar" dir="rtl" type="text" class="w-full bg-slate-50 border-none rounded-[30px] px-8 py-5 text-sm font-bold focus:ring-2 focus:ring-brand-500/20 font-sans text-right shadow-inner">
                        </div>

                        <template v-if="page.slug !== 'contact'">
                            <div class="space-y-3">
                                <label class="mr-6 font-sans uppercase">محتوى الصفحة (يدعم HTML)</label>
                                <textarea v-model="form.content_ar" dir="rtl" rows="20" class="w-full bg-slate-50 border-none rounded-[40px] px-8 py-8 text-sm font-medium leading-relaxed focus:ring-2 focus:ring-brand-500/20 font-sans text-right shadow-inner"></textarea>
                            </div>
                        </template>
                        <template v-else>
                            <div v-for="field in contactFields" :key="field.key" class="space-y-3">
                                <label class="mr-6 font-sans uppercase">{{ field.label }}</label>
                                <textarea v-if="field.type === 'textarea'" v-model="form.content_ar[field.key]" rows="3" class="w-full bg-slate-50 border-none rounded-[30px] px-8 py-5 text-sm font-bold focus:ring-2 focus:ring-brand-500/20 font-sans text-right shadow-inner"></textarea>
                                <input v-else v-model="form.content_ar[field.key]" type="text" class="w-full bg-slate-50 border-none rounded-[30px] px-8 py-5 text-sm font-bold focus:ring-2 focus:ring-brand-500/20 font-sans text-right shadow-inner">
                            </div>
                        </template>
                    </div>
                </div>
                
                <div class="text-center p-10 bg-slate-50/50 rounded-[40px] border border-dashed border-slate-200">
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">System Traceability ID: {{ page.id }} | Last Synchronized: {{ new Date(page.updated_at).toLocaleString() }}</p>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
