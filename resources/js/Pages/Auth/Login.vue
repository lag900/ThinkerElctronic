<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

import { ref } from 'vue';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const loginForm = useForm({
    email: '',
    password: '',
    remember: false,
});

const submitLogin = () => {
    loginForm.post(route('login'), {
        onFinish: () => loginForm.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log In or Register" />

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <div class="mb-8 text-center space-y-2">
            <h2 class="text-3xl font-black text-[#222] tracking-tighter uppercase">Welcome</h2>
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Sign in to your account. Or enter your email to automatically create one.</p>
        </div>

        <form @submit.prevent="submitLogin" class="space-y-6">
            <div>
                <InputLabel for="login_email" value="Email Adddress" />
                <TextInput
                    id="login_email"
                    type="email"
                    class="mt-1 block w-full py-3"
                    v-model="loginForm.email"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="name@example.com"
                />
                <InputError class="mt-2" :message="loginForm.errors.email" />
            </div>

            <div>
                <InputLabel for="login_password" value="Password" />
                <TextInput
                    id="login_password"
                    type="password"
                    class="mt-1 block w-full py-3"
                    v-model="loginForm.password"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••"
                />
                <InputError class="mt-2" :message="loginForm.errors.password" />
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center cursor-pointer group">
                    <Checkbox name="remember" v-model:checked="loginForm.remember" class="group-hover:ring-brand-500/20" />
                    <span class="ms-2 text-xs font-bold text-gray-500 group-hover:text-black transition-colors">Remember me</span>
                </label>

                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-xs font-bold text-[#ff2d55] hover:underline"
                >
                    Forgot password?
                </Link>
            </div>

            <div class="pt-4">
                <PrimaryButton
                    class="w-full justify-center py-4 text-[11px] font-black uppercase tracking-widest bg-black text-white hover:bg-brand-500 hover:shadow-lg hover:shadow-brand-500/20 transition-all rounded-2xl"
                    :class="{ 'opacity-50 pointer-events-none': loginForm.processing }"
                    :disabled="loginForm.processing"
                >
                    <span v-if="loginForm.processing" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Authenticating...
                    </span>
                    <span v-else>Continue / Login</span>
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
