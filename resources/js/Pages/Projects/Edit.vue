<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ProjectDetailTabs from '@/Components/ProjectDetailTabs.vue';
import AuditTrail from '@/Components/AuditTrail.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    project: Object,
    categories: Array,
    audits: Array,
});
</script>

<template>
    <Head :title="`History — ${project.name}`" />

    <AuthenticatedLayout eyebrow="Project" title="Detail">
        <ProjectDetailTabs :project="project" :categories="categories" active="history" />

        <div class="max-w-[640px] rounded-card border border-border bg-white p-6">
            <AuditTrail :audits="audits" />
        </div>
    </AuthenticatedLayout>
</template>
