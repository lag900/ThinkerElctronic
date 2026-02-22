<script setup>
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    governorates: Array
});

const showModal = ref(false);
const editingGov = ref(null);

const form = useForm({
    name_en: '',
    name_ar: '',
    default_delivery_price: 0,
    is_active: true
});

const openModal = (gov = null) => {
    editingGov.value = gov;
    if (gov) {
        form.name_en = gov.name_en;
        form.name_ar = gov.name_ar;
        form.default_delivery_price = gov.default_delivery_price;
        form.is_active = gov.is_active;
    } else {
        form.reset();
    }
    showModal.value = true;
};

const submitForm = () => {
    if (editingGov.value) {
        form.put(route('admin.governorates.update', editingGov.value.id), {
            onSuccess: () => showModal.value = false
        });
    } else {
        form.post(route('admin.governorates.store'), {
            onSuccess: () => showModal.value = false
        });
    }
};

const deleteGov = (id) => {
    if (confirm('Are you sure? This will delete all areas in this governorate.')) {
        router.delete(route('admin.governorates.destroy', id));
    }
};
</script>

<template>
    <AdminLayout>
        <template #header>Governorates Management</template>

        <div class="max-w-[1400px] mx-auto space-y-8">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-3xl font-black text-[#222] tracking-tighter">Governorates</h3>
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mt-2">Manage Delivery Regions</p>
                </div>
                <button @click="openModal()" class="h-12 bg-[#ff0033] text-white px-8 rounded-2xl text-[11px] font-black uppercase tracking-widest shadow-lg shadow-red-500/20 hover:scale-105 transition-all">
                    Add Governorate
                </button>
            </div>

            <div class="bg-white rounded-[32px] p-2 shadow-sm border border-slate-100 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">
                            <th class="p-6">Name (EN)</th>
                            <th class="p-6">Name (AR)</th>
                            <th class="p-6">Default Price</th>
                            <th class="p-6 text-center">Areas</th>
                            <th class="p-6 text-center">Status</th>
                            <th class="p-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="gov in governorates" :key="gov.id" class="border-t border-slate-50 hover:bg-slate-50/50 transition-colors">
                            <td class="p-6 font-black text-[#222]">{{ gov.name_en }}</td>
                            <td class="p-6 font-bold text-[#222]">{{ gov.name_ar }}</td>
                            <td class="p-6 font-black text-[#ff0033]">{{ gov.default_delivery_price }} EGP</td>
                            <td class="p-6 text-center font-bold text-slate-500">{{ gov.areas_count }}</td>
                            <td class="p-6 text-center">
                                <span :class="gov.is_active ? 'bg-emerald-50 text-emerald-500' : 'bg-red-50 text-red-500'" class="px-4 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest">
                                    {{ gov.is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="p-6 text-right space-x-2">
                                <button @click="openModal(gov)" class="w-10 h-10 inline-flex items-center justify-center rounded-xl hover:bg-white border transition-all text-slate-400 hover:text-[#ff0033] hover:border-[#ff0033]">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                </button>
                                <button @click="deleteGov(gov.id)" class="w-10 h-10 inline-flex items-center justify-center rounded-xl hover:bg-white border transition-all text-slate-400 hover:text-red-500 hover:border-red-500">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal -->
        <Transition name="modal">
            <div v-if="showModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
                <div @click="showModal = false" class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
                <div class="relative bg-white rounded-[32px] shadow-2xl w-full max-w-lg p-8">
                    <h2 class="text-2xl font-black text-[#222] tracking-tight mb-8">{{ editingGov ? 'Edit Governorate' : 'Add Governorate' }}</h2>
                    <form @submit.prevent="submitForm" class="space-y-6">
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Name (English)</label>
                            <input v-model="form.name_en" type="text" class="w-full h-12 rounded-2xl bg-slate-50 border-transparent focus:bg-white focus:border-[#ff0033] focus:ring-0 font-bold px-4 transition-all">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Name (Arabic)</label>
                            <input v-model="form.name_ar" type="text" class="w-full h-12 rounded-2xl bg-slate-50 border-transparent focus:bg-white focus:border-[#ff0033] focus:ring-0 font-bold px-4 transition-all">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Default Delivery Price (EGP)</label>
                            <input v-model="form.default_delivery_price" type="number" step="0.01" class="w-full h-12 rounded-2xl bg-slate-50 border-transparent focus:bg-white focus:border-[#ff0033] focus:ring-0 font-bold px-4 transition-all">
                        </div>
                        <div class="flex items-center gap-3">
                            <input type="checkbox" v-model="form.is_active" id="is_active" class="w-5 h-5 rounded border-slate-200 text-[#ff0033] focus:ring-0">
                            <label for="is_active" class="text-xs font-black uppercase tracking-widest text-[#222]">Active Status</label>
                        </div>
                        <div class="flex gap-4 pt-4">
                            <button type="submit" class="flex-1 h-14 bg-[#ff0033] text-white rounded-2xl font-black uppercase tracking-widest text-xs transition-transform active:scale-95 shadow-lg shadow-red-500/20">
                                {{ editingGov ? 'Update' : 'Create' }}
                            </button>
                            <button @click="showModal = false" type="button" class="px-8 h-14 border border-slate-200 rounded-2xl font-black uppercase tracking-widest text-[#222] text-xs hover:bg-slate-50">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Transition>
    </AdminLayout>
</template>

<style scoped>
.modal-enter-active, .modal-leave-active { transition: all 0.3s ease; }
.modal-enter-from, .modal-leave-to { opacity: 0; transform: scale(0.95); }
</style>
