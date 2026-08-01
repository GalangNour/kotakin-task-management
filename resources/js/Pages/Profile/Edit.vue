<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>
        <template #header>Profile</template>

        <div class="mx-auto max-w-3xl space-y-6">
            <div class="rounded-card border border-border bg-white p-6 shadow-card sm:p-8">
                <UpdateProfileInformationForm
                    :must-verify-email="mustVerifyEmail"
                    :status="status"
                />
            </div>

            <div class="rounded-card border border-border bg-white p-6 shadow-card sm:p-8">
                <UpdatePasswordForm />
            </div>

            <div class="rounded-card border border-border bg-white p-6 shadow-card sm:p-8">
                <DeleteUserForm />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
