<script setup>
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { cart } from '@/Stores/CartStore';

const showingNavigationDropdown = ref(false);
const page = usePage();
</script>

<template>
    <div class="min-h-screen bg-[#FDFDFD] font-sans text-[#222]">
        <nav class="bg-white/90 backdrop-blur-xl border-b border-slate-100 sticky top-0 z-50 h-20 flex items-center">
            <!-- Primary Navigation Menu -->
            <div class="mx-auto max-w-7xl px-8 w-full flex justify-between items-center">
                <div class="flex items-center gap-12">
                    <!-- Logo -->
                    <Link href="/" class="shrink-0 flex items-center gap-3">
                        <img src="/images/logo.png" class="h-12" alt="Logo">
                    </Link>

                    <!-- Navigation Links -->
                    <div class="hidden lg:flex items-center gap-10">
                        <Link 
                            href="/"
                            class="text-[10px] font-black uppercase tracking-[0.3em] hover:text-brand-500 transition-all"
                            :class="route().current('home') ? 'text-brand-500' : 'text-slate-400'"
                        >
                            {{ cart.t('home') }}
                        </Link>
                        <Link 
                            v-if="$page.props.auth.user.role === 'admin' || $page.props.auth.user.email.endsWith('@thinker.com')"
                            :href="route('dashboard')"
                            class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400 hover:text-brand-500 transition-all"
                        >
                            Admin Node
                        </Link>
                    </div>
                </div>

                <div class="flex items-center gap-8">
                    <Link 
                        :href="route('profile.edit')"
                        class="text-[10px] font-black uppercase tracking-[0.3em] transition-all"
                        :class="route().current('profile.edit') ? 'text-brand-500' : 'text-slate-400 hover:text-[#222]'"
                    >
                        {{ cart.t('account') }}
                    </Link>
                    <Link 
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-300 hover:text-red-500 transition-all"
                    >
                        {{ cart.t('logout') }}
                    </Link>
                </div>
            </div>
        </nav>

        <!-- Page Heading -->
        <header class="bg-white border-b border-slate-50" v-if="$slots.header">
            <div class="mx-auto max-w-7xl px-8 py-10">
                <slot name="header" />
            </div>
        </header>

        <!-- Page Content -->
        <main class="mx-auto max-w-7xl px-8 py-12">
            <slot />
        </main>
    </div>
</template>
