<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import AppButton from '@/Components/AppButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <h1 class="mb-6 font-display text-xl font-semibold tracking-tight text-ink">Masuk ke akun Anda</h1>

        <div v-if="status" class="mb-4 rounded-control bg-success-tint px-3 py-2 text-sm font-semibold text-success">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1.5"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <InputLabel for="password" value="Password" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1.5"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="block">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ms-2 text-sm text-secondary">Remember me</span>
                </label>
            </div>

            <div class="flex items-center justify-between pt-2">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="rounded-control text-sm font-semibold text-secondary underline decoration-border underline-offset-2 transition-colors duration-150 ease-out hover:text-accent focus:outline-none focus:ring-1 focus:ring-accent"
                >
                    Forgot your password?
                </Link>

                <AppButton
                    type="submit"
                    class="ms-auto"
                    :class="{ 'opacity-60': form.processing }"
                    :disabled="form.processing"
                >
                    Log in
                </AppButton>
            </div>
        </form>
    </GuestLayout>
</template>
