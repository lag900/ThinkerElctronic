<script setup>
import { ref, watch, computed } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { system } from '@/Stores/SystemStore';

const props = defineProps({
    zones: Object,
    filters: Object
});

const searchQuery = ref(props.filters.search || '');
const showAddModal = ref(false);
const editingZone = ref(null);

const form = useForm({
    city: '',
    price: 0,
    estimated_days: '',
    is_active: true,
    is_free: false,
    express_price: null,
    free_delivery_threshold: null
});

let searchTimeout = null;
watch(searchQuery, (value) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(
            route('admin.delivery-zones.index'),
            { search: value },
            { preserveState: true, replace: true }
        );
    }, 500);
});

const openModal = (zone = null) => {
    editingZone.value = zone;
    if (zone) {
        form.city = zone.city;
        form.price = zone.price;
        form.estimated_days = zone.estimated_days;
        form.is_active = zone.is_active;
        form.is_free = zone.is_free;
        form.express_price = zone.express_price;
        form.free_delivery_threshold = zone.free_delivery_threshold;
    } else {
        form.reset();
    }
    showAddModal.value = true;
};

const submitForm = () => {
    if (editingZone.value) {
        form.put(route('admin.delivery-zones.update', editingZone.value.id), {
            onSuccess: () => {
                showAddModal.value = false;
                system.showNotification('Done', 'Zone updated successfully.');
            }
        });
    } else {
        form.post(route('admin.delivery-zones.store'), {
            onSuccess: () => {
                showAddModal.value = false;
                system.showNotification('Done', 'Zone added successfully.');
            }
        });
    }
};

const toggleStatus = (zone) => {
    router.put(route('admin.delivery-zones.toggle', zone.id), {}, { preserveScroll: true });
};

const deleteZone = (zone) => {
    if (confirm('Are you certain you want to delete this zone?')) {
        router.delete(route('admin.delivery-zones.destroy', zone.id), {
            onSuccess: () => system.showNotification('Done', 'Zone removed.')
        });
    }
};
</script>

