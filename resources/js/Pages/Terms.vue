<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import Navbar from '@/Components/Thinker/Navbar.vue';
import CartModal from '@/Components/Thinker/CartModal.vue';
import { cart } from '@/Stores/CartStore';

const isCartOpen = ref(false);
const toggleCart = () => isCartOpen.value = !isCartOpen.value;
</script>

<template>
    <Head title="Terms of Service - Thinker" />
    
    <div class="bg-white min-h-screen font-sans selection:bg-red-600 selection:text-white">
        <Navbar @open-cart="toggleCart" />

        <section class="pt-32 pb-16 bg-black text-white relative overflow-hidden">
            <div class="absolute inset-0 opacity-20">
                <img src="/images/hero_bg.png" alt="Tech" class="w-full h-full object-cover grayscale" />
            </div>
            <div class="max-w-7xl mx-auto px-4 relative z-10 text-center">
                <h1 class="text-5xl md:text-7xl font-black tracking-tighter uppercase mb-4">
                    Terms of <span class="text-red-600 italic">Service</span>
                </h1>
                <p class="text-gray-400 text-xl">Please read these terms carefully before using our services</p>
            </div>
        </section>

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="prose prose-lg max-w-none space-y-8 text-gray-700">
                <p class="text-gray-500 text-sm">Last updated: February 14, 2026</p>

                <section>
                    <h2 class="text-2xl font-black text-black uppercase tracking-tight mb-4">1. Acceptance of Terms</h2>
                    <p>By accessing or using Thinker's website, products, or services, you agree to be bound by these Terms of Service. If you do not agree to these terms, please do not use our services.</p>
                </section>

                <section>
                    <h2 class="text-2xl font-black text-black uppercase tracking-tight mb-4">2. Use of Services</h2>
                    <p>You agree to use our services only for lawful purposes and in accordance with these terms. You must not use our services in any way that violates applicable laws, infringes on the rights of others, or could harm our systems or other users.</p>
                </section>

                <section>
                    <h2 class="text-2xl font-black text-black uppercase tracking-tight mb-4">3. Products and Orders</h2>
                    <p>All product descriptions, pricing, and availability are subject to change without notice. We reserve the right to limit quantities and refuse or cancel orders at our discretion. Orders are subject to acceptance and availability.</p>
                </section>

                <section>
                    <h2 class="text-2xl font-black text-black uppercase tracking-tight mb-4">4. Intellectual Property</h2>
                    <p>All content on this website, including text, graphics, logos, images, and software, is the property of Thinker or its licensors and is protected by intellectual property laws. You may not reproduce, distribute, or create derivative works without our written permission.</p>
                </section>

                <section>
                    <h2 class="text-2xl font-black text-black uppercase tracking-tight mb-4">5. Account Responsibility</h2>
                    <p>If you create an account, you are responsible for maintaining the confidentiality of your credentials and for all activities that occur under your account. You must notify us immediately of any unauthorized use.</p>
                </section>

                <section>
                    <h2 class="text-2xl font-black text-black uppercase tracking-tight mb-4">6. Limitation of Liability</h2>
                    <p>Thinker shall not be liable for any indirect, incidental, special, consequential, or punitive damages arising from your use of our services. Our total liability shall not exceed the amount you paid for the relevant product or service.</p>
                </section>

                <section>
                    <h2 class="text-2xl font-black text-black uppercase tracking-tight mb-4">7. Changes to Terms</h2>
                    <p>We may modify these terms at any time. Continued use of our services after changes constitutes acceptance of the new terms. We encourage you to review this page periodically.</p>
                </section>

                <section>
                    <h2 class="text-2xl font-black text-black uppercase tracking-tight mb-4">8. Contact</h2>
                    <p>For questions about these Terms of Service, please contact us through our website.</p>
                </section>
            </div>

            <div class="mt-12 pt-8 border-t border-gray-100">
                <Link :href="route('home')" class="inline-flex items-center text-red-600 font-bold hover:text-black transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Home
                </Link>
            </div>
        </div>

        <footer class="bg-white border-t border-gray-100 py-12 mt-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row justify-between items-center space-y-6 md:space-y-0 text-center md:text-left">
                    <div class="flex items-center space-x-2">
                        <img src="/images/logo.png" class="w-8 h-8 object-contain" alt="Thinker" />
                        <span class="text-xl font-black tracking-tighter uppercase">Thinker</span>
                    </div>
                    <div class="flex flex-wrap justify-center gap-6 text-sm">
                        <Link :href="route('privacy')" class="text-gray-500 hover:text-red-600 transition-colors">Privacy Policy</Link>
                        <Link :href="route('terms')" class="text-red-600 font-bold">Terms of Service</Link>
                        <Link :href="route('refund-policy')" class="text-gray-500 hover:text-red-600 transition-colors">Refund Policy</Link>
                        <Link :href="route('contact')" class="text-gray-500 hover:text-red-600 transition-colors">Contact</Link>
                    </div>
                    <p class="text-gray-400 text-sm">&copy; 2026 Thinker Inc.</p>
                </div>
            </div>
        </footer>

        <CartModal :is-open="isCartOpen" @close="toggleCart" />
    </div>
</template>
