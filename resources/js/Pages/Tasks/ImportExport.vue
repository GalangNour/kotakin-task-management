<script setup>
import { computed, onBeforeUnmount, ref, watch } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import SelectInput from '@/Components/SelectInput.vue';
import AppButton from '@/Components/AppButton.vue';
import Badge from '@/Components/Badge.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    project: Object,
    systemFields: Object,
    availableColumns: Object,
    exportHistory: Array,
    activeTab: String,
    activeImport: Object,
    activeExport: Object,
});

// Inertia reuses this same component instance across create()/show() visits
// (same page component name), so plain refs seeded once from props go stale.
// Everything derived from props is kept in sync via watchers below instead.
const tab = ref(props.activeTab ?? 'import');
watch(() => props.activeTab, (value) => { tab.value = value ?? 'import'; });

/* ---------- Import tab ---------- */

const IMPORT_STEP_LABELS = ['Upload', 'Mapping', 'Diproses', 'Selesai'];

const uploadStep = ref('upload'); // 'upload' | 'mapping' — only relevant when no activeImport yet
const uploading = ref(false);
const uploadError = ref('');
const fileHeaders = ref([]);
const uploadState = ref({ file_path: '', original_file_name: '' });

const importForm = useForm({
    file_path: '',
    original_file_name: '',
    mapping: {},
});

const getCsrfToken = () => {
    const match = document.cookie.match(/XSRF-TOKEN=([^;]+)/);
    return match ? decodeURIComponent(match[1]) : '';
};

const uploadFile = async (event) => {
    const file = event.target.files[0];
    if (!file) return;

    uploading.value = true;
    uploadError.value = '';

    const formData = new FormData();
    formData.append('file', file);

    try {
        const res = await fetch(route('tasks.import.headers'), {
            method: 'POST',
            headers: { 'X-XSRF-TOKEN': getCsrfToken(), Accept: 'application/json' },
            body: formData,
        });

        if (!res.ok) {
            const data = await res.json().catch(() => ({}));
            uploadError.value = data.errors?.file?.[0] ?? 'Gagal membaca file.';
            return;
        }

        const data = await res.json();
        fileHeaders.value = data.headers;
        uploadState.value = { file_path: data.file_path, original_file_name: data.original_file_name };
        importForm.file_path = data.file_path;
        importForm.original_file_name = data.original_file_name;
        uploadStep.value = 'mapping';
    } catch (e) {
        uploadError.value = 'Gagal mengupload file.';
    } finally {
        uploading.value = false;
    }
};

const submitImport = () => {
    importForm.post(route('projects.tasks.import.store', props.project.id));
};

const resetImport = () => {
    uploadStep.value = 'upload';
    fileHeaders.value = [];
    uploadState.value = { file_path: '', original_file_name: '' };
    importForm.reset();
};

const importStatus = ref(null);
const importCounts = ref({ total: null, imported: null, failed: null, errors: [] });
let importTimer = null;

const stopImportPolling = () => {
    clearInterval(importTimer);
    importTimer = null;
};

const pollImport = async (importId) => {
    const res = await fetch(route('task-imports.status', importId));
    const data = await res.json();
    importStatus.value = data.status;
    importCounts.value = {
        total: data.total_rows,
        imported: data.imported_rows,
        failed: data.failed_rows,
        errors: data.errors ?? [],
    };
    if (data.status === 'completed' || data.status === 'failed') {
        stopImportPolling();
    }
};

watch(
    () => props.activeImport,
    (activeImport) => {
        stopImportPolling();

        if (!activeImport) {
            importStatus.value = null;
            uploadStep.value = 'upload';
            fileHeaders.value = [];
            uploadState.value = { file_path: '', original_file_name: '' };
            importForm.reset();
            return;
        }

        importStatus.value = activeImport.status;
        importCounts.value = {
            total: activeImport.total_rows,
            imported: activeImport.imported_rows,
            failed: activeImport.failed_rows,
            errors: activeImport.errors ?? [],
        };

        if (activeImport.status === 'pending' || activeImport.status === 'processing') {
            importTimer = setInterval(() => pollImport(activeImport.id), 1500);
        }
    },
    { immediate: true },
);

const currentImportStepIndex = computed(() => {
    if (!props.activeImport) return uploadStep.value === 'upload' ? 0 : 1;
    return importStatus.value === 'completed' || importStatus.value === 'failed' ? 3 : 2;
});

/* ---------- Export tab ---------- */

const exportForm = useForm({
    columns: Object.keys(props.availableColumns),
});

