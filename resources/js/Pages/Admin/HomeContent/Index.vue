<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { system } from '@/Stores/SystemStore';
import { cart } from '@/Stores/CartStore';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';

const props = defineProps({
    content: Object
});

const form = useForm({
    content: props.content,
    files: {} // Store file objects with path as key
});

const previews = ref({});

const handleFile = (e, path) => {
    const file = e.target.files[0];
    if (file) {
        form.files[path] = file;
        previews.value[path] = URL.createObjectURL(file);
    }
};

const addSliderItem = () => {
    if (!form.content.slider) form.content.slider = [];
    form.content.slider.push({ image: null, title: '', subtitle: '' });
};

const removeSliderItem = (index) => {
    form.content.slider.splice(index, 1);
};

const addFeature = () => {
    if (!form.content.features) form.content.features = [];
    form.content.features.push({ title: '', text: '', icon: 'cpu' });
};

const removeFeature = (index) => {
    form.content.features.splice(index, 1);
};

const submit = () => {
    form.post(route('admin.home-content.update'), {
        forceFormData: true,
        onSuccess: () => {
            system.notify(cart.language === 'ar' ? 'تم تحديث محتوى الصفحة الرئيسية بنجاح' : 'Homepage updated successfully');
            previews.value = {};
            form.files = {};
        }
    });
};
</script>

