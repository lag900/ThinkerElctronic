<script setup>
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    areas: Array,
    governorates: Array
});

const showModal = ref(false);
const editingArea = ref(null);

const form = useForm({
    governorate_id: '',
    name_en: '',
    name_ar: '',
    delivery_price: 0,
    express_price: '',
    delivery_days: '',
    is_active: true
});

const openModal = (area = null) => {
    editingArea.value = area;
    if (area) {
        form.governorate_id = area.governorate_id;
        form.name_en = area.name_en;
        form.name_ar = area.name_ar;
        form.delivery_price = area.delivery_price;
        form.express_price = area.express_price;
        form.delivery_days = area.delivery_days;
        form.is_active = area.is_active;
    } else {
        form.reset();
        if (props.governorates.length) form.governorate_id = props.governorates[0].id;
    }
    showModal.value = true;
};

const submitForm = () => {
    if (editingArea.value) {
        form.put(route('admin.areas.update', editingArea.value.id), {
            onSuccess: () => showModal.value = false
        });
    } else {
        form.post(route('admin.areas.store'), {
            onSuccess: () => showModal.value = false
        });
    }
};

const deleteArea = (id) => {
    if (confirm('Are you sure?')) {
        router.delete(route('admin.areas.destroy', id));
    }
};
</script>

<template>
    <AdminLayout>
        <template #header>Areas Management</template>

        <div class="max-w-[1400px] mx-auto space-y-8">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-3xl font-black text-[#222] tracking-tighter">Delivery Areas</h3>
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mt-2">Manage Specific Zone Pricing</p>
                </div>
                <button @click="openModal()" class="h-12 bg-[#ff0033] text-white px-8 rounded-2xl text-[11px] font-black uppercase tracking-widest shadow-lg shadow-red-500/20 hover:scale-105 transition-all">
                    Add Area
                </button>
            </div>

            <div class="bg-white rounded-[32px] p-2 shadow-sm border border-slate-100 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">
                            <th class="p-6">Governorate</th>
                            <th class="p-6">Area Name</th>
                            <th class="p-6">Standard Price</th>
                            <th class="p-6">Express Price</th>
                            <th class="p-6">Delivery Days</th>
                            <th class="p-6 text-center">Status</th>
                            <th class="p-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="area in areas" :key="area.id" class="border-t border-slate-50 hover:bg-slate-50/50 transition-colors">
                            <td class="p-6">
                                <span class="bg-slate-100 text-slate-500 px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest animate-pulse">
                                    {{ area.governorate?.name_en }}
                                </span>
                            </td>
                            <td class="p-6">
                                <div class="font-black text-[#222] text-sm">{{ area.name_en }}</div>
                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ area.name_ar }}</div>
                            </td>
                            <td class="p-6 font-black text-[#ff0033]">{{ area.delivery_price }} EGP</td>
                            <td class="p-6 font-bold text-slate-400">{{ area.express_price ? `${area.express_price} EGP` : '—' }}</td>
                            <td class="p-6">
                                <span class="text-xs font-bold text-slate-500">{{ area.delivery_days }}</span>
                            </td>
                            <td class="p-6 text-center">
                                <span :class="area.is_active ? 'bg-emerald-50 text-emerald-500' : 'bg-red-50 text-red-500'" class="px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest">
                                    {{ area.is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="p-6 text-right space-x-2">
                                <button @click="openModal(area)" class="w-10 h-10 inline-flex items-center justify-center rounded-xl hover:bg-white border transition-all text-slate-400 hover:text-[#ff0033] hover:border-[#ff0033]">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                </button>
                                <button @click="deleteArea(area.id)" class="w-10 h-10 inline-flex items-center justify-center rounded-xl hover:bg-white border transition-all text-slate-400 hover:text-red-500 hover:border-red-500">
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
                <div class="relative bg-white rounded-[32px] shadow-2xl w-full max-w-xl p-8 max-h-[90vh] overflow-y-auto custom-scrollbar">
                    <h2 class="text-2xl font-black text-[#222] tracking-tight mb-8">{{ editingArea ? 'Edit Area' : 'Add New Area' }}</h2>
                    <form @submit.prevent="submitForm" class="grid grid-cols-2 gap-6">
                        <div class="col-span-2">
                            <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Governorate</label>
                            <select v-model="form.governorate_id" class="w-full h-12 rounded-2xl bg-slate-50 border-transparent focus:bg-white focus:border-[#ff0033] focus:ring-0 font-bold px-4 transition-all">
                                <option v-for="gov in governorates" :key="gov.id" :value="gov.id">{{ gov.name_en }} / {{ gov.name_ar }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Area Name (EN)</label>
                            <input v-model="form.name_en" type="text" class="w-full h-12 rounded-2xl bg-slate-50 border-transparent focus:bg-white focus:border-[#ff0033] focus:ring-0 font-bold px-4 transition-all">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Area Name (AR)</label>
                            <input v-model="form.name_ar" type="text" class="w-full h-12 rounded-2xl bg-slate-50 border-transparent focus:bg-white focus:border-[#ff0033] focus:ring-0 font-bold px-4 transition-all">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Delivery Price</label>
                            <input v-model="form.delivery_price" type="number" step="0.01" class="w-full h-12 rounded-2xl bg-slate-50 border-transparent focus:bg-white focus:border-[#ff0033] focus:ring-0 font-black px-4 transition-all">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Express Price (Optional)</label>
                            <input v-model="form.express_price" type="number" step="0.01" class="w-full h-12 rounded-2xl bg-slate-50 border-transparent focus:bg-white focus:border-[#ff0033] focus:ring-0 font-black px-4 transition-all">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Delivery Time (Days)</label>
                            <input v-model="form.delivery_days" type="text" placeholder="e.g. 1-2 days" class="w-full h-12 rounded-2xl bg-slate-50 border-transparent focus:bg-white focus:border-[#ff0033] focus:ring-0 font-bold px-4 transition-all">
                        </div>
                        <div class="flex items-center gap-3 pt-6">
                            <input type="checkbox" v-model="form.is_active" id="is_active_area" class="w-5 h-5 rounded border-slate-200 text-[#ff0033] focus:ring-0">
                            <label for="is_active_area" class="text-xs font-black uppercase tracking-widest text-[#222]">Active</label>
                        </div>
                        <div class="col-span-2 flex gap-4 pt-4">
                            <button type="submit" class="flex-1 h-14 bg-[#ff0033] text-white rounded-2xl font-black uppercase tracking-widest text-xs transition-transform active:scale-95 shadow-lg shadow-red-500/20">
                                {{ editingArea ? 'Update Area' : 'Create Area' }}
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
