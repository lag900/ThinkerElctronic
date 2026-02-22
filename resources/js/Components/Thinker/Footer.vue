<script setup>
import { Link } from '@inertiajs/vue3';
import { cart } from '@/Stores/CartStore';

const currentYear = new Date().getFullYear();

const footerLinks = [
    { name: 'privacyPolicy', route: 'privacy' },
    { name: 'termsOfService', route: 'terms' },
    { name: 'refundPolicy', route: 'refund-policy' },
    { name: 'contactInfo', route: 'contact' },
];
</script>

<template>
    <footer class="bg-white border-t border-gray-100 pt-24 pb-12" :dir="cart.language === 'ar' ? 'rtl' : 'ltr'">
        <div class="max-w-7xl mx-auto px-6 sm:px-8">
            <div class="flex flex-col lg:flex-row justify-between gap-16 lg:gap-24">
                <!-- Branding Left Wing -->
                <div class="max-w-md space-y-8">
                    <Link href="/" class="flex items-center gap-3 group">
                        <img src="/images/logo.png" class="h-14 w-auto transition-all duration-500 group-hover:scale-105 group-hover:rotate-3">
                        <div class="flex flex-col">
                            <span class="text-2xl font-black tracking-tighter text-black uppercase leading-none">Thinker</span>
                            <span class="text-[9px] font-black text-brand-500 uppercase mt-1 tracking-[0.4em] leading-none">Intelligence Hub</span>
                        </div>
                    </Link>
                    <p class="text-[15px] font-medium text-gray-400 leading-relaxed">
                        {{ cart.language === 'ar' 
                            ? 'نحن نبتكر في دمج الذكاء الاصطناعي مع الأنظمة المادية، لنبني غداً أذكى وأكثر كفاءة لكل مستخدم في النظام البيئي الرقمي.' 
                            : 'Architecting the convergence of intelligence and physical systems. We build high-performance hardware nodes for the modern digital ecosystem.' 
                        }}
                    </p>
                </div>

                <!-- Navigation Right Wing -->
                <div class="flex flex-col sm:flex-row gap-16 lg:gap-24">
                    <div class="space-y-8 min-w-[200px]">
                        <h4 class="text-[11px] font-black uppercase tracking-[0.3em] text-black border-l-4 border-brand-500 pl-4 py-1" :class="cart.language === 'ar' ? 'border-l-0 border-r-4 pl-0 pr-4' : ''">
                            {{ cart.language === 'ar' ? 'روابط هامة' : 'Useful Links' }}
                        </h4>
                        <nav>
                            <ul class="space-y-4">
                                <li v-for="link in footerLinks" :key="link.route">
                                    <Link 
                                        :href="route(link.route)" 
                                        class="text-sm font-bold transition-all duration-300 flex items-center gap-3 group"
                                        :class="route().current(link.route) ? 'text-brand-500 transform translate-x-2' : 'text-gray-400 hover:text-black'"
                                    >
                                        <span class="w-1.5 h-1.5 rounded-full bg-brand-500 transition-all duration-300" :class="route().current(link.route) ? 'opacity-100 scale-100' : 'opacity-0 scale-0 group-hover:opacity-100 group-hover:scale-100'"></span>
                                        {{ cart.t(link.name) }}
                                    </Link>
                                </li>
                            </ul>
                        </nav>
                    </div>

                    <div class="space-y-8">
                        <h4 class="text-[11px] font-black uppercase tracking-[0.3em] text-black border-l-4 border-slate-200 pl-4 py-1" :class="cart.language === 'ar' ? 'border-l-0 border-r-4 pl-0 pr-4' : ''">
                            {{ cart.language === 'ar' ? 'الخدمات' : 'Ecosystem' }}
                        </h4>
                        <ul class="space-y-4">
                            <li><Link :href="route('shop')" class="text-sm font-bold text-gray-400 hover:text-black transition-all">{{ cart.t('shop') }}</Link></li>
                            <li><Link :href="route('lms-points')" class="text-sm font-bold text-gray-400 hover:text-black transition-all">{{ cart.t('lmsPoints') }}</Link></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Deployment Baseline -->
            <div class="mt-24 pt-10 border-t border-slate-50 flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="text-[11px] font-black uppercase tracking-[0.2em] text-gray-300 flex items-center gap-3">
                    <span class="text-brand-500">&copy;</span> {{ currentYear }} Thinker Micro-Systems. All Nodes Operating.
                </div>
                <div class="flex items-center gap-8">
                    <div class="flex items-center gap-3">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse shadow-[0_0_10px_rgba(16,185,129,0.5)]"></span>
                        <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Mainframe Stable</span>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</template>

<style scoped>
.translate-x-2 {
    transform: translateX(0.5rem);
}
[dir="rtl"] .translate-x-2 {
    transform: translateX(-0.5rem);
}
</style>