<template>
    <Head :title="cart.language === 'ar' ? 'إدارة محتوى الواجهة الرئيسية' : 'Homepage Matrix Control'" />

    <AdminLayout>
        <template #header>{{ cart.language === 'ar' ? 'إدارة المحتوى البصري والترويجي للواجهة' : 'Homepage Content Strategy' }}</template>

        <div class="space-y-12 pb-32 font-inter" :dir="cart.language === 'ar' ? 'rtl' : 'ltr'">
            <form @submit.prevent="submit" class="space-y-12">
                
                <!-- Hero Section: Strategic Opening -->
                <div class="bg-white p-10 rounded-[28px] border border-gray-100 shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-gray-50 rounded-full -mr-16 -mt-16 opacity-50"></div>
                    <h3 class="text-2xl font-black text-gray-900 tracking-tighter mb-10 flex items-center gap-5 relative z-10">
                        <span class="w-2 h-8 bg-gray-900 rounded-full"></span>
                        {{ cart.language === 'ar' ? 'قسم الواجهة الرئيسي (Hero)' : 'Strategic Hero Matrix' }}
                    </h3>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 relative z-10">
                        <div class="space-y-8">
                            <div class="space-y-2">
                                <InputLabel :value="cart.language === 'ar' ? 'العنوان الرئيسي للواجهة' : 'Hero Command Title'" class="text-[11px] font-black uppercase tracking-widest text-gray-400 px-1" />
                                <TextInput v-model="form.content.hero.title" class="w-full bg-gray-50 border-none rounded-[16px] px-8 py-5 text-sm font-black text-gray-900 shadow-inner focus:ring-2 focus:ring-gray-900 transition-all" :placeholder="cart.language === 'ar' ? 'مثال: جيل جديد من الطباعة الذكية' : 'e.g. Next-Gen Printing Ecosystem'" />
                            </div>
                            <div class="space-y-2">
                                <InputLabel :value="cart.language === 'ar' ? 'النص الوصفي المساعد' : 'Subordinate Narrative'" class="text-[11px] font-black uppercase tracking-widest text-gray-400 px-1" />
                                <textarea v-model="form.content.hero.subtitle" class="w-full bg-gray-50 border-none rounded-[16px] p-8 text-sm font-black text-gray-900 shadow-inner focus:ring-2 focus:ring-gray-900 h-40 resize-none transition-all" :placeholder="cart.language === 'ar' ? 'أدخل سرداً يوضح القيمة المضافة للمؤسسة...' : 'Describe the value proposition logic...'"></textarea>
                            </div>
                            <div class="space-y-2">
                                <InputLabel :value="cart.language === 'ar' ? 'نص زر الإجراء (CTA)' : 'Call to Action Label'" class="text-[11px] font-black uppercase tracking-widest text-gray-400 px-1" />
                                <TextInput v-model="form.content.hero.button_text" class="w-full bg-gray-50 border-none rounded-[16px] px-8 py-5 text-sm font-black text-gray-900 shadow-inner focus:ring-2 focus:ring-gray-900 transition-all" :placeholder="cart.language === 'ar' ? 'مثال: اكتشف المزيد' : 'e.g. Explore Ecosystem'" />
                            </div>
                        </div>
                        <div class="space-y-4">
                            <InputLabel :value="cart.language === 'ar' ? 'الأصول البصرية للواجهة' : 'Primary Visual Asset'" class="text-[11px] font-black uppercase tracking-widest text-gray-400 px-1" />
                            <div class="relative aspect-[16/10] bg-gray-50 rounded-[24px] overflow-hidden border-2 border-dashed border-gray-200 group hover:border-gray-900 transition-all shadow-inner">
                                <img v-if="previews['hero.image'] || form.content.hero.image" :src="previews['hero.image'] || form.content.hero.image" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                                <div v-else class="w-full h-full flex flex-col items-center justify-center text-gray-300">
                                    <svg class="w-14 h-14 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    <span class="text-[10px] font-black uppercase tracking-[0.3em]">{{ cart.language === 'ar' ? 'اختر صورة الواجهة الرئيسية' : 'Inject Master Visualization' }}</span>
                                </div>
                                <input type="file" @change="handleFile($event, 'hero.image')" class="absolute inset-0 opacity-0 cursor-pointer z-10">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slider Section: Visual Momentum -->
                <div class="bg-white p-10 rounded-[28px] border border-gray-100 shadow-sm relative overflow-hidden">
                    <div class="flex items-center justify-between mb-10">
                        <h3 class="text-2xl font-black text-gray-900 tracking-tighter flex items-center gap-5">
                            <span class="w-2 h-8 bg-gray-900 rounded-full"></span>
                            {{ cart.language === 'ar' ? 'مصفوفة سلايدر الصور المتنقلة' : 'Dynamic Visual Flux' }}
                        </h3>
                        <button type="button" @click="addSliderItem" class="bg-gray-50 text-gray-900 px-8 py-3 rounded-[12px] text-[10px] font-black uppercase tracking-widest border border-gray-100 hover:bg-gray-900 hover:text-white transition-all shadow-sm active:scale-95">+ {{ cart.language === 'ar' ? 'إضافة شريحة' : 'Append Slide' }}</button>
                    </div>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <div v-for="(item, index) in form.content.slider" :key="index" class="p-8 bg-gray-50/50 rounded-[24px] relative group border border-gray-100 shadow-inner flex gap-8 animate-in slide-in-from-bottom-4 duration-500">
                            <button type="button" @click="removeSliderItem(index)" class="absolute top-6 left-6 p-2 bg-white text-red-500 rounded-lg shadow-md hover:bg-red-500 hover:text-white transition-all z-20 active:scale-90 border border-gray-100">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                            <div class="w-1/3 aspect-square bg-white rounded-[16px] overflow-hidden relative shadow-sm border border-gray-100 group-hover:shadow-lg transition-all">
                                <img v-if="previews[`slider.${index}.image`] || item.image" :src="previews[`slider.${index}.image`] || item.image" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                                <div v-else class="w-full h-full flex items-center justify-center text-gray-200">
                                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                                </div>
                                <input type="file" @change="handleFile($event, `slider.${index}.image`)" class="absolute inset-0 opacity-0 cursor-pointer">
                            </div>
                            <div class="flex-1 space-y-4">
                                <TextInput v-model="item.title" :placeholder="cart.language === 'ar' ? 'عنوان الشريحة' : 'Slide Sector Title'" class="w-full bg-white border-none rounded-[12px] px-6 py-4 text-xs font-black shadow-inner focus:ring-2 focus:ring-gray-900" />
                                <textarea v-model="item.subtitle" :placeholder="cart.language === 'ar' ? 'وصف الشريحة القصير...' : 'Node brief narrative...'" class="w-full bg-white border-none rounded-[12px] p-6 text-[11px] font-black text-gray-500 shadow-inner focus:ring-2 focus:ring-gray-900 h-28 resize-none"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Premium Promo Section: Brand Authority -->
                <div class="bg-white p-10 rounded-[28px] border border-gray-100 shadow-sm relative overflow-hidden">
                    <h3 class="text-2xl font-black text-gray-900 tracking-tighter mb-10 flex items-center gap-5">
                        <span class="w-2 h-8 bg-gray-900 rounded-full"></span>
                        {{ cart.language === 'ar' ? 'القسم الترويجي المميز' : 'High-Impact Promotional Plane' }}
                    </h3>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                        <div class="space-y-6">
                            <InputLabel :value="cart.language === 'ar' ? 'صورة ترويجية (جانبية)' : 'Sector Branding Visual'" class="text-[11px] font-black uppercase tracking-widest text-gray-400 px-1" />
                            <div class="relative aspect-[16/9] bg-gray-50 rounded-[20px] overflow-hidden border-2 border-dashed border-gray-200 group hover:border-gray-900 transition-all shadow-inner">
                                <img v-if="previews['custom_section.image'] || form.content.custom_section.image" :src="previews['custom_section.image'] || form.content.custom_section.image" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                                <div v-else class="w-full h-full flex flex-col items-center justify-center text-gray-300">
                                    <svg class="w-12 h-12 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                                    <span class="text-[10px] font-black uppercase tracking-[0.3em]">{{ cart.language === 'ar' ? 'رفع صورة ترويجية' : 'Inject Promo Vector' }}</span>
                                </div>
                                <input type="file" @change="handleFile($event, 'custom_section.image')" class="absolute inset-0 opacity-0 cursor-pointer z-10">
                            </div>
                            <div class="pt-4">
                                <InputLabel :value="cart.language === 'ar' ? 'رابط التوجيه عند النقر (URL)' : 'Endpoint Transmission Protocol'" class="text-[11px] font-black uppercase tracking-widest text-gray-400 px-1" />
                                <TextInput v-model="form.content.custom_section.link" placeholder="https://..." class="w-full bg-gray-50 border-none rounded-[16px] px-8 py-5 text-sm font-black text-gray-900 shadow-inner focus:ring-2 focus:ring-gray-900 transition-all mt-4" />
                            </div>
                        </div>
                        <div class="space-y-8 flex flex-col justify-center">
                            <div class="space-y-2">
                                <InputLabel :value="cart.language === 'ar' ? 'عنوان القسم الترويجي' : 'Promotional Title Vector'" class="text-[11px] font-black uppercase tracking-widest text-gray-400 px-1" />
                                <TextInput v-model="form.content.custom_section.title" class="w-full bg-gray-50 border-none rounded-[16px] px-8 py-5 text-sm font-black text-gray-900 shadow-inner focus:ring-2 focus:ring-gray-900 transition-all" />
                            </div>
                            <div class="space-y-2">
                                <InputLabel :value="cart.language === 'ar' ? 'النص الوصفي للقسم' : 'Narrative Breakdown'" class="text-[11px] font-black uppercase tracking-widest text-gray-400 px-1" />
                                <textarea v-model="form.content.custom_section.text" class="w-full bg-gray-50 border-none rounded-[16px] p-8 text-sm font-black text-gray-900 shadow-inner focus:ring-2 focus:ring-gray-900 h-44 resize-none transition-all"></textarea>
                            </div>
                            <div class="space-y-2">
                                <InputLabel :value="cart.language === 'ar' ? 'نص زر التوجيه' : 'Action Command'" class="text-[11px] font-black uppercase tracking-widest text-gray-400 px-1" />
                                <TextInput v-model="form.content.custom_section.button_text" class="w-full bg-gray-50 border-none rounded-[16px] px-8 py-5 text-sm font-black text-gray-900 shadow-inner focus:ring-2 focus:ring-gray-900 transition-all" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Features Section: Structural Advantages -->
                <div class="bg-white p-10 rounded-[28px] border border-gray-100 shadow-sm relative overflow-hidden">
                    <div class="flex items-center justify-between mb-10">
                        <h3 class="text-2xl font-black text-gray-900 tracking-tighter flex items-center gap-5">
                            <span class="w-2 h-8 bg-gray-900 rounded-full"></span>
                            {{ cart.language === 'ar' ? 'لماذا تختارنا؟ (المميزات البنيوية)' : 'Strategic Advantage Matrix' }}
                        </h3>
                        <button type="button" @click="addFeature" class="bg-gray-50 text-gray-900 px-8 py-3 rounded-[12px] text-[10px] font-black uppercase tracking-widest border border-gray-100 hover:bg-gray-900 hover:text-white transition-all shadow-sm active:scale-95">+ {{ cart.language === 'ar' ? 'إضافة ميزة' : 'Add Advantage' }}</button>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div v-for="(item, index) in form.content.features" :key="index" class="p-8 bg-gray-50/50 rounded-[24px] space-y-6 border border-gray-100 relative group transition-all hover:bg-white hover:shadow-2xl hover:shadow-gray-900/5 animate-in zoom-in-95 duration-500">
                            <button type="button" @click="removeFeature(index)" class="absolute top-6 left-6 p-2 bg-white text-red-500 rounded-lg shadow-md hover:bg-red-500 hover:text-white transition-all z-20 active:scale-90 border border-gray-100 opacity-0 group-hover:opacity-100">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                            <div class="space-y-3">
                                <InputLabel :value="cart.language === 'ar' ? 'رمز الأيقونة (Pico/Lucide)' : 'Icon Logic Key'" class="text-[10px] font-black uppercase text-gray-400 px-1" />
                                <TextInput v-model="item.icon" class="w-full bg-white border-none rounded-[12px] px-6 py-4 text-[11px] font-black text-gray-900 shadow-inner focus:ring-2 focus:ring-gray-900" />
                            </div>
                            <div class="space-y-3">
                                <InputLabel :value="cart.language === 'ar' ? 'عنوان الميزة' : 'Subject Heading'" class="text-[10px] font-black uppercase text-gray-400 px-1" />
                                <TextInput v-model="item.title" class="w-full bg-white border-none rounded-[12px] px-6 py-4 text-xs font-black text-gray-900 shadow-inner focus:ring-2 focus:ring-gray-900" />
                            </div>
                            <div class="space-y-3">
                                <InputLabel :value="cart.language === 'ar' ? 'التفاصيل والوصف' : 'Narrative Core'" class="text-[10px] font-black uppercase text-gray-400 px-1" />
                                <textarea v-model="item.text" class="w-full bg-white border-none rounded-[12px] p-6 text-[11px] font-black text-gray-500 shadow-inner focus:ring-2 focus:ring-gray-900 h-32 resize-none"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="fixed bottom-12 left-1/2 -translate-x-1/2 z-50">
                    <button type="submit" class="bg-gray-900 text-white px-20 py-6 rounded-[20px] font-black uppercase tracking-[0.4em] text-xs shadow-2xl shadow-gray-900/40 hover:bg-emerald-600 transition-all active:scale-95 disabled:opacity-50" :disabled="form.processing">
                        {{ form.processing ? (cart.language === 'ar' ? 'جاري مزامنة المحتوى الاستراتيجي...' : 'Synchronizing States...') : (cart.language === 'ar' ? 'تثبيت وحفظ تعديلات الواجهة' : 'COMMIT HOMEPAGE MATRIX') }}
                    </button>
                </div>

            </form>
        </div>
    </AdminLayout>
</template>
