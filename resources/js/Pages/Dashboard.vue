<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ProgressBar from '@/Components/ProgressBar.vue';
import { Head } from '@inertiajs/vue3';
import { FolderKanban, ListChecks, CircleCheckBig, AlertTriangle } from '@lucide/vue';

defineProps({
    stats: Array,
    progressPerProject: Array,
    attentionTasks: Array,
    recentActivity: Array,
});

const statIcons = [FolderKanban, ListChecks, CircleCheckBig, AlertTriangle];

const subToneClass = {
    success: 'text-success',
    danger: 'text-danger',
    neutral: 'text-secondary',
};

const eventDotClass = {
    created: 'bg-success',
    updated: 'bg-info',
    deleted: 'bg-danger',
    restored: 'bg-warning',
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout eyebrow="Ringkasan" title="Dashboard">
        <div class="mb-7 grid grid-cols-4 gap-4">
            <div
                v-for="(card, index) in stats"
                :key="card.label"
                class="animate-stagger-in rounded-card border border-border bg-white p-5"
                :style="{ animationDelay: `${index * 40}ms` }"
            >
                <div class="mb-3 flex items-center justify-between">
                    <span class="text-[13px] font-semibold text-secondary">{{ card.label }}</span>
                    <span class="flex h-7 w-7 items-center justify-center rounded-control bg-accent-tint text-accent-dark">
                        <component :is="statIcons[index]" :size="15" :stroke-width="2.25" />
                    </span>
                </div>
                <div class="font-display text-[28px] font-semibold tracking-tight">{{ card.value }}</div>
                <div class="mt-1.5 text-xs font-semibold" :class="subToneClass[card.subTone] ?? 'text-secondary'">
                    {{ card.sub }}
                </div>
            </div>
        </div>

        <div class="grid grid-cols-[1.3fr_1fr] gap-5">
            <div class="rounded-card border border-border bg-white p-[22px]">
                <div class="mb-4 font-display text-[15px] font-semibold">Progress per Project</div>
                <div class="flex flex-col gap-4">
                    <div v-for="row in progressPerProject" :key="row.name">
                        <div class="mb-1.5 flex justify-between text-[13px]">
                            <span class="font-bold">{{ row.name }}</span>
                            <span class="text-secondary">{{ row.doneCount }}/{{ row.totalCount }} task</span>
                        </div>
                        <ProgressBar :value="row.percentage" :height="8" />
                    </div>
                    <p v-if="progressPerProject.length === 0" class="text-sm text-secondary">
                        Belum ada project dengan task.
                    </p>
                </div>

                <div class="mb-3.5 mt-6 font-display text-[15px] font-semibold">Task Overdue &amp; Due Soon</div>
                <div class="flex flex-col border-t border-divider">
                    <div
                        v-for="(task, index) in attentionTasks"
                        :key="index"
                        class="flex items-center justify-between border-b border-divider py-[11px]"
                    >
                        <div>
                            <div class="text-[13px] font-bold">{{ task.title }}</div>
                            <div class="text-xs text-secondary">{{ task.projectName }} &middot; {{ task.assignee }}</div>
                        </div>
                        <span
                            class="text-xs font-bold"
                            :class="task.overdue ? 'text-danger' : 'text-warning'"
                        >
                            {{ task.dueLabel }}
                        </span>
                    </div>
                    <p v-if="attentionTasks.length === 0" class="py-3 text-sm text-secondary">
                        Tidak ada task overdue atau due soon.
                    </p>
                </div>
            </div>

            <div class="rounded-card border border-border bg-white p-[22px]">
                <div class="mb-4 font-display text-[15px] font-semibold">Aktivitas Terbaru</div>
                <div class="flex flex-col gap-4">
                    <div v-for="(item, index) in recentActivity" :key="index" class="flex gap-2.5">
                        <div
                            class="mt-1.5 h-2 w-2 shrink-0 rounded-full"
                            :class="eventDotClass[item.event] ?? 'bg-neutral'"
                        />
                        <div>
                            <div class="text-[13px] leading-snug">{{ item.text }}</div>
                            <div class="mt-0.5 text-xs text-neutral">{{ item.time }}</div>
                        </div>
                    </div>
                    <p v-if="recentActivity.length === 0" class="text-sm text-secondary">
                        Belum ada aktivitas.
                    </p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
