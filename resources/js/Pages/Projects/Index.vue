<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import Badge from '@/Components/Badge.vue';
import Avatar from '@/Components/Avatar.vue';
import ProgressBar from '@/Components/ProgressBar.vue';
import AppButton from '@/Components/AppButton.vue';
import SearchInput from '@/Components/SearchInput.vue';
import ProjectModal from '@/Pages/Projects/ProjectModal.vue';
import { useLiveSearch } from '@/composables/useLiveSearch';
import { useConfirm } from '@/composables/useConfirm';
import { Head, router } from '@inertiajs/vue3';

const props = defineProps({
    projects: Object,
    categories: Array,
    filters: Object,
});

const search = useLiveSearch(route('projects.index'), props.filters.search);
const { askConfirm } = useConfirm();

const destroy = (project) => {
    askConfirm(`Hapus project "${project.name}"? Semua task di dalamnya ikut terhapus.`, () => {
        router.delete(route('projects.destroy', project.id));
    });
};

const projectModal = ref(null); // null | { mode, project }

const openCreateProject = () => {
    projectModal.value = { mode: 'create', project: null };
};

const openEditProject = (project) => {
    projectModal.value = { mode: 'edit', project };
};

const closeProjectModal = () => {
    projectModal.value = null;
};

const formatDate = (value) => {
    if (!value) return '-';
    return new Date(value).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
};

const progressPct = (project) =>
    project.tasks_count > 0 ? Math.round((project.done_tasks_count / project.tasks_count) * 100) : 0;
</script>

<template>
    <Head title="Projects" />

    <AuthenticatedLayout eyebrow="Kelola" title="Projects">
        <template #actions>
            <SearchInput v-model="search" placeholder="Cari project..." />
            <AppButton variant="secondary" :href="route('projects.trashed')">Project Terhapus</AppButton>
            <AppButton variant="secondary" :href="route('project-categories.index')">Kelola Kategori</AppButton>
            <AppButton variant="primary" @click="openCreateProject">+ Tambah Project</AppButton>
        </template>

        <div class="grid gap-[18px]" style="grid-template-columns: repeat(auto-fill, minmax(300px, 1fr))">
            <div
                v-for="project in projects.data"
                :key="project.id"
                class="flex cursor-pointer flex-col gap-3 rounded-card border border-border bg-white p-5 transition hover:border-accent/40"
                @click="router.visit(route('projects.tasks.index', project.id))"
            >
                <div class="flex items-center justify-between">
                    <Badge :label="project.category?.name ?? 'Tanpa Kategori'" tone="accent" />
                    <Badge :label="project.is_active ? 'Aktif' : 'Nonaktif'" :tone="project.is_active ? 'success' : 'neutral'" />
                </div>

                <div>
                    <div class="mb-1 text-base font-extrabold">{{ project.name }}</div>
                    <div class="line-clamp-2 text-[13px] leading-relaxed text-secondary">
                        {{ project.description || 'Tidak ada deskripsi.' }}
                    </div>
                </div>

                <div>
                    <div class="mb-1 flex justify-between text-xs text-secondary">
                        <span>Progress</span>
                        <span>{{ project.done_tasks_count }}/{{ project.tasks_count }}</span>
                    </div>
                    <ProgressBar :value="progressPct(project)" :height="6" />
                </div>

                <div class="flex items-center justify-between border-t border-divider pt-2.5">
                    <div class="flex items-center gap-2">
                        <Avatar :name="project.creator?.name ?? '-'" :size="24" />
                        <span class="text-xs text-secondary">{{ project.creator?.name ?? '-' }}</span>
                    </div>
                    <span class="text-xs text-secondary">{{ formatDate(project.end_date) }}</span>
                </div>

                <div class="flex items-center justify-end gap-3 border-t border-divider pt-2.5 text-xs font-semibold" @click.stop>
                    <button @click="openEditProject(project)" class="text-ink hover:text-accent">Edit</button>
                    <button @click="destroy(project)" class="text-danger hover:underline">Hapus</button>
                </div>
            </div>

            <p v-if="projects.data.length === 0" class="col-span-full py-10 text-center text-sm text-secondary">
                Belum ada project.
            </p>
        </div>

        <Pagination :links="projects.links" />

        <ProjectModal
            :show="!!projectModal"
            :mode="projectModal?.mode ?? 'create'"
            :project="projectModal?.project ?? null"
            :categories="categories"
            @close="closeProjectModal"
        />
    </AuthenticatedLayout>
</template>
