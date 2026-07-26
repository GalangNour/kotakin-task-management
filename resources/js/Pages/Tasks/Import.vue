<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import SelectInput from '@/Components/SelectInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    project: Object,
    systemFields: Object,
});

const step = ref('upload');
const uploading = ref(false);
const uploadError = ref('');
const fileHeaders = ref([]);

const uploadState = ref({
    file_path: '',
    original_file_name: '',
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
            headers: {
                'X-XSRF-TOKEN': getCsrfToken(),
                Accept: 'application/json',
            },
            body: formData,
        });

        if (!res.ok) {
            const data = await res.json().catch(() => ({}));
            uploadError.value = data.errors?.file?.[0] ?? 'Gagal membaca file.';
            return;
        }

        const data = await res.json();
        fileHeaders.value = data.headers;
        uploadState.value = {
            file_path: data.file_path,
            original_file_name: data.original_file_name,
        };
        form.file_path = data.file_path;
        form.original_file_name = data.original_file_name;
        step.value = 'mapping';
    } catch (e) {
        uploadError.value = 'Gagal mengupload file.';
    } finally {
        uploading.value = false;
    }
};

const form = useForm({
    file_path: '',
    original_file_name: '',
    mapping: {},
});

const submit = () => {
    form.post(route('projects.tasks.import.store', props.project.id));
};
</script>

<template>
    <Head title="Import Task" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Import Task — {{ project.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-xl sm:px-6 lg:px-8">
                <div class="bg-white p-6 shadow sm:rounded-lg dark:bg-gray-800">
                    <div v-if="step === 'upload'" class="space-y-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Upload file Excel/CSV berisi daftar task. Baris pertama harus berupa nama kolom (header).
                        </p>
                        <input
                            type="file"
                            accept=".xlsx,.xls,.csv"
                            class="text-sm text-gray-600 dark:text-gray-400"
                            :disabled="uploading"
                            @change="uploadFile"
                        />
                        <p v-if="uploading" class="text-sm text-gray-500">Membaca file...</p>
                        <InputError :message="uploadError" />

                        <Link :href="route('projects.tasks.index', project.id)">
                            <SecondaryButton>Batal</SecondaryButton>
                        </Link>
                    </div>

                    <form v-else @submit.prevent="submit" class="space-y-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            File: <strong>{{ uploadState.original_file_name }}</strong>.
                            Cocokkan kolom di file dengan field task.
                        </p>

                        <div v-for="(label, key) in systemFields" :key="key">
                            <InputLabel :for="key" :value="label" />
                            <SelectInput :id="key" v-model="form.mapping[key]" class="mt-1 block w-full">
                                <option value="">- Tidak dipetakan -</option>
                                <option v-for="header in fileHeaders" :key="header" :value="header">
                                    {{ header }}
                                </option>
                            </SelectInput>
                            <InputError class="mt-2" :message="form.errors[`mapping.${key}`]" />
                        </div>

                        <InputError :message="form.errors.mapping" />

                        <div class="flex items-center gap-3">
                            <PrimaryButton :disabled="form.processing">Mulai Import</PrimaryButton>
                            <SecondaryButton type="button" @click="step = 'upload'">
                                Ganti File
                            </SecondaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
