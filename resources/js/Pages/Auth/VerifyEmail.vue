<script setup>
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import AppButton from '@/Components/AppButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);
</script>

<template>
    <GuestLayout>
        <Head title="Email Verification" />

        <div class="mb-4 text-sm text-secondary">
            Thanks for signing up! Before getting started, could you verify your
            email address by clicking on the link we just emailed to you? If you
            didn't receive the email, we will gladly send you another.
        </div>

        <div v-if="verificationLinkSent" class="mb-4 rounded-control bg-success-tint px-3 py-2 text-sm font-semibold text-success">
            A new verification link has been sent to the email address you
            provided during registration.
        </div>

        <form @submit.prevent="submit">
            <div class="flex items-center justify-between">
                <AppButton
                    type="submit"
                    :class="{ 'opacity-60': form.processing }"
                    :disabled="form.processing"
                >
                    Resend Verification Email
                </AppButton>

                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="rounded-control text-sm font-semibold text-secondary underline decoration-border underline-offset-2 transition-colors duration-150 ease-out hover:text-accent focus:outline-none focus:ring-1 focus:ring-accent"
                    >Log Out</Link
                >
            </div>
        </form>
    </GuestLayout>
</template>
