<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { cart } from '@/Stores/CartStore';
import { system } from '@/Stores/SystemStore';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    links: Array,
    logo: String,
    stats: Object,
});

const platforms = [
    { name: 'WhatsApp', icon: 'M3 21l1.65-3.8a9 9 0 1 1 3.4 2.9L3 21', color: '#25D366', urlPrefix: 'https://wa.me/' },
    { name: 'Instagram', icon: 'M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z M17.5 6.5h.01', color: '#E4405F', urlPrefix: 'https://instagram.com/' },
    { name: 'Facebook', icon: 'M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z', color: '#1877F2', urlPrefix: 'https://facebook.com/' },
    { name: 'Messenger', icon: 'M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 1 1-7.6-12.7 8.38 8.38 0 0 1 3.8.9L21 3z', color: '#0084FF', urlPrefix: 'https://m.me/' },
    { name: 'Telegram', icon: 'M21.19 5.11L18.88 21.32A0.73 0.73 0 0 1 17.65 21.64L11.77 17.43L8.63 20.25A0.6 0.6 0 0 1 7.6 19.82L7.6 15.65L17.75 6.43A0.42 0.42 0 0 0 17.15 5.83L6.15 12.78L2.09 11.41A0.67 0.67 0 0 1 2.06 10.15L20.44 3.12A0.73 0.73 0 0 1 21.19 5.11Z', color: '#24A1DE', urlPrefix: 'https://t.me/' },
    { name: 'YouTube', icon: 'M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.42a2.78 2.78 0 0 0-1.94 2C1 8.14 1 12 1 12s0 3.86.46 5.58a2.78 2.78 0 0 0 1.94 2c1.72.42 8.6.42 8.6.42s6.88 0 8.6-.42a2.78 2.78 0 0 0 1.94-2C23 15.86 23 12 23 12s0-3.86-.46-5.58z M9.75 15.02V8.98L15.33 12', color: '#FF0000', urlPrefix: 'https://youtube.com/' },
    { name: 'TikTok', icon: 'M9 12a4 4 0 1 0 4 4V4a5 5 0 0 0 5 5', color: '#000000', urlPrefix: 'https://tiktok.com/@' },
    { name: 'X (Twitter)', icon: 'M4 4l11.733 16h4.267l-11.733-16z M4 20l6.768-6.768 M12.466 12.466l7.534-7.534', color: '#000000', urlPrefix: 'https://x.com/' },
    { name: 'LinkedIn', icon: 'M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z M2 9h4v12H2z M4 2a2 2 0 1 1-2 2 2 2 0 0 1 2-2z', color: '#0A66C2', urlPrefix: 'https://linkedin.com/in/' },
    { name: 'Snapchat', icon: 'M12 3c-2 0-3 1-3 3 0 1 0 2 1 3-1 0-2 1-2 2 0 2 2 2 4 2s4 0 4-2c0-1-1-2-2-2 1-1 1-2 1-3 0-2-1-3-3-3z M12 21c-4 0-7-2-7-5 0-1 1-1 1-1 1 0 2 1 6 1s5-1 6-1c0 0 1 0 1 1 0 3-3 5-7 5z', color: '#FFFC00', urlPrefix: 'https://snapchat.com/add/' },
    { name: 'Pinterest', icon: 'M12 2a10 10 0 0 0-3.5 19.37 10 10 0 0 1-.13-3.64c.2-1.04 1.34-5.67 1.34-5.67a3.25 3.25 0 0 1-.3-1.63 3.5 3.5 0 0 1 4.05-3.46 2.37 2.37 0 0 1 2.37 2.65 6.13 6.13 0 0 1-3.9 5.8 2.6 2.6 0 0 0 2 4.7 5.17 5.17 0 0 0 4.14-5.8 7.37 7.37 0 0 0-7.37-7.37 7.37 7.37 0 0 0-7.37 7.37 7.37 7.37 0 0 0 1.25 4.08', color: '#BD081C', urlPrefix: 'https://pinterest.com/' },
    { name: 'Website', icon: 'M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm7.54 8h-3.32a13.3 13.3 0 0 0-1.46-5.12 8 8 0 0 1 4.78 5.12zM12 4a11.3 11.3 0 0 1 1.7 6h-3.4A11.3 11.3 0 0 1 12 4zm-1.7 6h3.4a11.3 11.3 0 0 1-1.7 6h-3.4a11.3 11.3 0 0 1 1.7-6zm-4.76-1.88a8 8 0 0 1 4.78-5.12 13.3 13.3 0 0 0-1.46 5.12H5.54zM4.46 12h3.32a11.3 11.3 0 0 0 0 4H4.46a8 8 0 0 1 0-4zm1.08 6h3.32a13.3 13.3 0 0 0 1.46 5.12 8 8 0 0 1-4.78-5.12zm8.7 5.12A13.3 13.3 0 0 0 15.7 18h3.32a8 8 0 0 1-4.78 5.12zM19.54 12a8 8 0 0 1 0 4h-3.32a11.3 11.3 0 0 0 0-4h3.32z', color: '#222222', urlPrefix: 'https://' },
    { name: 'Email', icon: 'M4 4h16c1.1 0 2 0.9 2 2v12c0 1.1-0.9 2-2 2H4c-1.1 0-2-0.9-2-2V6c0-1.1 0.9-2 2-2z M22 6l-10 7L2 6', color: '#222222', urlPrefix: 'mailto:' },
    { name: 'Phone', icon: 'M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z', color: '#222222', urlPrefix: 'tel:' },
    { name: 'Maps', icon: 'M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z M12 13a3 3 0 1 0 0-6 3 3 0 0 0 0 6z', color: '#4285F4', urlPrefix: 'https://goo.gl/maps/' },
    { name: 'PayPal', icon: 'M7 2h7c3 0 5 2 5 5s-2 5-5 5h-2l-1 8h-4l3-20z', color: '#003087', urlPrefix: 'https://paypal.me/' },
    { name: 'Github', icon: 'M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22', color: '#181717', urlPrefix: 'https://github.com/' },
    { name: 'Discord', icon: 'M18 10a13.4 13.4 0 0 0-1-1 13.4 13.4 0 0 0-1 1 M6 10a13.4 13.4 0 0 0 1-1 13.4 13.4 0 0 0 1 1 M15.5 17c0-2-1.5-3-3.5-3s-3.5 1-3.5 3', color: '#5865F2', urlPrefix: 'https://discord.gg/' },
];

