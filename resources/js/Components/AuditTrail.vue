<script setup>
import Badge from '@/Components/Badge.vue';
import { ArrowRight } from '@lucide/vue';

defineProps({
    audits: {
        type: Array,
        required: true,
    },
});

const eventMeta = {
    created: { label: 'Dibuat', tone: 'success', dot: 'bg-success' },
    updated: { label: 'Diperbarui', tone: 'info', dot: 'bg-info' },
    deleted: { label: 'Dihapus', tone: 'danger', dot: 'bg-danger' },
    restored: { label: 'Dipulihkan', tone: 'warning', dot: 'bg-warning' },
};

const changedKeys = (audit) => {
    const keys = new Set([
        ...Object.keys(audit.old_values ?? {}),
        ...Object.keys(audit.new_values ?? {}),
    ]);
    return [...keys].filter(
        (key) => !['id', 'created_at', 'updated_at'].includes(key),
    );
};

const formatValue = (value) => {
    if (value === null || value === undefined) return '—';
    if (typeof value === 'object') return JSON.stringify(value);
    return String(value);
};
</script>

<template>
    <div class="flex flex-col gap-0">
        <p v-if="audits.length === 0" class="text-sm text-secondary">
            Belum ada riwayat perubahan.
        </p>

        <div v-for="(audit, index) in audits" :key="audit.id" class="relative flex gap-3.5 pb-5">
            <div class="flex flex-col items-center">
                <div
                    class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full"
                    :class="(eventMeta[audit.event] ?? eventMeta.updated).dot"
                />
                <div v-if="index < audits.length - 1" class="mt-1 w-0.5 flex-1 bg-neutral-tint" />
            </div>

            <div class="flex-1 pb-1">
                <div class="mb-1.5 flex items-center gap-2">
                    <span v-if="audit.subject_type === 'task'" class="text-xs font-semibold text-secondary">
                        Task &middot; {{ audit.subject_label ?? 'tanpa judul' }}
                    </span>
                    <Badge
                        :label="(eventMeta[audit.event] ?? eventMeta.updated).label"
                        :tone="(eventMeta[audit.event] ?? eventMeta.updated).tone"
                    />
                    <span class="text-[13px] font-semibold">oleh {{ audit.user?.name ?? 'System' }}</span>
                    <span class="text-xs text-neutral">{{ new Date(audit.created_at).toLocaleString('id-ID') }}</span>
                </div>

                <div v-if="audit.event === 'updated' && changedKeys(audit).length">
                    <div v-for="key in changedKeys(audit)" :key="key" class="mb-0.5 flex items-center gap-1 text-xs">
                        <span class="font-semibold text-secondary">{{ key }}:</span>
                        <span class="text-danger line-through">{{ formatValue(audit.old_values?.[key]) }}</span>
                        <ArrowRight :size="11" :stroke-width="2.25" class="text-neutral" />
                        <span class="text-success">{{ formatValue(audit.new_values?.[key]) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
