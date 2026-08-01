<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import Badge from '@/Components/Badge.vue';
import ProjectModal from '@/Pages/Projects/ProjectModal.vue';

const props = defineProps({
    project: {
        type: Object,
        required: true,
    },
    categories: {
        type: Array,
        default: () => [],
    },
    active: {
        type: String,
        required: true, // 'tasks' | 'history'
    },
});

const tabClass = (key) =>
    props.active === key
        ? 'border-b-2 border-accent text-accent-dark'
        : 'border-b-2 border-transparent text-secondary transition-colors duration-150 ease-out hover:text-ink';

const showEditModal = ref(false);
</script>

<template>
    <div>
        <div class="mb-1.5 text-[13px] text-secondary">
            <Link :href="route('projects.index')" class="text-accent hover:text-accent-dark">Projects</Link>
            / {{ project.name }}
        </div>

        <div class="mb-5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="font-display text-[22px] font-semibold tracking-tight">{{ project.name }}</div>
                <Badge :label="project.is_active ? 'Aktif' : 'Nonaktif'" :tone="project.is_active ? 'success' : 'neutral'" />
                <button class="text-[13px] font-semibold text-accent transition-colors duration-150 ease-out hover:text-accent-dark" @click="showEditModal = true">
                    Edit
                </button>
            </div>
            <slot name="action" />
        </div>

        <div class="mb-6 flex gap-1 border-b border-border">
            <Link
                :href="route('projects.tasks.index', project.id)"
                class="px-3.5 py-2.5 text-[13px] font-bold"
                :class="tabClass('tasks')"
            >
                Task
            </Link>
            <Link
                :href="route('projects.edit', project.id)"
                class="px-3.5 py-2.5 text-[13px] font-bold"
                :class="tabClass('history')"
            >
                History &amp; Audit Trail
            </Link>
        </div>

        <ProjectModal
            :show="showEditModal"
            mode="edit"
            :project="project"
            :categories="categories"
            @close="showEditModal = false"
        />
    </div>
</template>
