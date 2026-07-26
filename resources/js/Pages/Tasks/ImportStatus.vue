<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    import: Object,
});

const status = ref(props.import.status);
const totalRows = ref(props.import.total_rows);
const importedRows = ref(props.import.imported_rows);
const failedRows = ref(props.import.failed_rows);
const errors = ref(props.import.errors ?? []);
const tasksUrl = ref(null);
let timer = null;

const statusLabel = {
    pending: 'Menunggu diproses...',
    processing: 'Sedang diproses...',
    completed: 'Selesai',
    failed: 'Gagal',
};

const poll = async () => {
    const res = await fetch(route('task-imports.status', props.import.id));
    const data = await res.json();
    status.value = data.status;
    totalRows.value = data.total_rows;
    importedRows.value = data.imported_rows;
    failedRows.value = data.failed_rows;
    errors.value = data.errors ?? [];
    tasksUrl.value = data.tasks_url;

    if (data.status === 'completed' || data.status === 'failed') {
        clearInterval(timer);
    }
};

onMounted(() => {
    poll();
    if (status.value !== 'completed' && status.value !== 'failed') {
        timer = setInterval(poll, 1500);
    }
});

onBeforeUnmount(() => clearInterval(timer));
</script>

<template>
    <Head title="Status Import" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Status Import Task
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-xl sm:px-6 lg:px-8">
                <div class="space-y-4 bg-white p-6 shadow sm:rounded-lg dark:bg-gray-800">
                    <div class="flex items-center gap-2">
                        <span
                            class="h-2.5 w-2.5 rounded-full"
                            :class="{
                                'animate-pulse bg-yellow-400': status === 'pending' || status === 'processing',
                                'bg-green-500': status === 'completed',
                                'bg-red-500': status === 'failed',
                            }"
                        />
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ statusLabel[status] }}
                        </span>
                    </div>

                    <div v-if="status === 'completed'" class="text-sm text-gray-600 dark:text-gray-400">
                        <p>Total baris: {{ totalRows }}</p>
                        <p>Berhasil diimport: {{ importedRows }}</p>
                        <p>Dilewati: {{ failedRows }}</p>
                    </div>

                    <div v-if="errors.length" class="space-y-1 rounded-md bg-red-50 p-3 text-xs text-red-700 dark:bg-red-950 dark:text-red-300">
                        <p v-for="(err, index) in errors" :key="index">{{ err }}</p>
                    </div>

                    <Link v-if="tasksUrl" :href="tasksUrl">
                        <SecondaryButton>Lihat Daftar Task</SecondaryButton>
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
