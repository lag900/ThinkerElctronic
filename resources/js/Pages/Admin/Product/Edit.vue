<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { system } from '@/Stores/SystemStore';
import { cart } from '@/Stores/CartStore';

const props = defineProps({
    product: Object,
    categories: Array
});

const form = useForm({
    _method: 'PUT',
    name: props.product.name,
    name_ar: props.product.name_ar,
    description_en: props.product.description_en || props.product.description,
    description_ar: props.product.description_ar,
    price: props.product.price,
    cost_price: props.product.cost_price,
    purchase_price: props.product.purchase_price,
    stock_quantity: props.product.stock_quantity,
    min_stock_alert: props.product.min_stock_alert,
    category_id: props.product.category_id,
    sale_price: props.product.sale_price,
    sale_start_date: props.product.sale_start_date ? props.product.sale_start_date.split('.')[0] : null,
    sale_end_date: props.product.sale_end_date ? props.product.sale_end_date.split('.')[0] : null,
    youtube_url: props.product.youtube_url,
    video_provider: props.product.video_provider || 'youtube',
    video_url: props.product.video_url || props.product.youtube_url,
    video_file: null,
    main_image: null,
    gallery_images: [],
    specs: props.product.specs || [],
    codes: props.product.codes || []
});

const submit = () => {
    form.post(route('admin.products.update', props.product.id), {
        forceFormData: true,
        onSuccess: () => {
            system.notify(cart.language === 'ar' ? 'تم تحديث معايير المنتج بنجاح' : 'Asset parameters updated successfully');
        },
        onError: (errors) => {
            console.error(errors);
            system.notify(cart.language === 'ar' ? 'فشل التحديث: يرجى مراجعة مصفوفة البيانات' : 'Update Protocol Error: Please check required matrices', 'error');
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

const deleteImage = (id) => {
    if (confirm(cart.language === 'ar' ? 'هل أنت متأكد من حذف هذه الصورة من ألبوم المنتج؟' : 'Are you sure you want to delete this image?')) {
        Link.visit(route('admin.products.image.delete', id), { 
            method: 'delete',
            preserveScroll: true
        });
    }
};
</script>

<template>
    <Head :title="cart.language === 'ar' ? 'تعديل المنتج' : 'Asset Parameters Editor'" />

    <AdminLayout>
        <template #header>{{ cart.language === 'ar' ? 'تعديل بيانات المنتج' : 'Hardware Asset Configuration' }}: {{ product.name }}</template>

        <div class="py-10 font-inter" :dir="cart.language === 'ar' ? 'rtl' : 'ltr'">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <form @submit.prevent="submit" class="space-y-12 bg-white p-12 rounded-[32px] border border-[#ececf2] shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-80 h-80 bg-brand-50 rounded-full -mr-40 -mt-40 opacity-50"></div>
                    
                    <!-- Basic Info -->
                    <div class="space-y-8 relative z-10">
                        <div class="pb-2 border-b border-[#f8f9fb]">
                            <h4 class="text-xl font-black text-[#1f1f1f] tracking-tighter uppercase">{{ cart.language === 'ar' ? 'البيانات العامة والمخزون' : 'Core Identification & Inventory' }}</h4>
                            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mt-1">{{ cart.language === 'ar' ? 'تعديل تفاصيل الصنف وحالة توفره في المستودعات' : 'Manage essential product attributes and real-time stock levels' }}</p>
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
                              <InputLabel :value="cart.language === 'ar' ? 'مزود الفيديو' : 'Video Provider Interface'" class="text-[10px] font-black uppercase text-gray-400 px-1" />
                              <select v-model="form.video_provider" class="input-premium appearance-none cursor-pointer">
                                  <option value="youtube">YouTube</option>
                                  <option value="vimeo">Vimeo</option>
                                  <option value="local">Direct Node Upload (MP4)</option>
                              </select>
                          </div>
                          <div v-if="form.video_provider === 'youtube' || form.video_provider === 'vimeo'" class="space-y-1.5">
                              <InputLabel :value="cart.language === 'ar' ? 'رابط الفيديو (URL)' : 'Endpoint Link (URL)'" class="text-[10px] font-black uppercase text-gray-400 px-1" />
                              <input v-model="form.video_url" type="url" placeholder="https://..." class="input-premium" />
                          </div>
                          <div v-if="form.video_provider === 'local'" class="space-y-1.5">
                              <InputLabel :value="cart.language === 'ar' ? 'استبدال ملف الفيديو MP4' : 'Replace Binary MP4 Asset'" class="text-[10px] font-black uppercase text-gray-400 px-1" />
                              <input type="file" @input="form.video_file = $event.target.files[0]" class="block w-full text-xs text-gray-400 file:mr-4 file:py-3 file:px-6 file:rounded-[10px] file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-brand-500 file:text-white hover:file:bg-brand-600 transition-all shadow-sm" accept="video/mp4,video/webm" />
                              <p v-if="product.video_path" class="mt-3 text-[10px] text-emerald-600 font-black flex items-center gap-2">
                                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293l-4 4a1 1 0 01-1.414 0l-2-2a1 1 0 111.414-1.414L9 10.586l3.293-3.293a1 1 0 111.414 1.414z"></path></svg>
                                  {{ cart.language === 'ar' ? 'يوجد فيديو مفعل حالياً لهذا المنتج' : 'Active Binary Stream Detected' }}
                              </p>
                          </div>
                      </div>
                  </div>

                  <!-- Tech Descriptions -->
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-8 border-t border-[#f8f9fb]">
                      <div class="space-y-1.5" :dir="cart.language === 'ar' ? 'ltr' : 'ltr'">
                          <InputLabel :value="cart.language === 'ar' ? 'الوصف الفني (الإنجليزية)' : 'Technical Documentation (English)'" class="text-[10px] font-black uppercase tracking-widest text-gray-400 pl-1" />
                          <textarea v-model="form.description_en" rows="6" class="input-premium !py-6 !px-8 leading-relaxed" placeholder="Detailed specs in English..."></textarea>
                      </div>
                      <div class="space-y-1.5" :dir="cart.language === 'ar' ? 'rtl' : 'ltr'">
                          <InputLabel :value="cart.language === 'ar' ? 'الوصف الفني (العربية)' : 'Technical Documentation (Arabic)'" class="text-[10px] font-black uppercase tracking-widest text-gray-400 px-1" />
                          <textarea v-model="form.description_ar" rows="6" class="input-premium !py-6 !px-8 leading-relaxed placeholder:text-right" dir="rtl" placeholder="التفاصيل الفنية بالعربية..."></textarea>
                      </div>
                  </div>

                  <!-- Image Management -->
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-10 p-12 bg-white rounded-[32px] border-2 border-[#f8f9fb] shadow-sm relative overflow-hidden group">
                      <div class="absolute top-0 left-0 w-32 h-32 bg-brand-50/50 rounded-br-full -ml-8 -mt-8"></div>
                      <div class="space-y-6 relative z-10">
                          <InputLabel :value="cart.language === 'ar' ? 'الصورة الرئيسية الحالية' : 'Primary Hardware Image'" class="text-[10px] font-black uppercase text-gray-400 px-1" />
                          <div class="flex items-center gap-8 bg-[#f8f9fb] p-6 rounded-[20px] border border-[#ececf2] group/img">
                              <img :src="product.image_url" class="h-24 w-24 object-cover rounded-[16px] bg-white ring-4 ring-white group-hover/img:scale-110 transition-transform shadow-lg" />
                              <div class="flex-1">
                                  <input type="file" @input="form.main_image = $event.target.files[0]" class="block w-full text-xs text-gray-400 file:mr-4 file:py-3 file:px-8 file:rounded-[12px] file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-brand-500 file:text-white hover:file:bg-brand-600 transition-all shadow-md" accept="image/*" />
                              </div>
                          </div>
                      </div>
                      
                      <div class="space-y-6 relative z-10">
                          <InputLabel :value="cart.language === 'ar' ? 'ألبوم صور المنتج' : 'Assembly Assets Gallery'" class="text-[10px] font-black uppercase text-gray-400 px-1" />
                          <div class="flex flex-wrap gap-3 mb-4" v-if="product.images && product.images.length">
                              <div v-for="img in product.images" :key="img.id" class="relative group animate-in zoom-in-50 duration-300">
                                  <img :src="img.image_path" class="h-14 w-14 object-cover rounded-[12px] bg-white ring-2 ring-[#ececf2] group-hover:opacity-40 transition-all shadow-sm" />
                                  <button @click.prevent="deleteImage(img.id)" class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all">
                                      <svg class="w-8 h-8 text-white bg-[#ff2e63] rounded-full p-2 shadow-2xl scale-75 group-hover:scale-100 transition-transform" fill="currentColor" viewBox="0 0 20 20"><path d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"></path></svg>
                                  </button>
                              </div>
                          </div>
                          <input type="file" multiple @input="form.gallery_images = Array.from($event.target.files)" class="block w-full text-xs text-gray-400 file:mr-4 file:py-3 file:px-8 file:rounded-[12px] file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-gray-100 file:text-gray-900 hover:file:bg-gray-200 transition-all shadow-sm" accept="image/*" />
                      </div>
                  </div>

                    <!-- Technical Metrics -->
                    <div class="space-y-8 pt-8 border-t border-[#f8f9fb]">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-xl font-black text-[#1f1f1f] tracking-tighter uppercase">{{ cart.language === 'ar' ? 'المواصفات والبيانات الفنية' : 'Hardware Performance Metrics' }}</h3>
                                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mt-1">{{ cart.language === 'ar' ? 'تعديل الخصائص والأحجام ومعايير الأداء' : 'Modify key performance thresholds and physical dimensions' }}</p>
                            </div>
                            <button @click="addSpec" type="button" class="px-6 py-3 bg-brand-50 text-brand-500 rounded-[12px] text-[10px] font-black uppercase tracking-widest hover:bg-brand-500 hover:text-white transition-all shadow-sm">
                                + {{ cart.language === 'ar' ? 'إضافة مواصفة' : 'Initialize Metric' }}
                            </button>
                        </div>
                        <div class="space-y-4">
                            <div v-for="(spec, index) in form.specs" :key="index" class="flex gap-6 items-center bg-[#f8f9fb] p-4 rounded-[20px] border border-[#ececf2] group animate-in slide-in-from-left-2 duration-300">
                                <input v-model="spec.label" :placeholder="cart.language === 'ar' ? 'العنوان (مثال: الجهد العالي)' : 'Metric Label'" class="input-premium !bg-white !shadow-none" />
                                <input v-model="spec.value" :placeholder="cart.language === 'ar' ? 'القيمة (مثال: 220 فولت)' : 'Specific Value'" class="input-premium !bg-white !shadow-none" />
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
                                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mt-1">{{ cart.language === 'ar' ? 'تعديل النماذج البرمجية المرفقة مع المنتج' : 'Update functional logic snippets and integration protocols' }}</p>
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
                            {{ cart.language === 'ar' ? 'إلغاء التعديلات' : 'Abort Session' }}
                        </Link>
                        <button 
                            type="submit" 
                            :disabled="form.processing"
                            class="w-full md:w-auto btn-red-gradient !px-20 !py-5 text-[11px]"
                        >
                            {{ form.processing ? (cart.language === 'ar' ? 'جاري الحفظ...' : 'Persisting...') : (cart.language === 'ar' ? 'تحديث مصفوفة البيانات' : 'Commit Parameters') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
