<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Badge from '@/Components/Badge.vue';
import AppButton from '@/Components/AppButton.vue';
import AuditTrail from '@/Components/AuditTrail.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    project: Object,
    audits: Array,
});
</script>

<template>
    <Head :title="`Audit Trail — ${project.name}`" />

    <AuthenticatedLayout eyebrow="Project Terhapus" :title="project.name">
        <template #actions>
            <AppButton variant="secondary" :href="route('projects.trashed')">Kembali</AppButton>
        </template>

        <div class="mb-5 flex items-center gap-3">
            <Badge label="Terhapus" tone="danger" />
            <span class="text-[13px] text-secondary">{{ project.category?.name ?? 'Tanpa Kategori' }}</span>
            <span class="text-[13px] text-secondary">&middot; Dibuat oleh {{ project.creator?.name ?? '-' }}</span>
        </div>

        <div class="max-w-[640px] rounded-card border border-border bg-white p-6">
            <AuditTrail :audits="audits" />
        </div>
    </AuthenticatedLayout>
</template>
