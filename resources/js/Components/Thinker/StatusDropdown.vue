<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { cart } from '@/Stores/CartStore';

const props = defineProps({
    modelValue: String,
    options: {
        type: Array,
        default: () => [
            { value: 'pending', label: 'Pending' },
            { value: 'processing', label: 'Processing' },
            { value: 'completed', label: 'Completed' },
            { value: 'failed', label: 'Failed' },
        ]
    },
    disabled: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['update:modelValue', 'change']);

const isOpen = ref(false);
const dropdownRef = ref(null);

const currentOption = computed(() => {
    return props.options.find(o => o.value === props.modelValue) || props.options[0];
});

const selectOption = (option) => {
    emit('update:modelValue', option.value);
    emit('change', option.value);
    isOpen.value = false;
};

const handleClickOutside = (event) => {
    if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
        isOpen.value = false;
    }
};

onMounted(() => {
    window.addEventListener('mousedown', handleClickOutside);
});

onUnmounted(() => {
    window.removeEventListener('mousedown', handleClickOutside);
});
</script>

<template>
    <div ref="dropdownRef" class="relative inline-block font-inter">
        <!-- Minimal Button -->
        <button
            @click="isOpen = !isOpen"
            :disabled="disabled"
            class="group relative flex items-center justify-between gap-2.5 px-4 py-2 rounded-[12px] text-[13px] font-semibold transition-all duration-200 border"
            :class="[
                isOpen ? 'bg-white border-gray-300 shadow-sm' : 'bg-gray-50/50 border-gray-100 hover:border-gray-300 hover:bg-white',
                disabled ? 'opacity-50 pointer-events-none' : 'text-gray-700'
            ]"
        >
            <div class="flex items-center gap-2">
                <div class="w-1.5 h-1.5 rounded-full" :class="currentOption.color || 'bg-gray-400'"></div>
                <span class="tracking-tight">{{ currentOption.label }}</span>
            </div>
            
            <!-- Dropdown Arrow Icon -->
            <svg 
                class="w-3.5 h-3.5 text-gray-400 transition-transform duration-200"
                :class="{ 'rotate-180': isOpen }"
                fill="none" 
                viewBox="0 0 24 24" 
                stroke="currentColor"
                stroke-width="2.5"
            >
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <!-- Dropdown Menu -->
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0 translate-y-2 scale-95"
            enter-to-class="opacity-100 translate-y-0 scale-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100 translate-y-0 scale-100"
            leave-to-class="opacity-0 translate-y-2 scale-95"
        >
            <div 
                v-if="isOpen" 
                class="absolute z-[100] mt-2 w-48 bg-white rounded-[12px] shadow-[0_20px_40px_rgba(0,0,0,0.08)] border border-gray-100 p-1.5 overflow-hidden"
                :class="cart.language === 'ar' ? 'right-0' : 'left-0'"
            >
                <div class="space-y-1">
                    <button
                        v-for="option in options"
                        :key="option.value"
                        @click="selectOption(option)"
                        class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-[8px] text-[13px] font-medium transition-all duration-200"
                        :class="[
                            modelValue === option.value 
                                ? 'bg-gray-50 text-gray-900' 
                                : 'text-gray-500 hover:bg-gray-50/80 hover:text-gray-900',
                            cart.language === 'ar' ? 'text-right flex-row-reverse' : 'text-left'
                        ]"
                    >
                        <div class="flex items-center gap-2.5">
                            <div class="w-1.5 h-1.5 rounded-full" :class="option.color || 'bg-gray-400'"></div>
                            <span>{{ option.label }}</span>
                        </div>
                        
                        <!-- Check Icon for Selected Item -->
                        <svg 
                            v-if="modelValue === option.value"
                            class="w-3.5 h-3.5 text-gray-900"
                            fill="none" 
                            viewBox="0 0 24 24" 
                            stroke="currentColor"
                            stroke-width="3"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </button>
                </div>
            </div>
        </Transition>
    </div>
</template>

<style scoped>
.font-inter {
    font-family: 'Inter', sans-serif;
}
</style>
