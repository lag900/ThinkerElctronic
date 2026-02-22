<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref, computed } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { cart } from '@/Stores/CartStore';

const props = defineProps({
    categories: Array,
    parentCategories: Array
});

const showModal = ref(false);
const editingCategory = ref(null);
const searchQuery = ref('');

const filteredCategories = computed(() => {
    if (!searchQuery.value) return props.categories;
    const q = searchQuery.value.toLowerCase();
    return props.categories.filter(c => 
        c.name.toLowerCase().includes(q) || 
        (c.parent && c.parent.name.toLowerCase().includes(q))
    );
});

const form = useForm({
    name: '',
    image_file: null,
    show_on_homepage: false,
    parent_id: null,
    sort_order: 0,
    is_active: true,
    slug: '',
    meta_title: '',
    meta_description: '',
    meta_keywords: '',
});

const openModal = (cat = null) => {
    editingCategory.value = cat;
    if (cat) {
        form.name = cat.name;
        form.show_on_homepage = !!cat.show_on_homepage;
        form.parent_id = cat.parent_id;
        form.sort_order = cat.sort_order || 0;
        form.is_active = !!cat.is_active;
        form.slug = cat.slug || '';
        form.meta_title = cat.meta_title || '';
        form.meta_description = cat.meta_description || '';
        form.meta_keywords = cat.meta_keywords || '';
    } else {
        form.reset();
    }
    showModal.value = true;
};

const submit = () => {
    if (editingCategory.value) {
        form.post(route('admin.categories.update', editingCategory.value.id), {
            onSuccess: () => {
                showModal.value = false;
                form.reset();
            }
        });
    } else {
        form.post(route('admin.categories.store'), {
            onSuccess: () => {
                showModal.value = false;
                form.reset();
            }
        });
    }
};

const deleteCategory = (id) => {
    if (confirm(cart.language === 'ar' ? 'هل أنت متأكد من حذف هذا التصنيف؟' : 'Are you sure you want to delete this category?')) {
        router.delete(route('admin.categories.destroy', id));
    }
};

const toggleActive = (cat) => {
    router.post(route('admin.categories.update', cat.id), { 
        ...cat, 
        _method: 'PUT', 
        is_active: !cat.is_active 
    }, { preserveScroll: true });
};

const handleImageUpload = (e) => {
    form.image_file = e.target.files[0];
};

const formatNumber = (val) => {
    return new Intl.NumberFormat(cart.language === 'ar' ? 'ar-EG' : 'en-US').format(val || 0);
};

// Helper for category colors
const getCategoryColor = (name) => {
    const colors = ['#ff2b2b', '#1e90ff', '#00a65a', '#f39c12', '#9b59b6'];
    const index = name.length % colors.length;
    return colors[index];
};
</script>

