<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
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

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Kategori Project
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <TextInput
                        v-model="search"
                        type="text"
                        placeholder="Cari kategori..."
                        class="w-64"
                        @keyup.enter="applySearch"
                    />
                    <Link :href="route('project-categories.create')">
                        <PrimaryButton>Tambah Kategori</PrimaryButton>
                    </Link>
                </div>

                <div class="overflow-x-auto rounded-lg bg-white shadow dark:bg-gray-800">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b border-gray-200 text-xs uppercase text-gray-500 dark:border-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-4 py-3">Nama</th>
                                <th class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr v-for="category in categories.data" :key="category.id">
                                <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-200">
                                    {{ category.name }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <Link
                                        :href="route('project-categories.edit', category.id)"
                                        class="me-3 text-indigo-600 hover:underline dark:text-indigo-400"
                                    >
                                        Edit
                                    </Link>
                                    <button
                                        @click="destroy(category)"
                                        class="text-red-600 hover:underline dark:text-red-400"
                                    >
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="categories.data.length === 0">
                                <td colspan="2" class="px-4 py-6 text-center text-gray-400">
                                    Belum ada kategori.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <Pagination :links="categories.links" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
