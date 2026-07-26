<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    projects: Object,
    filters: Object,
});

const search = ref(props.filters.search ?? '');

const applySearch = () => {
    router.get(
        route('projects.index'),
        { search: search.value || undefined },
        { preserveState: true, replace: true },
    );
};

const destroy = (project) => {
    if (confirm(`Hapus project "${project.name}"? Semua task di dalamnya ikut terhapus.`)) {
        router.delete(route('projects.destroy', project.id));
    }
};
</script>

<template>
    <Head title="Projects" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Projects
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <TextInput
                        v-model="search"
                        type="text"
                        placeholder="Cari project..."
                        class="w-64"
                        @keyup.enter="applySearch"
                    />
                    <div class="flex gap-2">
                        <Link :href="route('project-categories.index')">
                            <SecondaryButton>Kelola Kategori</SecondaryButton>
                        </Link>
                        <Link :href="route('projects.create')">
                            <PrimaryButton>Tambah Project</PrimaryButton>
                        </Link>
                    </div>
                </div>

                <div class="overflow-x-auto rounded-lg bg-white shadow dark:bg-gray-800">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b border-gray-200 text-xs uppercase text-gray-500 dark:border-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-4 py-3">Nama</th>
                                <th class="px-4 py-3">Kategori</th>
                                <th class="px-4 py-3">Dibuat oleh</th>
                                <th class="px-4 py-3">Task</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr v-for="project in projects.data" :key="project.id">
                                <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-200">
                                    <Link
                                        :href="route('projects.tasks.index', project.id)"
                                        class="hover:underline"
                                    >
                                        {{ project.name }}
                                    </Link>
                                </td>
                                <td class="px-4 py-3 text-gray-500 dark:text-gray-400">
                                    {{ project.category?.name ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-gray-500 dark:text-gray-400">
                                    {{ project.creator?.name ?? '-' }}
                                </td>
                                <td class="px-4 py-3">{{ project.tasks_count }}</td>
                                <td class="px-4 py-3">
                                    <span
                                        class="rounded-full px-2 py-0.5 text-xs font-semibold"
                                        :class="project.is_active
                                            ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                                            : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300'"
                                    >
                                        {{ project.is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <Link
                                        :href="route('projects.edit', project.id)"
                                        class="me-3 text-indigo-600 hover:underline dark:text-indigo-400"
                                    >
                                        Edit
                                    </Link>
                                    <button
                                        @click="destroy(project)"
                                        class="text-red-600 hover:underline dark:text-red-400"
                                    >
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="projects.data.length === 0">
                                <td colspan="6" class="px-4 py-6 text-center text-gray-400">
                                    Belum ada project.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <Pagination :links="projects.links" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
