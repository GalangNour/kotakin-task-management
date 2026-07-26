<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import Textarea from '@/Components/Textarea.vue';
import SelectInput from '@/Components/SelectInput.vue';
import AppButton from '@/Components/AppButton.vue';
import Badge from '@/Components/Badge.vue';
import AuditTrail from '@/Components/AuditTrail.vue';
import { Head, router, useForm } from '@inertiajs/vue3';

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

const deleteTask = () => {
    if (confirm(`Hapus task "${props.task.title}"?`)) {
        router.delete(route('tasks.destroy', props.task.id));
    }
};
</script>

<template>
    <Head title="Edit Task" />

    <AuthenticatedLayout eyebrow="Project" title="Task">
        <template #actions>
            <AppButton variant="secondary" :href="route('projects.tasks.index', task.project_id)">Kembali ke Task</AppButton>
        </template>

        <div class="mb-5 flex items-center justify-between">
            <div class="text-[22px] font-extrabold tracking-tight">{{ task.title }}</div>
            <button class="text-[13px] font-semibold text-danger hover:underline" @click="deleteTask">Hapus Task</button>
        </div>

        <div class="max-w-[640px] space-y-5">
            <div class="flex gap-1 border-b border-border">
                <button
                    class="px-3.5 py-2.5 text-[13px] font-bold transition"
                    :class="activeTab === 'form' ? 'border-b-2 border-accent text-accent-dark' : 'border-b-2 border-transparent text-secondary hover:text-ink'"
                    @click="activeTab = 'form'"
                >
                    Detail
                </button>
                <button
                    class="px-3.5 py-2.5 text-[13px] font-bold transition"
                    :class="activeTab === 'attachments' ? 'border-b-2 border-accent text-accent-dark' : 'border-b-2 border-transparent text-secondary hover:text-ink'"
                    @click="activeTab = 'attachments'"
                >
                    Attachments ({{ task.attachments.length }})
                </button>
                <button
                    class="px-3.5 py-2.5 text-[13px] font-bold transition"
                    :class="activeTab === 'audit' ? 'border-b-2 border-accent text-accent-dark' : 'border-b-2 border-transparent text-secondary hover:text-ink'"
                    @click="activeTab = 'audit'"
                >
                    History &amp; Audit Trail
                </button>
            </div>

            <div v-if="activeTab === 'form'" class="rounded-card border border-border bg-white p-6">
                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <InputLabel for="title" value="Judul Task" class="!text-xs !font-semibold !text-secondary" />
                        <TextInput id="title" v-model="form.title" class="mt-1 block w-full" required autofocus />
                        <InputError class="mt-1" :message="form.errors.title" />
                    </div>

                    <div>
                        <InputLabel for="description" value="Deskripsi" class="!text-xs !font-semibold !text-secondary" />
                        <Textarea id="description" v-model="form.description" class="mt-1 block w-full" rows="3" />
                        <InputError class="mt-1" :message="form.errors.description" />
                    </div>

                    <div>
                        <InputLabel for="assigned_to" value="Assignee" class="!text-xs !font-semibold !text-secondary" />
                        <SelectInput id="assigned_to" v-model="form.assigned_to" class="mt-1 block w-full">
                            <option value="">- Belum ditugaskan -</option>
                            <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                        </SelectInput>
                        <InputError class="mt-1" :message="form.errors.assigned_to" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="status" value="Status" class="!text-xs !font-semibold !text-secondary" />
                            <SelectInput id="status" v-model="form.status" class="mt-1 block w-full">
                                <option value="todo">Todo</option>
                                <option value="in_progress">In Progress</option>
                                <option value="done">Done</option>
                            </SelectInput>
                            <InputError class="mt-1" :message="form.errors.status" />
                        </div>
                        <div>
                            <InputLabel for="priority" value="Prioritas" class="!text-xs !font-semibold !text-secondary" />
                            <SelectInput id="priority" v-model="form.priority" class="mt-1 block w-full">
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                            </SelectInput>
                            <InputError class="mt-1" :message="form.errors.priority" />
                        </div>
                    </div>

                    <div>
                        <InputLabel for="due_date" value="Due Date" class="!text-xs !font-semibold !text-secondary" />
                        <TextInput id="due_date" type="date" v-model="form.due_date" class="mt-1 block w-full" />
                        <InputError class="mt-1" :message="form.errors.due_date" />
                    </div>

                    <AppButton type="submit" variant="primary" :disabled="form.processing">Simpan</AppButton>
                </form>
            </div>

            <div v-else-if="activeTab === 'attachments'" class="space-y-4">
                <div class="rounded-card border border-border bg-white p-6">
                    <InputLabel value="Upload File (100KB - 500KB)" class="!text-xs !font-semibold !text-secondary" />
                    <div class="mt-2 flex items-center gap-3">
                        <input
                            ref="fileInput"
                            type="file"
                            class="text-sm text-secondary"
                            @change="uploadForm.file = $event.target.files[0]"
                        />
                        <AppButton variant="primary" :disabled="uploadForm.processing || !uploadForm.file" @click="uploadAttachment">
                            Upload
                        </AppButton>
                    </div>
                    <InputError class="mt-2" :message="uploadForm.errors.file" />
                </div>

                <div class="overflow-hidden rounded-card border border-border bg-white">
                    <div
                        v-for="attachment in task.attachments"
                        :key="attachment.id"
                        class="flex items-center gap-4 border-b border-divider px-5 py-3.5 last:border-b-0"
                    >
                        <a
                            :href="`/storage/${attachment.file_path}`"
                            target="_blank"
                            class="flex-1 text-[13px] font-bold text-accent hover:text-accent-dark"
                        >
                            {{ attachment.file_name }}
                        </a>
                        <span class="text-xs text-secondary">{{ attachment.file_size }} KB</span>
                        <span class="text-xs text-secondary">{{ attachment.uploader?.name ?? '-' }}</span>
                        <button @click="toggleVerified(attachment)">
                            <Badge
                                :label="attachment.is_verified ? 'Terverifikasi' : 'Belum Verifikasi'"
                                :tone="attachment.is_verified ? 'success' : 'neutral'"
                            />
                        </button>
                        <button @click="deleteAttachment(attachment)" class="text-[13px] font-semibold text-danger hover:underline">
                            Hapus
                        </button>
                    </div>

                    <p v-if="task.attachments.length === 0" class="py-8 text-center text-sm text-secondary">
                        Belum ada attachment.
                    </p>
                </div>
            </div>

            <div v-else class="rounded-card border border-border bg-white p-6">
                <AuditTrail :audits="audits" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