const submitExport = () => {
    exportForm.post(route('projects.tasks.export.store', props.project.id));
};

const historyRows = ref([]);
watch(() => props.exportHistory, (value) => { historyRows.value = [...(value ?? [])]; }, { immediate: true });

const exportStatusLabel = { pending: 'Menunggu', processing: 'Diproses', completed: 'Selesai', failed: 'Gagal' };
const exportStatusTone = { pending: 'warning', processing: 'warning', completed: 'success', failed: 'danger' };

let exportTimer = null;

const stopExportPolling = () => {
    clearInterval(exportTimer);
    exportTimer = null;
};

const pollExport = async (exportId) => {
    const res = await fetch(route('task-exports.status', exportId));
    const data = await res.json();
    const row = historyRows.value.find((r) => r.id === exportId);
    if (row) row.status = data.status;
    if (data.status === 'completed' || data.status === 'failed') {
        stopExportPolling();
    }
};

watch(
    () => props.activeExport,
    (activeExport) => {
        stopExportPolling();
        if (activeExport && !['completed', 'failed'].includes(activeExport.status)) {
            exportTimer = setInterval(() => pollExport(activeExport.id), 1500);
        }
    },
    { immediate: true },
);

const downloadUrl = (job) => route('task-exports.download', job.id);

onBeforeUnmount(() => {
    stopImportPolling();
    stopExportPolling();
});
</script>