const showModal = ref(false);
const editingLink = ref(null);

const form = useForm({
    _method: 'POST',
    title: '',
    platform: '',
    url: '',
    color: '#222222',
    sort_order: 0,
    is_active: true,
    custom_icon: null,
});

const logoForm = useForm({
    logo: null,
});

const openCreateModal = () => {
    editingLink.value = null;
    form.reset();
    form._method = 'POST';
    showModal.value = true;
};

const openEditModal = (link) => {
    editingLink.value = link;
    form.title = link.title;
    form.platform = link.platform;
    form.url = link.url;
    form.color = link.color;
    form.sort_order = link.sort_order;
    form.is_active = link.is_active;
    form._method = 'POST';
    showModal.value = true;
};

const submit = () => {
    if (editingLink.value) {
        form.transform((data) => ({
            ...data,
            _method: 'PUT'
        })).post(route('admin.qr.update', editingLink.value.id), {
            forceFormData: true,
            onSuccess: () => {
                showModal.value = false;
                system.notify(cart.language === 'ar' ? 'تمت إعادة تهيئة مسار الرابط الرقمي بنجاح' : 'Quantum node reconfigured');
            },
        });
    } else {
        form.post(route('admin.qr.store'), {
            forceFormData: true,
            onSuccess: () => {
                showModal.value = false;
                system.notify(cart.language === 'ar' ? 'تم حقن مسار التوجيه الجديد في النظام' : 'New vector initialized');
            },
        });
    }
};

watch(() => form.platform, (newVal) => {
    const p = platforms.find(x => x.name === newVal);
    if (p) {
        if (!form.title) form.title = p.name;
        if (!form.url || form.url === 'https://') form.url = p.urlPrefix;
        form.color = p.color;
    }
});

const deleteLink = (id) => {
    if (confirm(cart.language === 'ar' ? 'هل أنت متأكد من تعطيل وحذف هذا المسار التوجيهي نهائياً؟' : 'Decommission this redirection link?')) {
        router.delete(route('admin.qr.destroy', id), {
            onSuccess: () => system.notify(cart.language === 'ar' ? 'تم إلغاء تفعيل المسار وحذفه' : 'Vector decommissioned', 'error'),
        });
    }
};

const updateLogo = (e) => {
    logoForm.logo = e.target.files[0];
    logoForm.post(route('admin.qr.logo.update'), {
        onSuccess: () => system.notify(cart.language === 'ar' ? 'تمت مزامنة الهوية البصرية الرسمية بنجاح' : 'Master Identity Synchronized'),
    });
};

