<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import AppButton from '@/Components/AppButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Forgot Password" />

        <h1 class="mb-3 font-display text-xl font-semibold tracking-tight text-ink">Lupa password?</h1>

        <div class="mb-5 text-sm text-secondary">
            Tidak masalah. Masukkan email Anda dan kami akan mengirimkan link untuk membuat password baru.
        </div>

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

            <div class="flex items-center justify-end pt-2">
                <AppButton
                    type="submit"
                    :class="{ 'opacity-60': form.processing }"
                    :disabled="form.processing"
                >
                    Email Password Reset Link
                </AppButton>
            </div>
        </form>
    </GuestLayout>
</template>
