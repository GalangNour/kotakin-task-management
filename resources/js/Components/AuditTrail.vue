<script setup>
defineProps({
    audits: {
        type: Array,
        required: true,
    },
});

const eventBadge = {
    created: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
    updated: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
    deleted: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
    restored:
        'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
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
    <div class="space-y-4">
        <p
            v-if="audits.length === 0"
            class="text-sm text-gray-500 dark:text-gray-400"
        >
            Belum ada riwayat perubahan.
        </p>

        <div
            v-for="audit in audits"
            :key="audit.id"
            class="rounded-md border border-gray-200 p-3 dark:border-gray-700"
        >
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span
                        class="rounded-full px-2 py-0.5 text-xs font-semibold uppercase"
                        :class="eventBadge[audit.event] ?? 'bg-gray-100 text-gray-800'"
                    >
                        {{ audit.event }}
                    </span>
                    <span class="text-sm text-gray-600 dark:text-gray-300">
                        oleh {{ audit.user?.name ?? 'System' }}
                    </span>
                </div>
                <span class="text-xs text-gray-400">
                    {{ new Date(audit.created_at).toLocaleString('id-ID') }}
                </span>
            </div>

            <table
                v-if="audit.event === 'updated' && changedKeys(audit).length"
                class="mt-2 w-full text-xs"
            >
                <tbody>
                    <tr v-for="key in changedKeys(audit)" :key="key">
                        <td class="pe-2 py-0.5 font-medium text-gray-500 dark:text-gray-400">
                            {{ key }}
                        </td>
                        <td class="pe-2 py-0.5 text-red-500 line-through">
                            {{ formatValue(audit.old_values?.[key]) }}
                        </td>
                        <td class="py-0.5 text-green-600 dark:text-green-400">
                            {{ formatValue(audit.new_values?.[key]) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
