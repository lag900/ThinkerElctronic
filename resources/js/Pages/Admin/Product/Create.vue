<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { system } from '@/Stores/SystemStore';
import { cart } from '@/Stores/CartStore';

const props = defineProps({
    categories: Array
});

const form = useForm({
    name: '',
    name_ar: '',
    sku: '',
    description_en: '',
    description_ar: '',
    price: '',
    cost_price: '',
    purchase_price: '',
    stock_quantity: '',
    min_stock_alert: 5,
    category_id: '',
    sale_price: null,
    sale_start_date: null,
    sale_end_date: null,
    youtube_url: '',
    video_provider: 'youtube', // default
    video_url: '',
    video_file: null,
    main_image: null,
    gallery_images: [], // File objects
    specs: [],
    codes: []
});

const submit = () => {
    form.post(route('admin.products.store'), {
        forceFormData: true,
        onSuccess: () => {
            system.notify(cart.language === 'ar' ? 'تم تسجيل المنتج بنجاح في قاعدة البيانات' : 'Technical Asset initialized successfully');
        },
        onError: (errors) => {
            console.error(errors);
            system.notify(cart.language === 'ar' ? 'فشل الحفظ: يرجى مراجعة كافة الحقول المطلوبة' : 'Protocol Error: Please check all required matrices', 'error');
        }
    });
};

const addSpec = () => {
    form.specs.push({ label: '', value: '' });
};

const removeSpec = (index) => {
    form.specs.splice(index, 1);
};

const addCode = () => {
    form.codes.push({ title: '', language: 'javascript', code: '' });
};

const removeCode = (index) => {
    form.codes.splice(index, 1);
};

const handleGalleryUpload = (e) => {
    form.gallery_images = Array.from(e.target.files);
};
</script>