<template>
    <Head :title="`Import / Export — ${project.name}`" />

    <AuthenticatedLayout eyebrow="Task" title="Import / Export Excel">
        <div class="mb-1.5 text-[13px] text-secondary">{{ project.name }}</div>

        <div class="mb-6 flex gap-1 border-b border-border">
            <button
                class="px-3.5 py-2.5 text-[13px] font-bold transition"
                :class="tab === 'import' ? 'border-b-2 border-accent text-accent-dark' : 'border-b-2 border-transparent text-secondary hover:text-ink'"
                @click="tab = 'import'"
            >
                Import
            </button>
            <button
                class="px-3.5 py-2.5 text-[13px] font-bold transition"
                :class="tab === 'export' ? 'border-b-2 border-accent text-accent-dark' : 'border-b-2 border-transparent text-secondary hover:text-ink'"
                @click="tab = 'export'"
            >
                Export
            </button>
        </div>

        <div v-if="tab === 'import'" class="max-w-[640px]">
            <div class="mb-8 flex items-center">
                <div v-for="(label, index) in IMPORT_STEP_LABELS" :key="label" class="flex flex-1 items-center">
                    <div class="flex flex-col items-center gap-1.5">
                        <div
                            class="flex h-7 w-7 items-center justify-center rounded-full text-xs font-bold"
                            :class="index <= currentImportStepIndex ? 'bg-accent text-white' : 'bg-neutral-tint text-secondary'"
                        >
                            {{ index + 1 }}
                        </div>
                        <span
                            class="text-xs font-semibold"
                            :class="index === currentImportStepIndex ? 'text-ink' : 'text-secondary'"
                        >
                            {{ label }}
                        </span>
                    </div>
                    <div
                        v-if="index < IMPORT_STEP_LABELS.length - 1"
                        class="mx-2 h-0.5 flex-1"
                        :class="index < currentImportStepIndex ? 'bg-accent' : 'bg-neutral-tint'"
                    />
                </div>
            </div>

            <div class="rounded-card border border-border bg-white p-6">
                <!-- Step: upload -->
                <div
                    v-if="!activeImport && uploadStep === 'upload'"
                    class="rounded-card-sm border-2 border-dashed border-neutral/40 p-10 text-center"
                >
                    <div class="mb-1.5 text-sm font-bold">Upload file Excel/CSV</div>
                    <div class="mb-4 text-xs text-secondary">Baris pertama harus berupa nama kolom (header)</div>
                    <label class="cursor-pointer">
                        <input type="file" accept=".xlsx,.xls,.csv" class="hidden" :disabled="uploading" @change="uploadFile" />
                        <span class="inline-flex items-center justify-center rounded-control bg-accent px-4 py-2.5 text-[13px] font-bold text-white hover:bg-accent-dark">
                            {{ uploading ? 'Membaca file...' : 'Pilih File' }}
                        </span>
                    </label>
                    <InputError class="mt-3" :message="uploadError" />
                </div>

                <!-- Step: mapping -->
                <div v-else-if="!activeImport && uploadStep === 'mapping'">
                    <div class="mb-4 text-[13px] text-secondary">
                        File: <strong>{{ uploadState.original_file_name }}</strong> &mdash; cocokkan kolom file dengan field task.
                    </div>
                    <div class="flex flex-col gap-2.5">
                        <div v-for="(label, key) in systemFields" :key="key" class="flex items-center gap-3 rounded-card-sm bg-app px-3.5 py-2.5">
                            <span class="flex-1 text-[13px] font-bold">{{ label }}</span>
                            <span class="text-neutral">&rarr;</span>
                            <SelectInput :id="key" v-model="importForm.mapping[key]" class="flex-1 !py-1.5 !text-[13px]">
                                <option value="">- Tidak dipetakan -</option>
                                <option v-for="header in fileHeaders" :key="header" :value="header">{{ header }}</option>
                            </SelectInput>
                        </div>
                    </div>
                    <InputError class="mt-2" :message="importForm.errors.mapping" />
                    <div class="mt-5 flex gap-2.5">
                        <AppButton variant="primary" :disabled="importForm.processing" @click="submitImport">Mulai Import</AppButton>
                        <AppButton variant="secondary" @click="resetImport">Ganti File</AppButton>
                    </div>
                </div>

                <!-- Step: processing -->
                <div v-else-if="importStatus === 'pending' || importStatus === 'processing'" class="py-8 text-center">
                    <div class="mb-2 text-sm font-bold">Sedang diproses di background&hellip;</div>
                    <div class="mb-4 text-xs text-secondary">Halaman ini akan update otomatis saat selesai.</div>
                    <div class="h-2 w-full overflow-hidden rounded-full bg-neutral-tint">
                        <div class="h-full w-3/5 animate-pulse rounded-full bg-accent" />
                    </div>
                </div>

                <!-- Step: done -->
                <div v-else>
                    <div
                        class="mb-1 text-sm font-bold"
                        :class="importStatus === 'failed' ? 'text-danger' : 'text-success'"
                    >
                        {{ importStatus === 'failed' ? 'Import gagal' : 'Import selesai' }}
                    </div>
                    <div class="mb-4 text-[13px] text-secondary">
                        {{ importCounts.imported ?? 0 }} baris berhasil diimport, {{ importCounts.failed ?? 0 }} baris dilewati.
                    </div>
                    <div
                        v-if="importCounts.errors?.length"
                        class="rounded-card-sm border border-border p-3.5 text-xs text-danger"
                    >
                        <p v-for="(err, i) in importCounts.errors" :key="i">{{ err }}</p>
                    </div>
                    <AppButton class="mt-4" variant="primary" :href="route('projects.tasks.import.create', project.id)">
                        Import File Lain
                    </AppButton>
                </div>
            </div>
        </div>

        <div v-else class="max-w-[640px]">
            <div class="mb-5 rounded-card border border-border bg-white p-6">
                <div class="mb-3.5 text-sm font-bold">Pilih kolom yang di-export</div>
                <div class="mb-4 grid grid-cols-2 gap-2.5">
                    <label v-for="(label, key) in availableColumns" :key="key" class="flex items-center gap-2 text-[13px]">
                        <input
                            type="checkbox"
                            :value="key"
                            v-model="exportForm.columns"
                            class="rounded border-border text-accent focus:ring-accent"
                        />
                        {{ label }}
                    </label>
                </div>
                <InputError class="mb-3" :message="exportForm.errors.columns" />
                <AppButton variant="primary" :disabled="exportForm.processing || exportForm.columns.length === 0" @click="submitExport">
                    Export Sekarang
                </AppButton>
            </div>

            <div class="mb-2.5 text-sm font-bold">Riwayat Export</div>
            <div class="overflow-hidden rounded-card border border-border bg-white">
                <div
                    v-for="job in historyRows"
                    :key="job.id"
                    class="flex items-center justify-between border-b border-divider px-4.5 py-3.5 last:border-b-0"
                >
                    <div>
                        <div class="text-[13px] font-bold">tasks-export-{{ job.id.slice(0, 8) }}.xlsx</div>
                        <div class="text-xs text-secondary">{{ new Date(job.created_at).toLocaleString('id-ID') }}</div>
                    </div>
                    <div class="flex items-center gap-3">
                        <a
                            v-if="job.status === 'completed'"
                            :href="downloadUrl(job)"
                            class="text-[13px] font-semibold text-accent hover:text-accent-dark"
                        >
                            Download
                        </a>
                        <Badge :label="exportStatusLabel[job.status]" :tone="exportStatusTone[job.status]" />
                    </div>
                </div>

                <p v-if="historyRows.length === 0" class="py-8 text-center text-sm text-secondary">
                    Belum ada riwayat export.
                </p>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
