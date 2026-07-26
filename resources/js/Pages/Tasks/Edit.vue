<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Textarea from '@/Components/Textarea.vue';
import SelectInput from '@/Components/SelectInput.vue';
import AuditTrail from '@/Components/AuditTrail.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';

const props = defineProps({
    task: Object,
    users: Array,
    audits: Array,
});

const toDateInput = (value) => (value ? value.slice(0, 10) : '');

const form = useForm({
    title: props.task.title,
    description: props.task.description ?? '',
    status: props.task.status,
    priority: props.task.priority,
    due_date: toDateInput(props.task.due_date),
    assigned_to: props.task.assigned_to ?? '',
});

const submit = () => {
    form.put(route('tasks.update', props.task.id));
};

const activeTab = ref('form');

const uploadForm = useForm({ file: null });
const fileInput = ref(null);

const uploadAttachment = () => {
    uploadForm.post(route('tasks.attachments.store', props.task.id), {
        forceFormData: true,
        onSuccess: () => {
            uploadForm.reset();
            if (fileInput.value) fileInput.value.value = '';
        },
    });
};

const toggleVerified = (attachment) => {
    router.patch(route('attachments.update', attachment.id), {
        is_verified: !attachment.is_verified,
    });
};

const deleteAttachment = (attachment) => {
    if (confirm(`Hapus file "${attachment.file_name}"?`)) {
        router.delete(route('attachments.destroy', attachment.id));
    }
};
</script>

<template>
    <Head title="Edit Task" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Edit Task — {{ task.title }}
                </h2>
                <Link :href="route('projects.tasks.index', task.project_id)">
                    <SecondaryButton>Kembali ke Task</SecondaryButton>
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-xl space-y-4 sm:px-6 lg:px-8">
                <div class="flex gap-2 border-b border-gray-200 dark:border-gray-700">
                    <button
                        class="px-3 py-2 text-sm font-medium"
                        :class="activeTab === 'form'
                            ? 'border-b-2 border-indigo-600 text-indigo-600 dark:text-indigo-400'
                            : 'text-gray-500 dark:text-gray-400'"
                        @click="activeTab = 'form'"
                    >
                        Detail
                    </button>
                    <button
                        class="px-3 py-2 text-sm font-medium"
                        :class="activeTab === 'attachments'
                            ? 'border-b-2 border-indigo-600 text-indigo-600 dark:text-indigo-400'
                            : 'text-gray-500 dark:text-gray-400'"
                        @click="activeTab = 'attachments'"
                    >
                        Attachments ({{ task.attachments.length }})
                    </button>
                    <button
                        class="px-3 py-2 text-sm font-medium"
                        :class="activeTab === 'audit'
                            ? 'border-b-2 border-indigo-600 text-indigo-600 dark:text-indigo-400'
                            : 'text-gray-500 dark:text-gray-400'"
                        @click="activeTab = 'audit'"
                    >
                        History &amp; Audit Trail
                    </button>
                </div>

                <div v-if="activeTab === 'form'" class="bg-white p-6 shadow sm:rounded-lg dark:bg-gray-800">
                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <InputLabel for="title" value="Judul Task" />
                            <TextInput
                                id="title"
                                v-model="form.title"
                                class="mt-1 block w-full"
                                required
                                autofocus
                            />
                            <InputError class="mt-2" :message="form.errors.title" />
                        </div>

                        <div>
                            <InputLabel for="description" value="Deskripsi" />
                            <Textarea id="description" v-model="form.description" class="mt-1 block w-full" rows="3" />
                            <InputError class="mt-2" :message="form.errors.description" />
                        </div>

                        <div>
                            <InputLabel for="assigned_to" value="Assignee" />
                            <SelectInput id="assigned_to" v-model="form.assigned_to" class="mt-1 block w-full">
                                <option value="">- Belum ditugaskan -</option>
                                <option v-for="user in users" :key="user.id" :value="user.id">
                                    {{ user.name }}
                                </option>
                            </SelectInput>
                            <InputError class="mt-2" :message="form.errors.assigned_to" />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <InputLabel for="status" value="Status" />
                                <SelectInput id="status" v-model="form.status" class="mt-1 block w-full">
                                    <option value="todo">Todo</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="done">Done</option>
                                </SelectInput>
                                <InputError class="mt-2" :message="form.errors.status" />
                            </div>
                            <div>
                                <InputLabel for="priority" value="Prioritas" />
                                <SelectInput id="priority" v-model="form.priority" class="mt-1 block w-full">
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                </SelectInput>
                                <InputError class="mt-2" :message="form.errors.priority" />
                            </div>
                        </div>

                        <div>
                            <InputLabel for="due_date" value="Due Date" />
                            <TextInput
                                id="due_date"
                                type="date"
                                v-model="form.due_date"
                                class="mt-1 block w-full"
                            />
                            <InputError class="mt-2" :message="form.errors.due_date" />
                        </div>

                        <div class="flex items-center gap-3">
                            <PrimaryButton :disabled="form.processing">Simpan</PrimaryButton>
                        </div>
                    </form>
                </div>

                <div v-else-if="activeTab === 'attachments'" class="space-y-4">
                    <div class="bg-white p-6 shadow sm:rounded-lg dark:bg-gray-800">
                        <InputLabel value="Upload File (100KB - 500KB)" />
                        <div class="mt-2 flex items-center gap-3">
                            <input
                                ref="fileInput"
                                type="file"
                                class="text-sm text-gray-600 dark:text-gray-400"
                                @change="uploadForm.file = $event.target.files[0]"
                            />
                            <PrimaryButton :disabled="uploadForm.processing || !uploadForm.file" @click="uploadAttachment">
                                Upload
                            </PrimaryButton>
                        </div>
                        <InputError class="mt-2" :message="uploadForm.errors.file" />
                    </div>

                    <div class="overflow-x-auto rounded-lg bg-white shadow dark:bg-gray-800">
                        <table class="w-full text-left text-sm">
                            <thead class="border-b border-gray-200 text-xs uppercase text-gray-500 dark:border-gray-700 dark:text-gray-400">
                                <tr>
                                    <th class="px-4 py-3">File</th>
                                    <th class="px-4 py-3">Ukuran</th>
                                    <th class="px-4 py-3">Uploader</th>
                                    <th class="px-4 py-3">Verifikasi</th>
                                    <th class="px-4 py-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                <tr v-for="attachment in task.attachments" :key="attachment.id">
                                    <td class="px-4 py-3">
                                        <a
                                            :href="`/storage/${attachment.file_path}`"
                                            target="_blank"
                                            class="text-indigo-600 hover:underline dark:text-indigo-400"
                                        >
                                            {{ attachment.file_name }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400">
                                        {{ attachment.file_size }} KB
                                    </td>
                                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400">
                                        {{ attachment.uploader?.name ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <button
                                            class="rounded-full px-2 py-0.5 text-xs font-semibold"
                                            :class="attachment.is_verified
                                                ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                                                : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300'"
                                            @click="toggleVerified(attachment)"
                                        >
                                            {{ attachment.is_verified ? 'Terverifikasi' : 'Belum Verifikasi' }}
                                        </button>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <button
                                            @click="deleteAttachment(attachment)"
                                            class="text-red-600 hover:underline dark:text-red-400"
                                        >
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="task.attachments.length === 0">
                                    <td colspan="5" class="px-4 py-6 text-center text-gray-400">
                                        Belum ada attachment.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div v-else class="bg-white p-6 shadow sm:rounded-lg dark:bg-gray-800">
                    <AuditTrail :audits="audits" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
