<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen flex flex-col items-center justify-center bg-[#FDFDFD] p-6">
        <div class="w-full max-w-md">
            <div class="flex flex-col items-center mb-10">
                <Link href="/">
                    <img src="/images/logo.png" class="h-20" alt="Thinker Logo">
                </Link>
                <h1 class="text-xs font-black tracking-[0.4em] text-[#222] uppercase mt-6">Secure Access</h1>
            </div>

            <div class="bg-white p-10 rounded-[40px] shadow-[0_20px_60px_rgba(0,0,0,0.03)] border border-slate-50">
                <slot />
            </div>

            <div class="mt-8 text-center">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-300">© 2026 Thinker Electronic Systems</p>
            </div>
        </div>
    </div>
</template>
