<script setup>
import { computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ProjectDetailTabs from '@/Components/ProjectDetailTabs.vue';
import Badge from '@/Components/Badge.vue';
import Avatar from '@/Components/Avatar.vue';
import AppButton from '@/Components/AppButton.vue';
import SearchInput from '@/Components/SearchInput.vue';
import { useLiveSearch } from '@/composables/useLiveSearch';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    project: Object,
    tasks: Array,
    filters: Object,
});

const search = useLiveSearch(route('projects.tasks.index', props.project.id), props.filters.search);

const COLUMN_DEFS = [
    { key: 'todo', label: 'To Do' },
    { key: 'in_progress', label: 'In Progress' },
    { key: 'done', label: 'Selesai' },
];

const columns = computed(() =>
    COLUMN_DEFS.map((col) => ({
        ...col,
        tasks: props.tasks.filter((t) => t.status === col.key),
    })),
);

const priorityTone = { low: 'neutral', medium: 'warning', high: 'danger' };
const priorityLabel = { low: 'Low', medium: 'Medium', high: 'High' };

const isOverdue = (task) => task.due_date && new Date(task.due_date) < new Date() && task.status !== 'done';

const dueLabel = (task) => {
    if (!task.due_date) return '-';
    return new Date(task.due_date).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
};
</script>

<template>
    <Head :title="`Task — ${project.name}`" />

    <AuthenticatedLayout eyebrow="Project" title="Task">
        <template #actions>
            <SearchInput v-model="search" placeholder="Cari task..." />
        </template>

        <ProjectDetailTabs :project="project" active="tasks">
            <template #action>
                <div class="flex gap-2">
                    <AppButton variant="secondary" :href="route('projects.tasks.import.create', project.id)">Import</AppButton>
                    <AppButton variant="secondary" :href="route('projects.tasks.export.create', project.id)">Export</AppButton>
                    <AppButton variant="primary" :href="route('projects.tasks.create', project.id)">+ Tambah Task</AppButton>
                </div>
            </template>
        </ProjectDetailTabs>

        <div class="grid grid-cols-3 gap-4">
            <div v-for="col in columns" :key="col.key">
                <div class="mb-3 flex items-center gap-2">
                    <span class="text-[13px] font-extrabold">{{ col.label }}</span>
                    <span class="rounded-full bg-neutral-tint px-2 py-0.5 text-[11px] font-bold text-secondary">
                        {{ col.tasks.length }}
                    </span>
                </div>

                <div class="flex flex-col gap-2.5">
                    <Link
                        v-for="task in col.tasks"
                        :key="task.id"
                        :href="route('tasks.edit', task.id)"
                        class="block rounded-card-sm border border-border bg-white p-3.5 transition hover:border-accent/40"
                    >
                        <div class="mb-2.5 text-[13px] font-bold">{{ task.title }}</div>
                        <div class="flex items-center justify-between">
                            <Badge :label="priorityLabel[task.priority]" :tone="priorityTone[task.priority]" />
                            <span
                                class="text-[11px] font-semibold"
                                :class="isOverdue(task) ? 'text-danger' : 'text-secondary'"
                            >
                                {{ dueLabel(task) }}
                            </span>
                        </div>
                        <div class="mt-2.5 flex items-center gap-1.5 border-t border-divider pt-2.5">
                            <Avatar :name="task.assignee?.name ?? '-'" :size="20" />
                            <span class="text-xs text-secondary">{{ task.assignee?.name ?? 'Belum ditugaskan' }}</span>
                        </div>
                    </Link>

                    <p v-if="col.tasks.length === 0" class="py-4 text-center text-xs text-secondary">
                        Tidak ada task.
                    </p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
