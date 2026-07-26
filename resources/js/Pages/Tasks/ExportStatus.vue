<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    export: Object,
});

const status = ref(props.export.status);
const error = ref(props.export.error);
const downloadUrl = ref(null);
let timer = null;

const statusLabel = {
    pending: 'Menunggu diproses...',
    processing: 'Sedang diproses...',
    completed: 'Selesai',
    failed: 'Gagal',
};

const poll = async () => {
    const res = await fetch(route('task-exports.status', props.export.id));
    const data = await res.json();
    status.value = data.status;
    error.value = data.error;
    downloadUrl.value = data.download_url;

    if (data.status === 'completed' || data.status === 'failed') {
        clearInterval(timer);
    }
};

onMounted(() => {
    if (status.value !== 'completed' && status.value !== 'failed') {
        timer = setInterval(poll, 1500);
        poll();
    } else {
        poll();
    }
});

onBeforeUnmount(() => clearInterval(timer));
</script>

<template>
    <Head title="Status Export" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Status Export Task
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

                    <p v-if="error" class="text-sm text-red-600 dark:text-red-400">{{ error }}</p>

                    <a
                        v-if="downloadUrl"
                        :href="downloadUrl"
                        class="inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-gray-700 dark:bg-gray-200 dark:text-gray-800"
                    >
                        Download File
                    </a>

                    <div>
                        <Link :href="route('projects.tasks.index', props.export.project_id)">
                            <SecondaryButton>Kembali ke Task</SecondaryButton>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
