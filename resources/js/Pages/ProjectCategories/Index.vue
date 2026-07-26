<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import AppButton from '@/Components/AppButton.vue';
import SearchInput from '@/Components/SearchInput.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    categories: Object,
    filters: Object,
});

const search = ref(props.filters.search ?? '');

const applySearch = () => {
    router.get(
        route('project-categories.index'),
        { search: search.value || undefined },
        { preserveState: true, replace: true },
    );
};

const destroy = (category) => {
    if (confirm(`Hapus kategori "${category.name}"?`)) {
        router.delete(route('project-categories.destroy', category.id));
    }
};
</script>

<template>
    <Head title="Kategori Project" />

    <AuthenticatedLayout eyebrow="Kelola" title="Kategori Project">
        <template #actions>
            <SearchInput v-model="search" placeholder="Cari kategori..." @keyup.enter="applySearch" />
            <AppButton variant="primary" :href="route('project-categories.create')">+ Tambah Kategori</AppButton>
        </template>

        <div class="overflow-hidden rounded-card border border-border bg-white">
            <div
                v-for="category in categories.data"
                :key="category.id"
                class="flex items-center justify-between border-b border-divider px-5 py-3.5 last:border-b-0"
            >
                <span class="text-sm font-bold">{{ category.name }}</span>
                <div class="flex gap-3.5 text-[13px] font-semibold">
                    <Link :href="route('project-categories.edit', category.id)" class="text-ink hover:text-accent">Edit</Link>
                    <button @click="destroy(category)" class="text-danger hover:underline">Hapus</button>
                </div>
            </div>

            <p v-if="categories.data.length === 0" class="py-10 text-center text-sm text-secondary">
                Belum ada kategori.
            </p>
        </div>

        <Pagination :links="categories.links" />
    </AuthenticatedLayout>
</template>
