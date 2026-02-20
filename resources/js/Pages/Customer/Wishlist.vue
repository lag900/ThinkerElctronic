<script setup>
import CustomerLayout from '@/Layouts/CustomerLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Wishlist" />
    <CustomerLayout>
        <div class="text-center py-20">
            <h3 class="text-xl font-black text-slate-900 mb-2 uppercase tracking-tight">Saved Frequencies</h3>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Your wishlist is currently empty</p>
        </div>
    </CustomerLayout>
</template>
