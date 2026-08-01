<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import AppButton from '@/Components/AppButton.vue';
import SearchInput from '@/Components/SearchInput.vue';
import { useLiveSearch } from '@/composables/useLiveSearch';
import { useConfirm } from '@/composables/useConfirm';
import { Head, Link, router } from '@inertiajs/vue3';
import { Plus, Pencil, Trash2 } from '@lucide/vue';

const props = defineProps({
    categories: Object,
    filters: Object,
});

const search = useLiveSearch(route('project-categories.index'), props.filters.search);
const { askConfirm } = useConfirm();

const destroy = (category) => {
    askConfirm(`Hapus kategori "${category.name}"?`, () => {
        router.delete(route('project-categories.destroy', category.id));
    });
};
</script>

<template>
    <Head title="Kategori Project" />

    <AuthenticatedLayout eyebrow="Kelola" title="Kategori Project">
        <template #actions>
            <SearchInput v-model="search" placeholder="Cari kategori..." />
            <AppButton variant="primary" :href="route('project-categories.create')">
                <Plus :size="14" :stroke-width="2.5" /> Tambah Kategori
            </AppButton>
        </template>

        <div class="overflow-hidden rounded-card border border-border bg-white">
            <div
                v-for="category in categories.data"
                :key="category.id"
                class="flex items-center justify-between border-b border-divider px-5 py-3.5 last:border-b-0"
            >
                <span class="text-sm font-bold">{{ category.name }}</span>
                <div class="flex gap-3.5 text-[13px] font-semibold">
                    <Link :href="route('project-categories.edit', category.id)" class="flex items-center gap-1 text-ink transition-colors duration-150 ease-out hover:text-accent">
                        <Pencil :size="12" :stroke-width="2.25" /> Edit
                    </Link>
                    <button @click="destroy(category)" class="flex items-center gap-1 text-danger hover:underline">
                        <Trash2 :size="12" :stroke-width="2.25" /> Hapus
                    </button>
                </div>
            </div>

            <p v-if="categories.data.length === 0" class="py-10 text-center text-sm text-secondary">
                Belum ada kategori.
            </p>
        </div>

        <Pagination :links="categories.links" />
    </AuthenticatedLayout>
</template>
