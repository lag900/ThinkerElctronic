<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    modelValue: String,
    options: Array, // Array of { name_ar, name_en }
    labelAr: String,
    labelEn: String,
    placeholder: String,
    disabled: Boolean,
    language: String,
    error: String
});

const emit = defineEmits(['update:modelValue']);

const isOpen = ref(false);
const search = ref('');
const container = ref(null);

const filteredOptions = computed(() => {
    if (!search.value) return props.options;
    const s = search.value.toLowerCase();
    return props.options.filter(opt => 
        opt.name_ar.includes(s) || 
        opt.name_en.toLowerCase().includes(s)
    );
});

const selectedOption = computed(() => {
    return props.options.find(opt => opt.name_ar === props.modelValue || opt.name_en === props.modelValue);
});

const select = (opt) => {
    emit('update:modelValue', props.language === 'ar' ? opt.name_ar : opt.name_en);
    isOpen.value = false;
    search.value = '';
};

const handleClickOutside = (e) => {
    if (container.value && !container.value.contains(e.target)) {
        isOpen.value = false;
    }
};

onMounted(() => document.addEventListener('click', handleClickOutside));
onUnmounted(() => document.removeEventListener('click', handleClickOutside));
</script>

<template>
    <div ref="container" class="relative group">
        <label class="flex flex-col mb-2" :class="language === 'ar' ? 'text-right' : 'text-left font-black'">
            <span class="text-[17px] font-black text-ink-main leading-none">{{ labelAr }}</span>
            <span class="text-[12px] font-roboto text-ink-muted opacity-50 font-medium mt-1 leading-none uppercase tracking-wider">{{ labelEn }}</span>
        </label>
        
        <div 
            @click="!disabled && (isOpen = !isOpen)"
            class="w-full bg-white border rounded-2xl p-5 text-sm font-bold flex items-center justify-between cursor-pointer transition-all shadow-sm group-hover:shadow-md h-[64px]"
            :class="[
                isOpen ? 'ring-4 ring-brand-500/10 border-brand-500' : (error ? 'border-brand-500' : 'border-border/40'),
                disabled ? 'bg-surface-100 opacity-50 cursor-not-allowed' : ''
            ]"
        >
            <span v-if="selectedOption" class="text-ink-main">
                {{ language === 'ar' ? selectedOption.name_ar : selectedOption.name_en }}
            </span>
            <span v-else class="text-ink-muted opacity-40">{{ placeholder }}</span>
            
            <svg class="w-5 h-5 text-ink-muted transition-transform duration-300" :class="isOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </div>

        <div v-if="error" class="text-brand-500 text-[10px] font-black uppercase tracking-widest mt-2">{{ error }}</div>

        <!-- Dropdown Menu -->
        <div 
            v-if="isOpen" 
            class="absolute z-[100] top-full mt-2 w-full bg-white border border-border/30 rounded-3xl shadow-2xl overflow-hidden animate-in fade-in slide-in-from-top-2 duration-300"
        >
            <div class="p-4 border-b border-border/10 bg-surface-50">
                <input 
                    v-model="search"
                    @click.stop
                    type="text"
                    class="w-full bg-white border border-border/20 rounded-xl p-3 text-sm font-bold focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all outline-none"
                    :placeholder="language === 'ar' ? 'بحث...' : 'Search...'"
                    autofocus
                />
            </div>
            
            <div class="max-h-[250px] overflow-y-auto custom-scrollbar">
                <div 
                    v-for="opt in filteredOptions" 
                    :key="opt.name_en"
                    @click="select(opt)"
                    class="p-4 hover:bg-brand-50 cursor-pointer flex items-center justify-between transition-colors border-b border-border/5 last:border-0"
                    :class="modelValue === (language === 'ar' ? opt.name_ar : opt.name_en) ? 'bg-brand-50/50' : ''"
                >
                    <div class="flex flex-col" :class="language === 'ar' ? 'text-right w-full' : 'text-left w-full'">
                        <span class="text-sm font-black text-ink-main">{{ opt.name_ar }}</span>
                        <span class="text-[10px] font-roboto text-ink-muted opacity-60 uppercase font-bold">{{ opt.name_en }}</span>
                    </div>
                    <div v-if="modelValue === (language === 'ar' ? opt.name_ar : opt.name_en)" class="w-2 h-2 rounded-full bg-brand-500 shrink-0"></div>
                </div>
                
                <div v-if="filteredOptions.length === 0" class="p-8 text-center text-ink-muted text-xs font-bold uppercase tracking-widest opacity-40">
                    No results found
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #e5e7eb;
    border-radius: 10px;
}
</style>
