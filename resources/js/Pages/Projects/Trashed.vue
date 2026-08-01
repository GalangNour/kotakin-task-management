<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import Badge from '@/Components/Badge.vue';
import Avatar from '@/Components/Avatar.vue';
import AppButton from '@/Components/AppButton.vue';
import SearchInput from '@/Components/SearchInput.vue';
import { useLiveSearch } from '@/composables/useLiveSearch';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    projects: Object,
    filters: Object,
});

const search = useLiveSearch(route('projects.trashed'), props.filters.search);

const formatDateTime = (value) => {
    if (!value) return '-';
    return new Date(value).toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
};
</script>

<template>
    <Head title="Project Terhapus" />

    <AuthenticatedLayout eyebrow="Kelola" title="Project Terhapus">
        <template #actions>
            <SearchInput v-model="search" placeholder="Cari project..." />
            <AppButton variant="secondary" :href="route('projects.index')">Kembali ke Projects</AppButton>
        </template>

        <div class="overflow-hidden rounded-card border border-border bg-white">
            <div
                v-for="project in projects.data"
                :key="project.id"
                class="flex items-center justify-between border-b border-divider px-5 py-3.5 last:border-b-0"
            >
                <div class="flex items-center gap-3">
                    <Avatar :name="project.creator?.name ?? '-'" :size="28" />
                    <div>
                        <div class="text-[13px] font-bold">{{ project.name }}</div>
                        <div class="text-xs text-secondary">
                            Dihapus {{ formatDateTime(project.deleted_at) }} &middot; {{ project.category?.name ?? 'Tanpa Kategori' }}
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <Badge label="Terhapus" tone="danger" />
                    <Link :href="route('projects.trashed.show', project.id)" class="text-[13px] font-semibold text-accent hover:text-accent-dark">
                        Lihat Audit Trail
                    </Link>
                </div>
            </div>

            <p v-if="projects.data.length === 0" class="py-10 text-center text-sm text-secondary">
                Tidak ada project yang dihapus.
            </p>
        </div>

        <Pagination :links="projects.links" />
    </AuthenticatedLayout>
</template>
