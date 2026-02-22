<script setup>
import { useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    settings: Object
});

const form = useForm({
    free_delivery_over: props.settings?.free_delivery_over || '',
    express_enabled: props.settings?.express_enabled ?? true
});

const submit = () => {
    form.post(route('admin.delivery-settings.update'));
};
</script>

<template>
    <AdminLayout>
        <template #header>Delivery Global Settings</template>

        <div class="max-w-2xl mx-auto py-12">
            <div class="bg-white rounded-[40px] p-10 shadow-sm border border-slate-100">
                <div class="mb-10">
                    <h3 class="text-3xl font-black text-[#222] tracking-tighter uppercase">Global Protocols</h3>
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mt-2">Configure delivery logic across the node</p>
                </div>

                <form @submit.prevent="submit" class="space-y-8">
                    <div class="space-y-3 group">
                        <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 flex items-center gap-2">
                            Free Delivery Threshold (EGP)
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        </label>
                        <div class="relative">
                            <input v-model="form.free_delivery_over" type="number" step="0.01" class="w-full h-16 rounded-2xl bg-slate-50 border-transparent focus:bg-white focus:border-[#ff0033] focus:ring-0 font-black text-xl px-6 transition-all" placeholder="e.g. 500.00">
                            <div class="absolute right-6 top-1/2 -translate-y-1/2 text-slate-300 font-black">EGP</div>
                        </div>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Leave empty to disable auto-free delivery</p>
                    </div>

                    <div class="p-6 bg-slate-50 rounded-3xl border border-dashed border-slate-200 flex items-center justify-between">
                        <div>
                            <p class="text-xs font-black text-[#222] uppercase tracking-widest">Enable Express Channel</p>
                            <p class="text-[9px] font-bold text-slate-400 mt-1 uppercase tracking-wider">Allow users to select faster delivery paths</p>
                        </div>
                        <div @click="form.express_enabled = !form.express_enabled" class="w-14 h-8 rounded-full border-2 border-slate-200 relative cursor-pointer transition-all" :class="form.express_enabled ? 'bg-[#ff0033] border-[#ff0033]' : 'bg-white'">
                            <div class="absolute top-1 bottom-1 w-5 bg-white rounded-full shadow-sm transition-all" :class="form.express_enabled ? 'right-1' : 'left-1'"></div>
                        </div>
                    </div>

                    <button :disabled="form.processing" type="submit" class="w-full h-16 bg-[#ff0033] text-white rounded-2xl font-black uppercase tracking-widest text-xs shadow-xl shadow-red-500/20 active:scale-95 transition-all">
                        {{ form.processing ? 'Updating Cloud...' : 'Save Configuration' }}
                    </button>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