const toggleActive = (link) => {
    router.put(route('admin.qr.update', link.id), {
        ...link,
        is_active: !link.is_active
    }, {
        preserveScroll: true,
        onSuccess: () => system.notify(cart.language === 'ar' ? 'تم تحديث حالة الرابط وبث الإشارة' : 'Signal visibility toggled')
    });
};

const handleIconUpload = (e) => {
    form.custom_icon = e.target.files[0];
};

</script>

<template>
    <Head :title="cart.language === 'ar' ? 'إدارة الهوية والروابط الرقمية' : 'Digital Ecosystem Commander'" />

    <AdminLayout>
        <template #header>{{ cart.language === 'ar' ? 'إدارة الهوية الرقمية والروابط المختصرة (QR)' : 'Digital Identity & Logic Vectors' }}</template>

        <div class="space-y-12 font-inter" :dir="cart.language === 'ar' ? 'rtl' : 'ltr'">
            <!-- Operational Metrics Overview -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div v-for="(val, labelAr, idx) in {
                    'إجمالي النقرات': stats.total_clicks,
                    'المسارات النشطة': stats.active_links,
                    'جاهزية البث': '99.9%',
                    'فجوة التوجيه': 'Zero-Lag'
                }" :key="idx" class="bg-white p-8 rounded-[24px] border border-gray-100 shadow-sm relative overflow-hidden group hover:shadow-2xl transition-all">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-gray-50 rounded-full -mr-12 -mt-12 group-hover:bg-gray-100 transition-colors"></div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 relative z-10">{{ cart.language === 'ar' ? labelAr : Object.keys(stats)[idx] }}</p>
                    <h4 class="text-3xl font-black text-gray-900 tracking-tighter mt-4 relative z-10">{{ val }}</h4>
                </div>
            </div>

            <!-- Master Identity Synchronizer -->
            <div class="bg-gray-900 p-10 rounded-[28px] border border-gray-800 shadow-2xl relative overflow-hidden group">
                <div class="absolute inset-0 bg-gradient-to-br from-brand-500/5 via-transparent to-emerald-500/5 opacity-50"></div>
                <div class="flex flex-col md:flex-row items-center gap-10 relative z-10">
                    <div class="relative">
                        <div class="w-28 h-28 bg-white rounded-[20px] flex items-center justify-center overflow-hidden border-4 border-gray-800 shadow-2xl group-hover:scale-105 transition-transform duration-500">
                            <img v-if="logo" :src="'/storage/' + logo" class="w-full h-full object-contain p-4">
                            <svg v-else class="w-12 h-12 text-gray-100" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        </div>
                        <label class="absolute -bottom-3 -right-3 w-10 h-10 bg-white text-gray-900 rounded-xl flex items-center justify-center cursor-pointer shadow-2xl hover:bg-gray-100 hover:rotate-90 transition-all border-4 border-gray-900">
                            <input type="file" class="hidden" @change="updateLogo">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                        </label>
                    </div>
                    
                    <div class="text-center md:text-right flex-1">
                        <h3 class="text-2xl font-black text-white tracking-tighter uppercase">{{ cart.language === 'ar' ? 'الهوية البصرية الرسمية' : 'Sovereign Brand Identity' }}</h3>
                        <p class="text-xs font-bold text-gray-400 mt-2 max-w-xl">{{ cart.language === 'ar' ? 'قم بتحميل الشعار الرئيسي للنظام ليظهر في صفحة الروابط المختصرة (QR) والتقارير العامة' : 'Upload the master system logo to synchronize visual presence across dynamic QR endpoints and public narratives' }}</p>
                        <div class="flex items-center gap-6 mt-6 justify-center md:justify-start">
                            <a href="/qr" target="_blank" class="text-[10px] font-black uppercase text-white bg-white/10 px-6 py-2 rounded-full hover:bg-white/20 transition-all tracking-[0.2em] shadow-lg border border-white/10">{{ cart.language === 'ar' ? 'معاينة العرض الحي' : 'Live Preview' }}</a>
                            <span class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span class="text-[9px] font-black uppercase text-emerald-500 tracking-widest">{{ cart.language === 'ar' ? 'متصل وحي' : 'Signal Active' }}</span>
                            </span>
                        </div>
                    </div>
                    
                    <button @click="openCreateModal" class="w-full md:w-auto bg-white text-gray-900 px-12 py-5 rounded-[16px] font-black uppercase tracking-[0.2em] text-xs shadow-2xl hover:bg-gray-100 transition-all active:scale-95 group/btn">
                        + {{ cart.language === 'ar' ? 'إضافة مسار توجيه جديد' : 'Initialize Vector' }}
                    </button>
                </div>
            </div>

            <!-- Vector Command Matrix -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10 pb-20">
                <div v-for="link in links" :key="link.id" class="relative group">
                    <div class="bg-white p-10 rounded-[28px] border border-gray-100 shadow-sm transition-all duration-500 hover:shadow-2xl hover:shadow-gray-900/5 group-hover:-translate-y-3 relative overflow-hidden flex flex-col">
                        <div class="absolute top-0 left-0 w-full h-1 bg-gray-900 opacity-5 group-hover:opacity-100 transition-opacity"></div>
                        
                        <div class="flex items-start justify-between mb-10">
                            <div class="w-16 h-16 rounded-[20px] flex items-center justify-center text-white shadow-2xl overflow-hidden transform group-hover:rotate-6 transition-all duration-500" :style="{ backgroundColor: link.color }">
                                <img v-if="link.custom_icon" :src="'/storage/' + link.custom_icon" class="w-full h-full object-cover">
                                <svg v-else-if="platforms.find(p => p.name === link.platform)" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="w-8 h-8">
                                    <path :d="platforms.find(p => p.name === link.platform).icon" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <span v-else class="text-3xl font-black uppercase">{{ link.title.charAt(0) }}</span>
                            </div>
                            
                            <div class="flex gap-2">
                                <button @click="openEditModal(link)" class="p-3 bg-gray-50 text-gray-400 hover:bg-gray-900 hover:text-white rounded-[12px] transition-all shadow-sm active:scale-95">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </button>
                                <button @click="deleteLink(link.id)" class="p-3 bg-gray-50 text-gray-400 hover:bg-red-600 hover:text-white rounded-[12px] transition-all shadow-sm active:scale-95">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <h5 class="text-xl font-black text-gray-900 tracking-tighter uppercase">{{ link.title }}</h5>
                            <p class="text-[10px] font-bold text-gray-400 truncate tracking-wide bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-100 max-w-fit" dir="ltr">{{ link.url }}</p>
                        </div>

                        <div class="mt-10 pt-8 border-t border-gray-50 flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="px-4 py-2 bg-gray-900 text-white rounded-xl shadow-lg shadow-gray-900/10">
                                    <span class="text-[10px] font-black uppercase tracking-widest">{{ link.clicks }} {{ cart.language === 'ar' ? 'نقرة' : 'Hits' }}</span>
                                </div>
                                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">{{ cart.language === 'ar' ? 'الأولوية:' : 'Rank:' }} {{ link.sort_order }}</span>
                            </div>
                            
                            <button 
                                @click="toggleActive(link)"
                                class="w-14 h-7 rounded-full p-1.5 transition-all duration-500 relative shadow-inner"
                                :class="link.is_active ? 'bg-emerald-500' : 'bg-gray-200'"
                            >
                                <div class="h-4 w-4 bg-white rounded-full shadow-lg transition-all duration-500" :class="link.is_active ? (cart.language === 'ar' ? '-translate-x-7' : 'translate-x-7') : 'translate-x-0'"></div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Structural Vector Configuration Modal -->
        <div v-if="showModal" class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-gray-900/60 backdrop-blur-md" :dir="cart.language === 'ar' ? 'rtl' : 'ltr'">
            <div class="bg-white w-full max-w-2xl rounded-[32px] shadow-2xl border border-gray-100 relative animate-in zoom-in-95 duration-500 overflow-hidden">
                <div class="h-2 bg-gray-900"></div>
                <div class="p-12">
                    <button @click="showModal = false" class="absolute top-10 left-10 p-3 text-gray-400 hover:text-gray-900 bg-gray-50 rounded-full hover:rotate-90 transition-all duration-300">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>

                    <h3 class="text-3xl font-black text-gray-900 tracking-tighter mb-2">{{ editingLink ? (cart.language === 'ar' ? 'إعادة تهيئة مسار الرابط' : 'Sync Link Core') : (cart.language === 'ar' ? 'حقن مسار توجيه جديد' : 'New Vector Injection') }}</h3>
                    <p class="text-[11px] font-black uppercase tracking-[0.2em] text-gray-400 mb-12">{{ cart.language === 'ar' ? 'قم بضبط بروتوكول التوجيه للأصول الرقمية في صفحة الروابط' : 'Configure strategic redirection protocols for digital assets across the ecosystem' }}</p>

                    <form @submit.prevent="submit" class="space-y-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-3">
                                <label class="text-[11px] font-black uppercase tracking-widest text-gray-500 px-1">{{ cart.language === 'ar' ? 'تصنيف المنصة / القناة' : 'Platform Logic' }}</label>
                                <select v-model="form.platform" class="w-full bg-gray-50 border-none rounded-[16px] py-5 px-8 text-sm font-black text-gray-900 shadow-inner focus:ring-2 focus:ring-gray-900 outline-none cursor-pointer appearance-none transition-all">
                                    <option value="">{{ cart.language === 'ar' ? 'قناة مخصصة (Custom)' : 'Custom Vector' }}</option>
                                    <option v-for="p in platforms" :key="p.name" :value="p.name">{{ p.name }}</option>
                                </select>
                                <InputError :message="form.errors.platform" />
                            </div>
                            <div class="space-y-3">
                                <label class="text-[11px] font-black uppercase tracking-widest text-gray-500 px-1">{{ cart.language === 'ar' ? 'المسمى المعروض للجمهور' : 'Display Nomenclature' }}</label>
                                <input v-model="form.title" type="text" class="w-full bg-gray-50 border-none rounded-[16px] py-5 px-8 text-sm font-black text-gray-900 shadow-inner focus:ring-2 focus:ring-gray-900 transition-all" :placeholder="cart.language === 'ar' ? 'واتساب / فيسبوك / أعمالي...' : 'e.g. WhatsApp / Direct Line / Portfolio'">
                                <InputError :message="form.errors.title" />
                            </div>
                        </div>

                        <div class="space-y-3">
                            <label class="text-[11px] font-black uppercase tracking-widest text-gray-500 px-1">{{ cart.language === 'ar' ? 'رابط التوجيه المستهدف (URL)' : 'Endpoint Transmission (URL)' }}</label>
                            <input v-model="form.url" type="text" class="w-full bg-gray-50 border-none rounded-[16px] py-5 px-8 text-sm font-black text-gray-900 shadow-inner focus:ring-2 focus:ring-gray-900 transition-all font-mono" placeholder="https://..." required dir="ltr">
                            <InputError :message="form.errors.url" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                            <div class="space-y-3">
                                <label class="text-[11px] font-black uppercase tracking-widest text-gray-500 px-1">{{ cart.language === 'ar' ? 'اللون الهوياتي' : 'Brand Aesthetics' }}</label>
                                <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-[16px] shadow-inner border border-gray-100 transition-all">
                                    <input v-model="form.color" type="color" class="w-10 h-10 bg-transparent border-none p-0 cursor-pointer rounded-lg overflow-hidden shrink-0">
                                    <span class="text-[10px] font-black font-mono text-gray-400 uppercase tracking-tighter">{{ form.color }}</span>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <label class="text-[11px] font-black uppercase tracking-widest text-gray-500 px-1">{{ cart.language === 'ar' ? 'ترتيب الأسبقية' : 'Rank' }}</label>
                                <input v-model="form.sort_order" type="number" class="w-full bg-gray-50 border-none rounded-[16px] py-5 px-8 text-sm font-black text-gray-900 shadow-inner focus:ring-2 focus:ring-gray-900 transition-all">
                            </div>
                            <div class="space-y-3">
                                <label class="text-[11px] font-black uppercase tracking-widest text-gray-500 px-1">{{ cart.language === 'ar' ? 'أيقونة مخصصة' : 'Vector Visual' }}</label>
                                <label class="relative w-full block">
                                    <div class="w-full bg-gray-50 rounded-[16px] py-5 px-8 text-[9px] font-black uppercase tracking-widest text-gray-400 text-center shadow-inner cursor-pointer hover:bg-gray-100 transition-all border border-dashed border-gray-200 group-hover:border-gray-900">
                                        {{ form.custom_icon ? (cart.language === 'ar' ? 'تم اختيار أيقونة' : 'Media Selected') : (cart.language === 'ar' ? 'رفع أيقونة' : 'Upload Icon') }}
                                    </div>
                                    <input type="file" @change="handleIconUpload" class="hidden">
                                </label>
                                <InputError :message="form.errors.custom_icon" />
                            </div>
                        </div>

                        <button type="submit" class="w-full py-6 bg-gray-900 text-white rounded-[20px] font-black uppercase tracking-[0.3em] text-xs shadow-2xl shadow-gray-900/40 hover:bg-emerald-600 transition-all active:scale-95 disabled:opacity-50" :disabled="form.processing">
                            {{ form.processing ? (cart.language === 'ar' ? 'جاري المزامنة...' : 'Syncing...') : (cart.language === 'ar' ? 'تثبيت وتفعيل المسار التوجيهي' : 'Commit Vector Transformation') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
select {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23374151' stroke-width='3'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7' /%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: left 1.5rem center;
    background-size: 1rem;
}
[dir="ltr"] select {
    background-position: right 1.5rem center;
}
</style>
