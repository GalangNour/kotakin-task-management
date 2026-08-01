<script setup>
import { computed, ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ProjectDetailTabs from '@/Components/ProjectDetailTabs.vue';
import Badge from '@/Components/Badge.vue';
import Avatar from '@/Components/Avatar.vue';
import AppButton from '@/Components/AppButton.vue';
import SearchInput from '@/Components/SearchInput.vue';
import TaskModal from '@/Pages/Tasks/TaskModal.vue';
import { useLiveSearch } from '@/composables/useLiveSearch';
import { useToast } from '@/composables/useToast';
import { Head, router } from '@inertiajs/vue3';
import { Plus } from '@lucide/vue';

const props = defineProps({
    project: Object,
    tasks: Array,
    users: Array,
    categories: Array,
    filters: Object,
});

const search = useLiveSearch(route('projects.tasks.index', props.project.id), props.filters.search);
const { show: showToast } = useToast();

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

/* ---------- Task modal ---------- */

const taskModal = ref(null); // null | { mode, task, presetStatus }

const openCreateTask = (status = 'todo') => {
    taskModal.value = { mode: 'create', task: null, presetStatus: status };
};

const openEditTask = (task) => {
    taskModal.value = { mode: 'edit', task, presetStatus: task.status };
};

const closeTaskModal = () => {
    taskModal.value = null;
};

/* ---------- Kanban drag-and-drop ---------- */

const draggingTaskId = ref(null);
const dragOverColumnKey = ref(null);
const dragGhost = ref(null);

const onDragStart = (event, task) => {
    draggingTaskId.value = task.id;
    event.dataTransfer.effectAllowed = 'move';

    if (dragGhost.value) {
        dragGhost.value.textContent = task.title;
        event.dataTransfer.setDragImage(dragGhost.value, 14, 14);
    }
};

const onDragEnd = () => {
    draggingTaskId.value = null;
    dragOverColumnKey.value = null;
};

const onColumnDragLeave = (event) => {
    if (!event.currentTarget.contains(event.relatedTarget)) {
        dragOverColumnKey.value = null;
    }
};

const onDrop = (col) => {
    const task = props.tasks.find((t) => t.id === draggingTaskId.value);
    dragOverColumnKey.value = null;
    draggingTaskId.value = null;

    if (!task || task.status === col.key) return;

    router.patch(
        route('tasks.update', task.id),
        {
            title: task.title,
            description: task.description,
            assigned_to: task.assignee?.id ?? '',
            status: col.key,
            priority: task.priority,
            due_date: task.due_date ? task.due_date.slice(0, 10) : '',
            is_completed: task.is_completed,
        },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => showToast(`"${task.title}" dipindahkan ke ${col.label}`, 'success'),
        },
    );
};
</script>

<template>
    <Head :title="`Task — ${project.name}`" />

    <AuthenticatedLayout eyebrow="Project" title="Task">
        <template #actions>
            <AppButton variant="secondary" :href="route('projects.index')">Kembali ke Project</AppButton>
            <SearchInput v-model="search" placeholder="Cari task..." />
        </template>

        <ProjectDetailTabs :project="project" :categories="categories" active="tasks">
            <template #action>
                <div class="flex gap-2">
                    <AppButton variant="secondary" :href="route('projects.tasks.import.create', project.id)">Import</AppButton>
                    <AppButton variant="secondary" :href="route('projects.tasks.export.create', project.id)">Export</AppButton>
                    <AppButton variant="primary" @click="openCreateTask('todo')">
                        <Plus :size="14" :stroke-width="2.5" /> Tambah Task
                    </AppButton>
                </div>
            </template>
        </ProjectDetailTabs>

        <div class="grid grid-cols-3 gap-4">
            <div
                v-for="col in columns"
                :key="col.key"
                class="rounded-card border-2 p-2 transition-all duration-200 ease-out"
                :class="dragOverColumnKey === col.key
                    ? 'border-dashed border-accent bg-accent-tint scale-[1.01] shadow-inner'
                    : 'border-transparent'"
                @dragover.prevent="dragOverColumnKey = col.key"
                @dragleave="onColumnDragLeave($event)"
                @drop="onDrop(col)"
            >
                <div class="mb-3 flex items-center gap-2">
                    <span class="font-display text-[13px] font-semibold">{{ col.label }}</span>
                    <span class="rounded-chip bg-neutral-tint px-2 py-0.5 text-[11px] font-bold text-secondary">
                        {{ col.tasks.length }}
                    </span>
                </div>

                <div class="flex flex-col gap-2.5">
                    <div
                        v-for="(task, index) in col.tasks"
                        :key="task.id"
                        draggable="true"
                        class="animate-stagger-in block cursor-grab rounded-card-sm border bg-white p-3.5 transition-all duration-150 ease-out active:cursor-grabbing"
                        :class="draggingTaskId === task.id
                            ? 'scale-[0.97] border-dashed border-accent/50 opacity-50 shadow-none'
                            : 'border-border opacity-100 shadow-card hover:border-accent/40 hover:shadow-popover'"
                        :style="{ animationDelay: `${index * 40}ms` }"
                        @dragstart="onDragStart($event, task)"
                        @dragend="onDragEnd"
                        @click="openEditTask(task)"
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
                    </div>

                    <p v-if="col.tasks.length === 0" class="py-4 text-center text-xs text-secondary">
                        Tidak ada task.
                    </p>

                    <button
                        class="flex items-center justify-center gap-1 rounded-card-sm border border-dashed border-border py-2 text-xs font-semibold text-secondary transition-colors duration-150 ease-out hover:border-accent/40 hover:text-accent-dark"
                        @click="openCreateTask(col.key)"
                    >
                        <Plus :size="13" :stroke-width="2.5" /> Tambah task
                    </button>
                </div>
            </div>
        </div>

        <div
            ref="dragGhost"
            class="fixed -top-[1000px] -left-[1000px] whitespace-nowrap rounded-full border border-accent/30 bg-white px-3.5 py-2 text-xs font-bold text-ink shadow-lg"
        />

        <TaskModal
            :show="!!taskModal"
            :mode="taskModal?.mode ?? 'create'"
            :task="taskModal?.task ?? null"
            :project="project"
            :users="users"
            :preset-status="taskModal?.presetStatus ?? 'todo'"
            @close="closeTaskModal"
        />
    </AuthenticatedLayout>
</template>