<template>
    <Head :title="cart.language === 'ar' ? 'إدارة التصنيفات' : 'Categories Management'" />

    <AdminLayout>
        <template #header>{{ cart.language === 'ar' ? 'تصنيفات المنتجات' : 'Product Categories' }}</template>

        <div class="space-y-6 font-inter bg-white -m-8 p-8 min-h-screen" :dir="cart.language === 'ar' ? 'rtl' : 'ltr'">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row justify-between items-center gap-6 pb-8 border-b border-[#f1f1f1]">
                <div>
                    <h3 class="text-2xl font-black text-[#1e1e1e] tracking-tight">{{ cart.language === 'ar' ? 'هيكل التصنيفات' : 'Categories Hierarchy' }}</h3>
                    <p class="text-xs text-gray-400 font-bold mt-1 uppercase tracking-widest">{{ cart.language === 'ar' ? 'تنظيم المنتجات في مجموعات ذكية' : 'Organize products into logical groups' }}</p>
                </div>
                
                <div class="flex flex-col md:flex-row gap-4 items-center w-full md:w-auto">
                    <!-- Search Feature -->
                    <div class="relative w-full md:w-72">
                        <svg class="absolute w-4 h-4 text-gray-400 top-1/2 -translate-y-1/2" :class="cart.language === 'ar' ? 'right-4' : 'left-4'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        <input 
                            v-model="searchQuery"
                            type="text" 
                            :placeholder="cart.language === 'ar' ? 'ابحث عن تصنيف...' : 'Search categories...'"
                            class="w-full bg-[#fcfcfc] border border-[#f1f1f1] rounded-[14px] py-3 text-sm font-medium transition-all focus:ring-4 focus:ring-[#ff2b2b]/5 focus:border-[#ff2b2b]"
                            :class="cart.language === 'ar' ? 'pr-10' : 'pl-10'"
                        >
                    </div>

                    <button @click="openModal()" class="w-full md:w-auto bg-gradient-to-r from-[#ff2b2b] to-[#ff4d4d] text-white px-8 py-3.5 rounded-[12px] font-black text-xs uppercase tracking-[0.1em] shadow-[0_10px_25px_rgba(255,43,43,0.25)] hover:scale-[1.02] active:scale-95 transition-all">
                        + {{ cart.language === 'ar' ? 'تصنيف جديد' : 'New Category' }}
                    </button>
                </div>
            </div>

            <!-- Categories Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <!-- Add New Placeholder -->
                <button @click="openModal()" class="group bg-[#fcfcfc] border-2 border-dashed border-[#f1f1f1] rounded-[24px] flex flex-col items-center justify-center p-8 hover:border-[#ff2b2b] hover:bg-[#fff3f3] transition-all min-h-[320px]">
                    <div class="w-16 h-16 bg-white border border-[#f1f1f1] rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform shadow-sm group-hover:shadow-[0_10px_20px_rgba(255,43,43,0.1)]">
                        <svg class="w-8 h-8 text-gray-300 group-hover:text-[#ff2b2b] transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                    </div>
                    <span class="text-[11px] font-black text-gray-400 group-hover:text-[#ff2b2b] uppercase tracking-widest transition-colors">{{ cart.language === 'ar' ? 'إضافة تصنيف' : 'Add Category' }}</span>
                </button>

                <div v-for="cat in filteredCategories" :key="cat.id" class="group relative bg-white p-6 rounded-[24px] border border-[#f1f1f1] shadow-[0_8px_20px_rgba(0,0,0,0.03)] hover:shadow-[0_20px_40px_rgba(255,43,43,0.12)] hover:-translate-y-1.5 transition-all duration-300 overflow-hidden flex flex-col justify-between" :class="{ 'opacity-60': !cat.is_active }">
                    
                    <div class="flex flex-col gap-6">
                        <!-- Top Row: Icon and Quick Actions -->
                        <div class="flex items-start justify-between">
                            <div class="w-14 h-14 bg-[#fff3f3] rounded-2xl flex items-center justify-center text-[#ff2b2b] border border-red-50 shadow-sm shrink-0 overflow-hidden">
                                <img v-if="cat.image" :src="cat.image" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                <svg v-else class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                            </div>
                            
                            <div class="flex gap-1.5">
                                <button @click="openModal(cat)" class="p-2 text-gray-400 hover:text-[#ff2b2b] hover:bg-[#fff3f3] rounded-lg transition-all">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </button>
                                <button @click="deleteCategory(cat.id)" class="p-2 text-gray-400 hover:text-[#ff2b2b] hover:bg-[#fff3f3] rounded-lg transition-all">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Info Row -->
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <div class="w-1.5 h-4 rounded-full" :style="{ backgroundColor: getCategoryColor(cat.name) }"></div>
                                <h4 class="font-black text-gray-900 tracking-tight text-lg truncate">{{ cat.name }}</h4>
                            </div>
                            <span class="text-[9px] font-black uppercase tracking-widest text-[#ff2b2b] bg-[#fff3f3] px-2.5 py-1 rounded-lg">
                                {{ cat.parent ? cat.parent.name : (cart.language === 'ar' ? 'تصنيف رئيسي' : 'Root Category') }}
                            </span>
                        </div>

                        <!-- Stats Grid -->
                        <div class="grid grid-cols-2 gap-3">
                            <div class="px-4 py-3 bg-[#fcfcfc] border border-[#f8f8f8] rounded-2xl">
                                <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1">{{ cart.language === 'ar' ? 'المنتجات' : 'Products' }}</p>
                                <p class="text-sm font-black text-gray-900 leading-none">{{ formatNumber(cat.products_count || 0) }}</p>
                            </div>
                            <div class="px-4 py-3 bg-[#fcfcfc] border border-[#f8f8f8] rounded-2xl">
                                <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1">{{ cart.language === 'ar' ? 'الترتيب' : 'Sorting' }}</p>
                                <p class="text-sm font-black text-gray-900 leading-none">#{{ cat.sort_order }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Actions -->
                    <div class="mt-6 pt-4 border-t border-[#f8f8f8] flex items-center justify-between">
                        <div class="flex items-center gap-2 cursor-pointer group/switch" @click="toggleActive(cat)">
                            <div 
                                class="w-9 h-5 rounded-full p-1 transition-all duration-300 relative"
                                :class="cat.is_active ? 'bg-[#ff2b2b]' : 'bg-gray-200'"
                            >
                                <div 
                                    class="w-3 h-3 bg-white rounded-full shadow-sm transition-all duration-300"
                                    :class="cat.is_active ? (cart.language === 'ar' ? '-translate-x-4' : 'translate-x-4') : 'translate-x-0'"
                                ></div>
                            </div>
                            <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest transition-colors" :class="{'text-[#ff2b2b]': cat.is_active}">
                                {{ cat.is_active ? (cart.language === 'ar' ? 'نشط' : 'Active') : (cart.language === 'ar' ? 'مخفي' : 'Hidden') }}
                            </span>
                        </div>
                        
                        <div v-if="cat.show_on_homepage" class="text-[9px] font-black text-emerald-600 uppercase tracking-widest flex items-center gap-1">
                            <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></div>
                            {{ cart.language === 'ar' ? 'يظهر بالرئيسية' : 'On Homepage' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div v-if="showModal" class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-gray-900/40 backdrop-blur-sm animate-in fade-in duration-300" :dir="cart.language === 'ar' ? 'rtl' : 'ltr'" @click.self="showModal = false">
            <div class="bg-white w-full max-w-xl rounded-[28px] shadow-2xl overflow-hidden animate-in zoom-in-95 duration-500">
                <div class="p-8 border-b border-[#f1f1f1] flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-black text-[#1e1e1e] tracking-tighter">{{ editingCategory ? (cart.language === 'ar' ? 'تعديل التصنيف' : 'Edit Category') : (cart.language === 'ar' ? 'إضافة تصنيف جديد' : 'New Category') }}</h3>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Setup category metadata and hierarchy</p>
                    </div>
                    <button @click="showModal = false" class="p-2 text-gray-400 hover:text-[#ff2b2b] transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <form @submit.prevent="submit" class="p-8 space-y-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 px-1">{{ cart.language === 'ar' ? 'اسم القسم' : 'Category Name' }}</label>
                        <input v-model="form.name" type="text" placeholder="e.g. Smartphones, Electronics" class="w-full bg-[#fcfcfc] border border-[#f1f1f1] rounded-[16px] py-4 px-6 text-sm font-bold text-gray-900 focus:ring-4 focus:ring-[#ff2b2b]/5 focus:border-[#ff2b2b] outline-none" required>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 px-1">{{ cart.language === 'ar' ? 'القسم الأب' : 'Parent' }}</label>
                            <select v-model="form.parent_id" class="w-full bg-[#fcfcfc] border border-[#f1f1f1] rounded-[16px] py-4 px-6 text-sm font-bold text-gray-900 focus:ring-4 focus:ring-[#ff2b2b]/5 focus:border-[#ff2b2b] outline-none appearance-none">
                                <option :value="null">{{ cart.language === 'ar' ? '--- أساسي ---' : 'None (Root)' }}</option>
                                <option v-for="pc in parentCategories" :key="pc.id" :value="pc.id" :disabled="pc.id === editingCategory?.id">{{ pc.name }}</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 px-1">{{ cart.language === 'ar' ? 'الترتيب' : 'Sort Order' }}</label>
                            <input v-model="form.sort_order" type="number" class="w-full bg-[#fcfcfc] border border-[#f1f1f1] rounded-[16px] py-4 px-6 text-sm font-bold text-gray-900 focus:ring-4 focus:ring-[#ff2b2b]/5 focus:border-[#ff2b2b] outline-none">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 px-1">{{ cart.language === 'ar' ? 'الصورة أو الأيقونة' : 'Category Icon/Image' }}</label>
                        <div class="flex items-center gap-6 p-4 bg-[#fcfcfc] border border-[#f1f1f1] rounded-[20px]">
                            <div class="w-16 h-16 bg-white border border-[#f1f1f1] rounded-2xl flex items-center justify-center relative overflow-hidden group shadow-sm">
                                <input type="file" @change="handleImageUpload" class="absolute inset-0 opacity-0 cursor-pointer z-10" accept="image/*">
                                <svg class="w-7 h-7 text-gray-300 group-hover:text-[#ff2b2b] transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                            </div>
                            <div class="flex-1 overflow-hidden">
                                <p class="text-xs font-bold text-[#1e1e1e] truncate">{{ form.image_file ? form.image_file.name : (cart.language === 'ar' ? 'اختر صورة...' : 'Click to upload...') }}</p>
                                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-1">Recommended 512x512 PNG</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="flex items-center justify-between bg-[#fcfcfc] p-4 rounded-2xl cursor-pointer hover:bg-white border border-[#f1f1f1] transition-all" @click="form.show_on_homepage = !form.show_on_homepage">
                            <span class="text-[10px] font-black text-gray-900 uppercase tracking-widest">{{ cart.language === 'ar' ? 'الرئيسية' : 'On Home' }}</span>
                            <div class="w-9 h-5 rounded-full p-1 transition-all" :class="form.show_on_homepage ? 'bg-emerald-500' : 'bg-gray-200'">
                                <div class="w-3 h-3 bg-white rounded-full shadow-sm transition-all" :class="form.show_on_homepage ? (cart.language === 'ar' ? '-translate-x-4' : 'translate-x-4') : 'translate-x-0'"></div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between bg-[#fcfcfc] p-4 rounded-2xl cursor-pointer hover:bg-white border border-[#f1f1f1] transition-all" @click="form.is_active = !form.is_active">
                            <span class="text-[10px] font-black text-gray-900 uppercase tracking-widest">{{ cart.language === 'ar' ? 'مفعل' : 'Is Active' }}</span>
                            <div class="w-9 h-5 rounded-full p-1 transition-all" :class="form.is_active ? 'bg-[#ff2b2b]' : 'bg-gray-200'">
                                <div class="w-3 h-3 bg-white rounded-full shadow-sm transition-all" :class="form.is_active ? (cart.language === 'ar' ? '-translate-x-4' : 'translate-x-4') : 'translate-x-0'"></div>
                            </div>
                        </div>
                    </div>

                    <!-- SEO Fields -->
                    <div class="space-y-4 pt-4 border-t border-gray-100">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-[#ff2b2b] px-1">SEO Matrix (Slug & Meta)</label>
                            <input v-model="form.slug" type="text" placeholder="category-slug" class="w-full bg-[#fcfcfc] border border-[#f1f1f1] rounded-[16px] py-4 px-6 text-sm font-bold text-gray-900 focus:ring-4 focus:ring-[#ff2b2b]/5 focus:border-[#ff2b2b] outline-none mb-2">
                            <input v-model="form.meta_title" type="text" placeholder="Meta Title..." class="w-full bg-[#fcfcfc] border border-[#f1f1f1] rounded-[16px] py-4 px-6 text-sm font-bold text-gray-900 focus:ring-4 focus:ring-[#ff2b2b]/5 focus:border-[#ff2b2b] outline-none mb-2">
                            <textarea v-model="form.meta_description" rows="2" placeholder="Meta Description..." class="w-full bg-[#fcfcfc] border border-[#f1f1f1] rounded-[16px] py-4 px-6 text-sm font-bold text-gray-900 focus:ring-4 focus:ring-[#ff2b2b]/5 focus:border-[#ff2b2b] outline-none mb-2"></textarea>
                            <input v-model="form.meta_keywords" type="text" placeholder="keywords, comma, separated" class="w-full bg-[#fcfcfc] border border-[#f1f1f1] rounded-[16px] py-4 px-6 text-sm font-bold text-gray-900 focus:ring-4 focus:ring-[#ff2b2b]/5 focus:border-[#ff2b2b] outline-none">
                        </div>
                    </div>

                    <button type="submit" class="w-full py-5 bg-gradient-to-r from-[#ff2b2b] to-[#ff4d4d] text-white rounded-[16px] font-black uppercase tracking-[0.2em] text-xs shadow-[0_15px_30px_rgba(255,43,43,0.3)] hover:scale-[1.01] hover:-translate-y-1 transition-all active:scale-95 disabled:opacity-50" :disabled="form.processing">
                        {{ form.processing ? '...' : (editingCategory ? (cart.language === 'ar' ? 'حفظ التغييرات' : 'Save Changes') : (cart.language === 'ar' ? 'إضافة القسم' : 'Create Category')) }}
                    </button>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