<template>
    <AdminLayout>
        <template #header>Delivery Pricing Control</template>
        
        <div class="max-w-[1400px] mx-auto space-y-8">

            <!-- Stats & Header Block -->
            <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                <div>
                    <h3 class="text-3xl font-black text-[#222] tracking-tighter">Governorate Rules</h3>
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mt-2">Manage ERP Shipping Algorithms</p>
                </div>
                
                <div class="flex items-center gap-4 w-full md:w-auto">
                    <!-- Search Input -->
                    <div class="relative flex-1 md:w-64">
                        <input 
                            v-model="searchQuery" 
                            type="text" 
                            placeholder="Find governorate..." 
                            class="w-full h-12 rounded-2xl border-none bg-white shadow-sm hover:shadow transition-shadow pl-12 pr-4 font-bold text-sm focus:ring-2 focus:ring-brand-500"
                        >
                        <svg class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </div>

                    <button @click="openModal()" class="h-12 bg-brand-500 text-white px-6 rounded-2xl text-[11px] font-black uppercase tracking-widest shadow-lg shadow-brand-500/20 hover:scale-[1.02] transition-transform shrink-0 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                        Add Zone
                    </button>
                </div>
            </div>

            <!-- Table Block -->
            <div class="bg-white rounded-[32px] p-2 shadow-sm border border-slate-100">
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">
                                <th class="p-6">Governorate</th>
                                <th class="p-6 text-right">Standard Price</th>
                                <th class="p-6 text-right">Express / Free</th>
                                <th class="p-6 text-center">Delivery Time</th>
                                <th class="p-6 text-center">Status</th>
                                <th class="p-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="zone in zones.data" :key="zone.id" class="border-t border-slate-50 hover:bg-slate-50/50 transition-colors group">
                                <td class="p-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 font-black text-brand-500 bg-brand-50">
                                            {{ zone.city.substring(0,2).toUpperCase() }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-black text-[#222]">{{ zone.city }}</div>
                                            <div class="text-[10px] font-bold uppercase tracking-widest text-[#999] mt-0.5" v-if="zone.free_delivery_threshold">
                                                Free threshold: {{ zone.free_delivery_threshold }} EGP
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="p-6 text-right font-black text-[#222]">
                                    <template v-if="zone.is_free">
                                        <span class="text-brand-500">FREE</span>
                                    </template>
                                    <template v-else>
                                        {{ zone.price }} EGP
                                    </template>
                                </td>

                                <td class="p-6 text-right">
                                    <div class="text-[11px] font-bold text-slate-500 uppercase tracking-widest">EXP: {{ zone.express_price ? `${zone.express_price} EGP` : 'N/A' }}</div>
                                </td>

                                <td class="p-6 text-center">
                                    <span class="px-3 py-1 bg-slate-100 text-slate-600 rounded-lg text-xs font-bold">{{ zone.estimated_days }}</span>
                                </td>

                                <td class="p-6 text-center">
                                    <button 
                                        @click="toggleStatus(zone)"
                                        class="w-12 h-6 rounded-full relative transition-colors duration-300"
                                        :class="zone.is_active ? 'bg-brand-500' : 'bg-slate-200'"
                                    >
                                        <div 
                                            class="absolute top-1 left-1 bg-white w-4 h-4 rounded-full transition-transform duration-300"
                                            :class="zone.is_active ? 'translate-x-6' : 'translate-x-0'"
                                        ></div>
                                    </button>
                                </td>

                                <td class="p-6 text-right space-x-2">
                                    <!-- Edit -->
                                    <button @click="openModal(zone)" class="w-10 h-10 inline-flex items-center justify-center rounded-xl hover:bg-white border transform transition-all text-slate-400 hover:text-brand-500 hover:border-brand-500">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                    </button>
                                    <!-- Delete -->
                                    <button @click="deleteZone(zone)" class="w-10 h-10 inline-flex items-center justify-center rounded-xl hover:bg-white border transform transition-all text-slate-400 hover:text-red-500 hover:border-red-500">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div v-if="zones.data.length === 0" class="p-12 text-center">
                        <span class="text-sm font-bold text-slate-400 block uppercase tracking-widest">No governorates found or added yet.</span>
                    </div>
                </div>
            </div>
            
            <!-- Pagination inside dashboard logic -->
            <div v-if="zones.links && zones.data.length > 0" class="flex justify-center gap-2 mt-8 pb-10">
                <template v-for="(link, key) in zones.links" :key="key">
                    <Link 
                        v-if="link.url"
                        :href="link.url" 
                        v-html="link.label"
                        class="px-4 py-2 rounded-xl text-xs font-black transition-all"
                        :class="link.active ? 'bg-brand-500 text-white shadow-lg' : 'bg-white text-slate-400 hover:text-[#222] border'"
                    />
                    <span v-else v-html="link.label" class="px-4 py-2 text-xs font-black text-slate-400 opacity-50"></span>
                </template>
            </div>
        </div>

        <!-- Add / Edit Modal -->
        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
            <div v-if="showAddModal" class="fixed inset-0 z-[200] flex items-center justify-center p-4">
                <div @click="showAddModal = false" class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
                <div class="relative bg-white rounded-[32px] shadow-2xl w-full max-w-xl overflow-hidden p-8 border border-white">
                    <h2 class="text-2xl font-black text-[#222] tracking-tight mb-8">{{ editingZone ? 'Edit Zone' : 'Create Pricing Zone' }}</h2>
                    <form @submit.prevent="submitForm" class="space-y-6">
                        
                        <div class="grid grid-cols-2 gap-6">
                            <div class="col-span-2">
                                <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-2">Governorate / City</label>
                                <input v-model="form.city" type="text" class="w-full h-12 rounded-2xl bg-slate-50 border-transparent focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500 font-bold px-4 transition-all" required>
                                <span v-if="form.errors.city" class="text-xs text-brand-500 font-bold mt-1 block">{{ form.errors.city }}</span>
                            </div>

                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-2">Standard Delivery (EGP)</label>
                                <input v-model="form.price" type="number" step="0.01" class="w-full h-12 rounded-2xl bg-slate-50 border-transparent focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500 font-black px-4 transition-all" required>
                                <span v-if="form.errors.price" class="text-xs text-brand-500 font-bold mt-1 block">{{ form.errors.price }}</span>
                            </div>
                            
                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-2">Estimated Days</label>
                                <input v-model="form.estimated_days" type="text" placeholder="e.g. 1-2 Days" class="w-full h-12 rounded-2xl bg-slate-50 border-transparent focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500 font-bold px-4 transition-all" required>
                                <span v-if="form.errors.estimated_days" class="text-xs text-brand-500 font-bold mt-1 block">{{ form.errors.estimated_days }}</span>
                            </div>

                            <div class="col-span-2 p-6 rounded-2xl border border-slate-100 bg-slate-50/50 space-y-6">
                                <h4 class="text-[10px] font-black text-brand-500 uppercase tracking-widest border-b border-slate-200 pb-2">Advanced Config</h4>
                                
                                <div>
                                    <label class="flex items-center gap-3 cursor-pointer">
                                        <div class="w-5 h-5 rounded flex items-center justify-center transition-colors" :class="form.is_free ? 'bg-brand-500' : 'bg-slate-200'">
                                            <svg v-if="form.is_free" class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                        </div>
                                        <input type="checkbox" v-model="form.is_free" class="hidden">
                                        <span class="text-xs font-bold uppercase tracking-widest">Always Free Delivery</span>
                                    </label>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-6" v-if="!form.is_free">
                                    <div>
                                        <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-2">Express Target (EGP) optional</label>
                                        <input v-model="form.express_price" type="number" step="0.01" class="w-full h-12 rounded-xl bg-white border border-slate-100 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 font-bold px-4 transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-2">Order Threshold for Free</label>
                                        <input v-model="form.free_delivery_threshold" type="number" step="0.01" class="w-full h-12 rounded-xl bg-white border border-slate-100 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 font-bold px-4 transition-all" placeholder="e.g. 1000">
                                    </div>
                                </div>
                            </div>

                            <div class="col-span-2 mt-2">
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <div class="w-12 h-6 rounded-full relative transition-colors duration-300" :class="form.is_active ? 'bg-emerald-500' : 'bg-slate-200'">
                                        <div class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full transition-transform duration-300" :class="form.is_active ? 'translate-x-6' : 'translate-x-0'"></div>
                                    </div>
                                    <input type="checkbox" v-model="form.is_active" class="hidden">
                                    <span class="text-xs font-black tracking-widest uppercase" :class="form.is_active ? 'text-emerald-500' : 'text-slate-400'">{{ form.is_active ? 'Zone Verified Active' : 'Zone Deactivated' }}</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 pt-6">
                            <button type="submit" :disabled="form.processing" class="flex-1 h-14 bg-[#222] hover:bg-brand-500 text-white rounded-2xl font-black uppercase tracking-widest text-xs transition-colors shadow-lg active:scale-[0.98] disabled:opacity-50">
                                {{ form.processing ? 'Saving...' : (editingZone ? 'Update Engine' : 'Deploy Pricing') }}
                            </button>
                            <button type="button" @click="showAddModal = false" class="px-8 h-14 border border-slate-200 rounded-2xl font-black uppercase tracking-widest text-[#222] text-xs hover:bg-slate-50 transition-colors">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Transition>

    </AdminLayout>
</template>
