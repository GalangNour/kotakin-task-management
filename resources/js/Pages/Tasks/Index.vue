<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    project: Object,
    tasks: Object,
    filters: Object,
});

const search = ref(props.filters.search ?? '');

const applySearch = () => {
    router.get(
        route('projects.tasks.index', props.project.id),
        { search: search.value || undefined },
        { preserveState: true, replace: true },
    );
};

const destroy = (task) => {
    if (confirm(`Hapus task "${task.title}"?`)) {
        router.delete(route('tasks.destroy', task.id));
    }
};

const statusBadge = {
    todo: 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
    in_progress: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
    done: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
};

const priorityBadge = {
    low: 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
    medium: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
    high: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
};
</script>

<template>
    <Head :title="`Task — ${project.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Task — {{ project.name }}
                </h2>
                <Link :href="route('projects.index')">
                    <SecondaryButton>Kembali ke Projects</SecondaryButton>
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <TextInput
                        v-model="search"
                        type="text"
                        placeholder="Cari task..."
                        class="w-64"
                        @keyup.enter="applySearch"
                    />
                    <div class="flex gap-2">
                        <Link :href="route('projects.tasks.import.create', project.id)">
                            <SecondaryButton>Import</SecondaryButton>
                        </Link>
                        <Link :href="route('projects.tasks.export.create', project.id)">
                            <SecondaryButton>Export</SecondaryButton>
                        </Link>
                        <Link :href="route('projects.tasks.create', project.id)">
                            <PrimaryButton>Tambah Task</PrimaryButton>
                        </Link>
                    </div>
                </div>

                <div class="overflow-x-auto rounded-lg bg-white shadow dark:bg-gray-800">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b border-gray-200 text-xs uppercase text-gray-500 dark:border-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-4 py-3">Judul</th>
                                <th class="px-4 py-3">Assignee</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Prioritas</th>
                                <th class="px-4 py-3">Due Date</th>
                                <th class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr v-for="task in tasks.data" :key="task.id">
                                <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-200">
                                    <Link :href="route('tasks.edit', task.id)" class="hover:underline">
                                        {{ task.title }}
                                    </Link>
                                </td>
                                <td class="px-4 py-3 text-gray-500 dark:text-gray-400">
                                    {{ task.assignee?.name ?? '-' }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="rounded-full px-2 py-0.5 text-xs font-semibold" :class="statusBadge[task.status]">
                                        {{ task.status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="rounded-full px-2 py-0.5 text-xs font-semibold" :class="priorityBadge[task.priority]">
                                        {{ task.priority }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-500 dark:text-gray-400">
                                    {{ task.due_date ? task.due_date.slice(0, 10) : '-' }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <Link
                                        :href="route('tasks.edit', task.id)"
                                        class="me-3 text-indigo-600 hover:underline dark:text-indigo-400"
                                    >
                                        Edit
                                    </Link>
                                    <button
                                        @click="destroy(task)"
                                        class="text-red-600 hover:underline dark:text-red-400"
                                    >
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="tasks.data.length === 0">
                                <td colspan="6" class="px-4 py-6 text-center text-gray-400">
                                    Belum ada task.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <Pagination :links="tasks.links" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
