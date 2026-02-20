<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { cart } from '@/Stores/CartStore';

const props = defineProps({
    categories: Array
});

const showModal = ref(false);
const editingCategory = ref(null);
const form = useForm({
    name: '',
    image_file: null,
    show_on_homepage: false,
});

const openModal = (cat = null) => {
    editingCategory.value = cat;
    if (cat) {
        form.name = cat.name;
        form.show_on_homepage = !!cat.show_on_homepage;
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
    if (confirm('Are you sure you want to delete this category node?')) {
        router.delete(route('admin.categories.destroy', id));
    }
};

const handleImageUpload = (e) => {
    form.image_file = e.target.files[0];
};

const formatNumber = (val) => {
    return new Intl.NumberFormat(cart.language === 'ar' ? 'ar-EG' : 'en-US').format(val);
};
</script>

<template>
    <Head :title="cart.t('categories')" />

    <AdminLayout>
        <template #header>{{ cart.language === 'ar' ? 'هندسة التصنيفات' : 'Categories Architecture' }}</template>

        <div class="space-y-12">
            <div class="flex justify-between items-center bg-white p-10 rounded-[50px] border border-slate-50 shadow-[0_20px_60px_rgba(0,0,0,0.02)]">
                <div>
                    <h3 class="text-3xl font-black text-[#222] tracking-tighter">{{ cart.language === 'ar' ? 'عقد التصنيف' : 'Classification Nodes' }}</h3>
                    <p class="text-[10px] font-black text-slate-300 uppercase tracking-[0.4em] mt-2">Managing Product Hierarchies</p>
                </div>
                <button @click="openModal()" class="bg-[#222] hover:bg-brand-500 text-white px-8 py-4 rounded-[28px] font-black uppercase tracking-widest text-[11px] transition-all shadow-xl shadow-black/10 active:scale-95">
                    + {{ cart.language === 'ar' ? 'تصنيف جديد' : 'New Category' }}
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                <div v-for="cat in categories" :key="cat.id" class="group relative bg-white p-8 rounded-[50px] border border-slate-50 shadow-sm hover:shadow-[0_40px_100px_rgba(0,0,0,0.05)] hover:-translate-y-3 transition-all duration-700 overflow-hidden">
                    <div class="flex items-center gap-6 mb-8 relative z-10">
                        <div class="w-20 h-20 rounded-[28px] overflow-hidden border border-slate-100 shrink-0 shadow-sm group-hover:border-brand-500/30 transition-all duration-500">
                            <img v-if="cat.image" :src="cat.image" class="w-full h-full object-cover group-hover:scale-110 transition-all duration-700">
                            <div v-else class="w-full h-full bg-slate-50 flex items-center justify-center text-slate-200">
                                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            </div>
                        </div>
                        <div class="overflow-hidden">
                            <h4 class="font-black text-[#222] tracking-tighter text-xl truncate">{{ cat.name }}</h4>
                            <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest mt-1">{{ formatNumber(cat.products_count || 0) }} Data Hubs</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-8 border-t border-slate-50 relative z-10">
                        <div class="flex items-center gap-3">
                            <div 
                                class="w-10 h-6 rounded-full transition-all cursor-pointer p-1"
                                :class="cat.show_on_homepage ? 'bg-emerald-500' : 'bg-slate-200'"
                            >
                                <div class="w-4 h-4 bg-white rounded-full shadow-sm transition-all" :class="cat.show_on_homepage ? (cart.language === 'ar' ? '-translate-x-4' : 'translate-x-4') : 'translate-x-0'"></div>
                            </div>
                            <span class="text-[9px] font-black uppercase text-slate-400 tracking-widest">Visibility</span>
                        </div>
                        
                        <div class="flex gap-3">
                            <button @click="openModal(cat)" class="p-3 bg-slate-50 hover:bg-brand-50 rounded-2xl text-slate-400 hover:text-brand-500 transition-all">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                            </button>
                            <button @click="deleteCategory(cat.id)" class="p-3 bg-slate-50 hover:bg-red-50 rounded-2xl text-slate-400 hover:text-red-500 transition-all">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Professional Modal -->
        <div v-if="showModal" class="fixed inset-0 z-[100] flex items-center justify-center p-6 backdrop-blur-md bg-white/40">
            <div class="bg-white w-full max-w-lg rounded-[60px] shadow-[0_40px_100px_rgba(0,0,0,0.1)] border border-slate-50 p-16 relative animate-in zoom-in duration-500">
                <button @click="showModal = false" class="absolute top-12 right-12 p-3 bg-slate-50 rounded-3xl text-slate-400 hover:text-brand-500 transition-all">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>

                <h3 class="text-4xl font-black text-[#222] tracking-tighter mb-4">{{ editingCategory ? 'Modify Matrix' : 'Initialize Matrix' }}</h3>
                <p class="text-[10px] font-black uppercase tracking-[0.4em] text-slate-300 mb-12">Category Transmission Protocol</p>

                <form @submit.prevent="submit" class="space-y-10">
                    <div>
                        <label class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400 block mb-4">Node Title</label>
                        <input v-model="form.name" type="text" class="w-full bg-slate-50 border-none rounded-3xl py-5 px-8 text-sm font-bold focus:ring-2 focus:ring-brand-500 transition-all outline-none" required>
                    </div>

                    <div>
                        <label class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400 block mb-4">Visual data</label>
                        <div class="flex items-center gap-8">
                            <div class="w-24 h-24 bg-slate-50 rounded-[32px] border-2 border-dashed border-slate-200 flex items-center justify-center text-slate-300 overflow-hidden relative group">
                                <input type="file" @change="handleImageUpload" class="absolute inset-0 opacity-0 cursor-pointer z-10">
                                <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-[11px] font-black text-[#222] truncate">{{ form.image_file ? form.image_file.name : 'Awaiting file...' }}</p>
                                <p class="text-[9px] font-bold text-slate-300 uppercase tracking-widest mt-1">Maximum size: 2MB Node</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-6 bg-slate-50 p-8 rounded-[40px] cursor-pointer group" @click="form.show_on_homepage = !form.show_on_homepage">
                        <div class="w-12 h-7 rounded-full p-1.5 transition-all" :class="form.show_on_homepage ? 'bg-emerald-500' : 'bg-slate-200'">
                            <div class="w-4 h-4 bg-white rounded-full shadow-sm transition-all" :class="form.show_on_homepage ? (cart.language === 'ar' ? '-translate-x-5' : 'translate-x-5') : 'translate-x-0'"></div>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs font-black uppercase tracking-widest text-[#222]">Reveal on Homepage</p>
                            <p class="text-[9px] font-bold text-slate-300 uppercase mt-0.5">Toggle visibility for public data stream</p>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-6 bg-[#222] text-white rounded-[32px] font-black uppercase tracking-widest text-xs shadow-2xl shadow-black/20 hover:bg-brand-500 transition-all active:scale-95 disabled:opacity-50" :disabled="form.processing">
                        {{ editingCategory ? 'RE-MAP NODE' : 'EXECUTE INITIALIZATION' }}
                    </button>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