<template>
    <Head :title="cart.language === 'ar' ? 'إضافة منتج جديد' : 'Deploy New Item'" />

    <AdminLayout>
        <template #header>{{ cart.language === 'ar' ? 'إدارة كتالوج المنتجات - إضافة صنف جديد' : 'Product Catalog - Deploy New Item' }}</template>

        <div class="py-10 font-inter" :dir="cart.language === 'ar' ? 'rtl' : 'ltr'">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <form @submit.prevent="submit" class="space-y-12 bg-white p-12 rounded-[32px] border border-[#ececf2] shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-80 h-80 bg-brand-50 rounded-full -mr-40 -mt-40 opacity-50"></div>
                    
                    <!-- Basic Info -->
                    <div class="space-y-8 relative z-10">
                        <div class="pb-2 border-b border-[#f8f9fb]">
                            <h4 class="text-xl font-black text-[#1f1f1f] tracking-tighter uppercase">{{ cart.language === 'ar' ? 'البيانات العامة والمخزون' : 'Core Identification & Inventory' }}</h4>
                            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mt-1">{{ cart.language === 'ar' ? 'إدخال تفاصيل الصنف الجديد وحالة توفره' : 'Define essential product attributes and initial stock levels' }}</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-1.5" :dir="cart.language === 'ar' ? 'ltr' : 'ltr'">
                                <InputLabel :value="cart.language === 'ar' ? 'اسم المنتج (الإنجليزية)' : 'Product Name (English)'" class="text-[10px] font-black uppercase tracking-widest text-gray-400 pl-1" />
                                <input v-model="form.name" type="text" class="input-premium" required />
                                <div v-if="form.errors.name" class="text-red-500 text-[10px] mt-1 font-black">{{ form.errors.name }}</div>
                            </div>
                            <div class="space-y-1.5" :dir="cart.language === 'ar' ? 'rtl' : 'ltr'">
                                <InputLabel :value="cart.language === 'ar' ? 'اسم المنتج (العربية)' : 'Product Name (Arabic)'" class="text-[10px] font-black uppercase tracking-widest text-gray-400 px-1" />
                                <input v-model="form.name_ar" type="text" class="input-premium" />
                            </div>

                            <div class="space-y-1.5">
                                <InputLabel :value="cart.language === 'ar' ? 'سعر البيع المعتاد (ج.م)' : 'Regular Retail Price (EGP)'" class="text-[10px] font-black uppercase tracking-widest text-gray-400 px-1" />
                                <input v-model="form.price" type="number" step="0.01" class="input-premium !font-black !text-emerald-600" required />
                                <div v-if="form.errors.price" class="text-red-500 text-[10px] mt-1 font-black">{{ form.errors.price }}</div>
                            </div>
                            <div class="space-y-1.5">
                                <InputLabel :value="cart.language === 'ar' ? 'سعر العرض/الخصم (اختياري)' : 'Strategic Sale Price (Optional)'" class="text-[10px] font-black uppercase tracking-widest text-[#ff2e63] px-1" />
                                <input v-model="form.sale_price" type="number" step="0.01" class="input-premium !bg-red-50/30 !border-red-100 !text-[#ff2e63] !font-black" />
                            </div>

                            <div class="space-y-1.5">
                                <InputLabel :value="cart.language === 'ar' ? 'بداية العرض' : 'Sale Start Date'" class="text-[10px] font-black uppercase tracking-widest text-gray-400 px-1" />
                                <input v-model="form.sale_start_date" type="datetime-local" class="input-premium" />
                            </div>
                            <div class="space-y-1.5">
                                <InputLabel :value="cart.language === 'ar' ? 'نهاية العرض' : 'Sale End Date'" class="text-[10px] font-black uppercase tracking-widest text-gray-400 px-1" />
                                <input v-model="form.sale_end_date" type="datetime-local" class="input-premium" />
                            </div>

                            <div class="space-y-1.5">
                                <InputLabel :value="cart.language === 'ar' ? 'الكمية الحالية بالمخزون' : 'Current Physical Inventory'" class="text-[10px] font-black uppercase tracking-widest text-gray-400 px-1" />
                                <input v-model="form.stock_quantity" type="number" class="input-premium !bg-white !shadow-none !border-[#ececf2] !border" />
                            </div>
                            <div class="space-y-1.5">
                                <InputLabel :value="cart.language === 'ar' ? 'مستوى التنبيه بنقص المخزون' : 'Low Stock Trigger Level'" class="text-[10px] font-black uppercase tracking-widest text-gray-400 px-1" />
                                <input v-model="form.min_stock_alert" type="number" class="input-premium !bg-red-50/20 !text-red-600 !font-black" />
                            </div>

                            <div class="space-y-1.5">
                                <InputLabel :value="cart.language === 'ar' ? 'سعر التكلفة (ج.م)' : 'Historical Acquisition Cost (EGP)'" class="text-[10px] font-black uppercase tracking-widest text-gray-400 px-1" />
                                <input v-model="form.purchase_price" type="number" step="0.01" class="input-premium !bg-gray-50 !text-gray-500" />
                            </div>
                            <div class="space-y-1.5">
                                <InputLabel :value="cart.language === 'ar' ? 'رمز الصنف (SKU)' : 'SKU / Global Reference'" class="text-[10px] font-black uppercase tracking-widest text-gray-400 px-1" />
                                <input v-model="form.sku" type="text" placeholder="THK-..." class="input-premium" />
                            </div>

                            <div class="space-y-1.5 md:col-span-2">
                                <InputLabel :value="cart.language === 'ar' ? 'تصنيف المنتج التابع له' : 'Parent Category Node'" class="text-[10px] font-black uppercase tracking-widest text-gray-400 px-1" />
                                <select v-model="form.category_id" class="input-premium appearance-none cursor-pointer" required>
                                    <option value="" disabled>{{ cart.language === 'ar' ? 'اختر التصنيف...' : 'Select Category Node' }}</option>
                                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Video Controls -->
                    <div class="space-y-8 bg-[#f8f9fb] p-10 rounded-[24px] border border-[#ececf2]">
                        <div class="pb-2 border-b border-[#ececf2]/50">
                            <h4 class="text-lg font-black text-[#1f1f1f] tracking-tighter uppercase">{{ cart.language === 'ar' ? 'إعدادات الوسائط والفيديو' : 'Media Flux Configuration' }}</h4>
                            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mt-1">{{ cart.language === 'ar' ? 'إدارة الفيديوهات التعريفية والروابط الخارجية' : 'Manage rich media streams and documentation video providers' }}</p>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-1.5">
                                <InputLabel :value="cart.language === 'ar' ? 'مزود خدمة الفيديو' : 'Video Logic Provider'" class="text-[10px] font-black uppercase text-gray-400 px-1" />
                                <select v-model="form.video_provider" class="input-premium appearance-none cursor-pointer">
                                    <option value="youtube">YouTube (Global Embed)</option>
                                    <option value="vimeo">Vimeo</option>
                                    <option value="local">Direct Node Upload (MP4)</option>
                                </select>
                            </div>
                            <div v-if="form.video_provider === 'youtube' || form.video_provider === 'vimeo'" class="space-y-1.5">
                                <InputLabel :value="cart.language === 'ar' ? 'رابط الفيديو المباشر' : 'Stream Endpoint URL'" class="text-[10px] font-black uppercase text-gray-400 px-1" />
                                <input v-model="form.video_url" type="url" placeholder="https://..." class="input-premium" />
                            </div>
                            <div v-if="form.video_provider === 'local'" class="space-y-1.5">
                                <InputLabel :value="cart.language === 'ar' ? 'رفع ملف الفيديو' : 'Binary Asset Upload'" class="text-[10px] font-black uppercase text-gray-400 px-1" />
                                <input type="file" @input="form.video_file = $event.target.files[0]" class="block w-full text-xs text-gray-400 file:mr-4 file:py-3 file:px-6 file:rounded-[10px] file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-brand-500 file:text-white hover:file:bg-brand-600 transition-all shadow-sm" accept="video/mp4,video/webm,video/quicktime" />
                            </div>
                        </div>
                    </div>

                    <!-- Tech Descriptions -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-8 border-t border-[#f8f9fb]">
                        <div class="space-y-1.5" :dir="cart.language === 'ar' ? 'ltr' : 'ltr'">
                            <InputLabel :value="cart.language === 'ar' ? 'وصف المنتج (بالإنجليزية)' : 'Technical Abstract (English)'" class="text-[10px] font-black uppercase tracking-widest text-gray-400 pl-1" />
                            <textarea v-model="form.description_en" rows="6" class="input-premium !py-6 !px-8 leading-relaxed" :placeholder="cart.language === 'ar' ? 'أدخل الوصف بالإنجليزية...' : 'Detailed technical documentation in English...'"></textarea>
                        </div>
                        <div class="space-y-1.5" :dir="cart.language === 'ar' ? 'rtl' : 'ltr'">
                            <InputLabel :value="cart.language === 'ar' ? 'وصف المنتج (بالعربية)' : 'Technical Abstract (Arabic)'" class="text-[10px] font-black uppercase tracking-widest text-gray-400 px-1" />
                            <textarea v-model="form.description_ar" rows="6" class="input-premium !py-6 !px-8 leading-relaxed placeholder:text-right" dir="rtl" :placeholder="cart.language === 'ar' ? 'أدخل الوصف التفصيلي بالعربية...' : 'Detailed technical documentation in Arabic...'"></textarea>
                        </div>
                    </div>

                    <!-- Visual Assets -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10 p-12 bg-white rounded-[32px] border-2 border-[#f8f9fb] shadow-sm relative overflow-hidden group">
                        <div class="absolute top-0 left-0 w-32 h-32 bg-brand-50/50 rounded-br-full -ml-8 -mt-8"></div>
                        <div class="space-y-6 relative z-10">
                            <InputLabel :value="cart.language === 'ar' ? 'الصورة الرئيسية للمنتج' : 'Primary Hardware Component Image'" class="text-[10px] font-black uppercase text-gray-400 px-1" />
                            <div class="bg-[#f8f9fb] p-6 rounded-[20px] border border-[#ececf2]">
                                <input type="file" @input="form.main_image = $event.target.files[0]" class="block w-full text-xs text-gray-400 file:mr-4 file:py-3 file:px-8 file:rounded-[12px] file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-brand-500 file:text-white hover:file:bg-brand-600 transition-all shadow-md" required accept="image/*" />
                                <div v-if="form.errors.main_image" class="text-red-500 text-[10px] font-black mt-2 px-1">{{ form.errors.main_image }}</div>
                            </div>
                        </div>
                        <div class="space-y-6 relative z-10">
                            <InputLabel :value="cart.language === 'ar' ? 'معرض الصور الإضافية (اختياري)' : 'Sub-Assembly Gallery (Optional)'" class="text-[10px] font-black uppercase text-gray-400 px-1" />
                            <div class="bg-gray-50/50 p-6 rounded-[20px] border border-[#ececf2] border-dashed">
                                <input type="file" multiple @input="handleGalleryUpload" class="block w-full text-xs text-gray-400 file:mr-4 file:py-3 file:px-8 file:rounded-[12px] file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-gray-200 file:text-gray-900 hover:file:bg-gray-300 transition-all shadow-sm" accept="image/*" />
                            </div>
                        </div>
                    </div>

                    <!-- Tech Specs -->
                    <div class="space-y-8 pt-8 border-t border-[#f8f9fb]">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-xl font-black text-[#1f1f1f] tracking-tighter uppercase">{{ cart.language === 'ar' ? 'المواصفات والبيانات الفنية' : 'Hardware Performance Metrics' }}</h3>
                                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mt-1">{{ cart.language === 'ar' ? 'إدخال الخصائص والأحجام ومعايير الأداء' : 'Define key performance thresholds and physical dimensions' }}</p>
                            </div>
                            <button @click="addSpec" type="button" class="px-6 py-3 bg-brand-50 text-brand-500 rounded-[12px] text-[10px] font-black uppercase tracking-widest hover:bg-brand-500 hover:text-white transition-all shadow-sm">
                                + {{ cart.language === 'ar' ? 'إضافة مواصفة' : 'Initialize Metric' }}
                            </button>
                        </div>
                        <div class="space-y-4">
                            <div v-for="(spec, index) in form.specs" :key="index" class="flex gap-6 items-center bg-[#f8f9fb] p-4 rounded-[20px] border border-[#ececf2] group animate-in slide-in-from-left-2 duration-300">
                                <input v-model="spec.label" :placeholder="cart.language === 'ar' ? 'العنوان (مثال: الجهد العالي)' : 'Metric Label'" class="input-premium !bg-white !shadow-none flex-1" />
                                <input v-model="spec.value" :placeholder="cart.language === 'ar' ? 'القيمة (مثال: 220 فولت)' : 'Specific Value'" class="input-premium !bg-white !shadow-none flex-1" />
                                <button @click="removeSpec(index)" type="button" class="p-3 text-gray-300 hover:text-[#ff2e63] hover:bg-red-50 rounded-[12px] transition-all">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Developer Logic blocks -->
                    <div class="space-y-8 pt-12 border-t border-[#f8f9fb]">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-xl font-black text-[#1f1f1f] tracking-tighter uppercase">{{ cart.language === 'ar' ? 'وثائق المطورين والأكواد' : 'Logic Interface Protocols' }}</h3>
                                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mt-1">{{ cart.language === 'ar' ? 'إضافة النماذج البرمجية المرفقة مع المنتج' : 'Embed functional logic snippets and integration protocols' }}</p>
                            </div>
                            <button @click="addCode" type="button" class="btn-red-gradient">
                                + {{ cart.language === 'ar' ? 'حقن قالب برمي' : 'Inject Logic Block' }}
                            </button>
                        </div>
                        <div class="space-y-8">
                            <div v-for="(block, index) in form.codes" :key="index" class="space-y-6 p-10 bg-[#f8f9fb] rounded-[32px] border border-[#ececf2] relative animate-in zoom-in-95 duration-500">
                                <button @click="removeCode(index)" type="button" class="absolute top-8 right-8 text-gray-400 hover:text-[#ff2e63] transition-all">
                                    <svg class="w-8 h-8 p-1.5 border border-[#ececf2] rounded-full" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                                <div class="flex flex-col md:flex-row gap-6 relative z-10">
                                    <input v-model="block.title" :placeholder="cart.language === 'ar' ? 'عنوان النموذج (مثال: بروتوكول التشغيل)' : 'Logic Descriptor Title'" class="input-premium !bg-white !shadow-none flex-1" />
                                    <select v-model="block.language" class="input-premium !bg-white !shadow-none w-full md:w-64 appearance-none cursor-pointer">
                                        <option value="javascript">JavaScript</option>
                                        <option value="python">Python</option>
                                        <option value="cpp">C++ (Arduino)</option>
                                        <option value="html">HTML5</option>
                                    </select>
                                </div>
                                <textarea v-model="block.code" rows="8" :placeholder="cart.language === 'ar' ? '// الصق الكود البرمجي المطور هنا...' : '// Paste specialized functional logic here...'" class="w-full bg-[#1f1f1f] text-emerald-400 font-mono text-sm rounded-[24px] border-none px-8 py-8 focus:ring-2 focus:ring-[#ff2e63]/30 shadow-2xl leading-relaxed" dir="ltr" spellcheck="false"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row items-center justify-end gap-10 pt-12 border-t border-[#f8f9fb] relative z-10">
                        <Link :href="route('admin.products.index')" class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 hover:text-[#1f1f1f] transition-colors">
                            {{ cart.language === 'ar' ? 'إلغاء العملية' : 'Abort Session' }}
                        </Link>
                        <button 
                            type="submit" 
                            :disabled="form.processing"
                            class="w-full md:w-auto btn-red-gradient !px-20 !py-5 text-[11px]"
                        >
                            {{ form.processing ? (cart.language === 'ar' ? 'جاري الحفظ...' : 'Persisting...') : (cart.language === 'ar' ? 'تأكيد تسجيل المنتج' : 'Commit Parameters') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
