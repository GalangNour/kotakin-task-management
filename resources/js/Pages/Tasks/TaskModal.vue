<script setup>
import { computed, ref, watch } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import Textarea from '@/Components/Textarea.vue';
import SelectInput from '@/Components/SelectInput.vue';
import AppButton from '@/Components/AppButton.vue';
import Avatar from '@/Components/Avatar.vue';
import Badge from '@/Components/Badge.vue';
import AuditTrail from '@/Components/AuditTrail.vue';
import { useToast } from '@/composables/useToast';
import { useConfirm } from '@/composables/useConfirm';
import { X, MessageSquare, Paperclip, History as HistoryIcon, Send, Upload, Trash2, FileText } from '@lucide/vue';

const props = defineProps({
    show: { type: Boolean, default: false },
    mode: { type: String, default: 'create' }, // 'create' | 'edit'
    task: { type: Object, default: null },
    project: { type: Object, required: true },
    users: { type: Array, default: () => [] },
    presetStatus: { type: String, default: 'todo' },
});

const emit = defineEmits(['close']);

const { show: showToast } = useToast();
const { askConfirm } = useConfirm();

const STATUS_OPTIONS = [
    { key: 'todo', label: 'To Do' },
    { key: 'in_progress', label: 'In Progress' },
    { key: 'done', label: 'Selesai' },
];

const PRIORITY_OPTIONS = [
    { key: 'low', label: 'Low' },
    { key: 'medium', label: 'Medium' },
    { key: 'high', label: 'High' },
];

const activeTab = ref('detail');
const detail = ref(null); // { task: {..., attachments, comments}, audits }
const loadingDetail = ref(false);

const toDateInput = (value) => (value ? value.slice(0, 10) : '');

const buildFormState = () => ({
    title: props.task?.title ?? '',
    description: props.task?.description ?? '',
    status: props.task?.status ?? props.presetStatus,
    priority: props.task?.priority ?? 'medium',
    due_date: toDateInput(props.task?.due_date),
    assigned_to: props.task?.assigned_to ?? props.task?.assignee?.id ?? '',
});

const form = useForm(buildFormState());

const fetchDetail = async () => {
    if (!props.task) return;
    loadingDetail.value = true;
    try {
        const res = await fetch(route('tasks.modal-data', props.task.id), {
            headers: { Accept: 'application/json' },
        });
        detail.value = await res.json();
    } finally {
        loadingDetail.value = false;
    }
};

watch(
    () => props.show,
    (isOpen) => {
        if (!isOpen) return;
        activeTab.value = 'detail';
        form.clearErrors();
        Object.assign(form, buildFormState());
        detail.value = null;
        if (props.mode === 'edit') fetchDetail();
    },
);

const close = () => emit('close');

const submit = () => {
    if (!form.title.trim()) {
        showToast('Judul task tidak boleh kosong', 'error');
        return;
    }

    const options = { preserveScroll: true, onSuccess: () => close() };

    if (props.mode === 'edit') {
        form.put(route('tasks.update', props.task.id), options);
    } else {
        form.post(route('projects.tasks.store', props.project.id), options);
    }
};

const deleteTask = () => {
    askConfirm(`Hapus task "${props.task.title}"? Tindakan ini tidak dapat dibatalkan.`, () => {
        router.delete(route('tasks.destroy', props.task.id), {
            onSuccess: () => close(),
        });
    });
};

/* ---------- Comments ---------- */

const commentDraft = ref('');
const submittingComment = ref(false);

const getCsrfToken = () => {
    const match = document.cookie.match(/XSRF-TOKEN=([^;]+)/);
    return match ? decodeURIComponent(match[1]) : '';
};

const addComment = async () => {
    if (!commentDraft.value.trim() || !props.task) return;
    submittingComment.value = true;
    try {
        const res = await fetch(route('tasks.comments.store', props.task.id), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-XSRF-TOKEN': getCsrfToken(),
            },
            body: JSON.stringify({ body: commentDraft.value }),
        });
        const data = await res.json();
        if (detail.value) {
            detail.value.task.comments = [...(detail.value.task.comments ?? []), data.comment];
        }
        commentDraft.value = '';
    } finally {
        submittingComment.value = false;
    }
};

/* ---------- Attachments ---------- */

const uploadForm = useForm({ file: null });
const attachmentFileInput = ref(null);

const uploadAttachment = () => {
    uploadForm.post(route('tasks.attachments.store', props.task.id), {
        forceFormData: true,
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            uploadForm.reset();
            if (attachmentFileInput.value) attachmentFileInput.value.value = '';
            fetchDetail();
        },
    });
};

const toggleVerified = (attachment) => {
    router.patch(
        route('attachments.update', attachment.id),
        { is_verified: !attachment.is_verified },
        { preserveScroll: true, preserveState: true, onSuccess: () => fetchDetail() },
    );
};

const deleteAttachment = (attachment) => {
    askConfirm(`Hapus file "${attachment.file_name}"?`, () => {
        router.delete(route('attachments.destroy', attachment.id), {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => fetchDetail(),
        });
    });
};

const attachments = computed(() => detail.value?.task?.attachments ?? []);
const comments = computed(() => detail.value?.task?.comments ?? []);
const audits = computed(() => detail.value?.audits ?? []);

const TABS = ['detail', 'comments', 'attachments', 'history'];
const tabIndex = computed(() => TABS.indexOf(activeTab.value));

const priorityIndicatorClass = {
    low: 'bg-neutral',
    medium: 'bg-warning',
    high: 'bg-danger',
};
</script>

<template>
    <Modal :show="show" max-width="2xl" @close="close">
        <div class="p-6">
            <div class="mb-4 flex items-center justify-between">
                <div class="font-display text-lg font-semibold">{{ mode === 'edit' ? 'Edit Task' : 'Tambah Task' }}</div>
                <button class="rounded-control p-1 text-secondary transition-colors duration-150 ease-out hover:bg-neutral-tint hover:text-ink" @click="close">
                    <X :size="18" :stroke-width="2.25" />
                </button>
            </div>

            <div v-if="mode === 'edit'" class="relative mb-5 grid grid-cols-4 border-b border-border">
                <button
                    class="flex items-center justify-center gap-1.5 px-2 py-2.5 text-[13px] font-bold transition-colors duration-150 ease-out"
                    :class="activeTab === 'detail' ? 'text-accent-dark' : 'text-secondary hover:text-ink'"
                    @click="activeTab = 'detail'"
                >
                    Detail
                </button>
                <button
                    class="flex items-center justify-center gap-1.5 px-2 py-2.5 text-[13px] font-bold transition-colors duration-150 ease-out"
                    :class="activeTab === 'comments' ? 'text-accent-dark' : 'text-secondary hover:text-ink'"
                    @click="activeTab = 'comments'"
                >
                    <MessageSquare :size="13" :stroke-width="2.25" /> ({{ comments.length }})
                </button>
                <button
                    class="flex items-center justify-center gap-1.5 px-2 py-2.5 text-[13px] font-bold transition-colors duration-150 ease-out"
                    :class="activeTab === 'attachments' ? 'text-accent-dark' : 'text-secondary hover:text-ink'"
                    @click="activeTab = 'attachments'"
                >
                    <Paperclip :size="13" :stroke-width="2.25" /> ({{ attachments.length }})
                </button>
                <button
                    class="flex items-center justify-center gap-1.5 px-2 py-2.5 text-[13px] font-bold transition-colors duration-150 ease-out"
                    :class="activeTab === 'history' ? 'text-accent-dark' : 'text-secondary hover:text-ink'"
                    @click="activeTab = 'history'"
                >
                    <HistoryIcon :size="13" :stroke-width="2.25" />
                </button>

                <span
                    class="absolute bottom-0 h-0.5 w-1/4 bg-accent transition-transform duration-200 ease-in-out"
                    :style="{ transform: `translateX(${tabIndex * 100}%)` }"
                />
            </div>

            <!-- Detail tab -->
            <form v-if="activeTab === 'detail'" class="max-h-[60vh] space-y-4 overflow-y-auto pr-1" @submit.prevent="submit">
                <div>
                    <InputLabel for="title" value="Judul Task" />
                    <TextInput id="title" v-model="form.title" class="mt-1 block w-full" required autofocus />
                    <InputError class="mt-1" :message="form.errors.title" />
                </div>

                <div>
                    <InputLabel value="Status" />
                    <div class="relative mt-1.5 grid grid-cols-3 gap-1 rounded-control bg-neutral-tint p-1">
                        <span
                            class="absolute inset-y-1 w-[calc(33.333%-0.16rem)] rounded-[5px] bg-accent shadow-card transition-transform duration-200 ease-in-out"
                            :style="{ transform: `translateX(${STATUS_OPTIONS.findIndex((o) => o.key === form.status) * 100}%)` }"
                        />
                        <button
                            v-for="opt in STATUS_OPTIONS"
                            :key="opt.key"
                            type="button"
                            class="relative z-10 rounded-[5px] px-3 py-1.5 text-[13px] font-bold transition-colors duration-150 ease-out"
                            :class="form.status === opt.key ? 'text-white' : 'text-secondary hover:text-ink'"
                            @click="form.status = opt.key"
                        >
                            {{ opt.label }}
                        </button>
                    </div>
                    <InputError class="mt-1" :message="form.errors.status" />
                </div>

                <div>
                    <InputLabel value="Prioritas" />
                    <div class="relative mt-1.5 grid grid-cols-3 gap-1 rounded-control bg-neutral-tint p-1">
                        <span
                            class="absolute inset-y-1 w-[calc(33.333%-0.16rem)] rounded-[5px] shadow-card transition-transform duration-200 ease-in-out"
                            :class="priorityIndicatorClass[form.priority]"
                            :style="{ transform: `translateX(${PRIORITY_OPTIONS.findIndex((o) => o.key === form.priority) * 100}%)` }"
                        />
                        <button
                            v-for="opt in PRIORITY_OPTIONS"
                            :key="opt.key"
                            type="button"
                            class="relative z-10 rounded-[5px] px-3 py-1.5 text-[13px] font-bold transition-colors duration-150 ease-out"
                            :class="form.priority === opt.key ? 'text-white' : 'text-secondary hover:text-ink'"
                            @click="form.priority = opt.key"
                        >
                            {{ opt.label }}
                        </button>
                    </div>
                    <InputError class="mt-1" :message="form.errors.priority" />
                </div>

                <div>
                    <InputLabel for="assigned_to" value="Assignee" />
                    <SelectInput id="assigned_to" v-model="form.assigned_to" class="mt-1 block w-full">
                        <option value="">- Belum ditugaskan -</option>
                        <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                    </SelectInput>
                    <InputError class="mt-1" :message="form.errors.assigned_to" />
                </div>

                <div>
                    <InputLabel for="due_date" value="Due Date" />
                    <TextInput id="due_date" type="date" v-model="form.due_date" class="mt-1 block w-full" />
                    <InputError class="mt-1" :message="form.errors.due_date" />
                </div>

                <div>
                    <InputLabel for="description" value="Deskripsi" />
                    <Textarea id="description" v-model="form.description" class="mt-1 block w-full" rows="3" />
                    <InputError class="mt-1" :message="form.errors.description" />
                </div>
            </form>

            <!-- Comments tab -->
            <div v-else-if="activeTab === 'comments'" class="max-h-[60vh] space-y-4 overflow-y-auto pr-1">
                <div v-if="loadingDetail" class="py-8 text-center text-sm text-secondary">Memuat...</div>
                <template v-else>
                    <div v-for="comment in comments" :key="comment.id" class="flex gap-2.5">
                        <Avatar :name="comment.author?.name ?? '-'" :size="28" />
                        <div class="flex-1 rounded-card-sm bg-app px-3.5 py-2.5">
                            <div class="mb-0.5 flex items-center gap-2 text-xs font-bold">
                                {{ comment.author?.name ?? 'Pengguna' }}
                                <span class="font-normal text-secondary">{{ new Date(comment.created_at).toLocaleString('id-ID') }}</span>
                            </div>
                            <div class="text-[13px]">{{ comment.body }}</div>
                        </div>
                    </div>
                    <p v-if="comments.length === 0" class="py-4 text-center text-sm text-secondary">Belum ada komentar.</p>

                    <div class="flex gap-2.5 border-t border-divider pt-4">
                        <TextInput
                            v-model="commentDraft"
                            class="flex-1"
                            placeholder="Tulis komentar..."
                            @keydown.enter.prevent="addComment"
                        />
                        <AppButton variant="primary" :disabled="submittingComment || !commentDraft.trim()" @click="addComment">
                            <Send :size="13" :stroke-width="2.25" /> Kirim
                        </AppButton>
                    </div>
                </template>
            </div>

            <!-- Attachments tab -->
            <div v-else-if="activeTab === 'attachments'" class="max-h-[60vh] space-y-4 overflow-y-auto pr-1">
                <div>
                    <InputLabel value="Upload File (100KB - 500KB)" />
                    <div class="mt-2 flex items-center gap-3">
                        <input
                            ref="attachmentFileInput"
                            type="file"
                            class="text-sm text-secondary"
                            @change="uploadForm.file = $event.target.files[0]"
                        />
                        <AppButton variant="primary" :disabled="uploadForm.processing || !uploadForm.file" @click="uploadAttachment">
                            <Upload :size="13" :stroke-width="2.25" /> Upload
                        </AppButton>
                    </div>
                    <InputError class="mt-2" :message="uploadForm.errors.file" />
                </div>

                <div class="overflow-hidden rounded-card-sm border border-border">
                    <div
                        v-for="attachment in attachments"
                        :key="attachment.id"
                        class="flex items-center gap-3 border-b border-divider px-4 py-3 text-[13px] last:border-b-0"
                    >
                        <a :href="`/storage/${attachment.file_path}`" target="_blank" class="flex flex-1 items-center gap-1.5 font-bold text-accent hover:text-accent-dark">
                            <FileText :size="14" :stroke-width="2.25" class="shrink-0" /> {{ attachment.file_name }}
                        </a>
                        <span class="text-xs text-secondary">{{ attachment.file_size }} KB</span>
                        <button @click="toggleVerified(attachment)">
                            <Badge
                                :label="attachment.is_verified ? 'Terverifikasi' : 'Belum Verifikasi'"
                                :tone="attachment.is_verified ? 'success' : 'neutral'"
                            />
                        </button>
                        <button @click="deleteAttachment(attachment)" class="text-secondary transition-colors duration-150 ease-out hover:text-danger">
                            <Trash2 :size="14" :stroke-width="2.25" />
                        </button>
                    </div>
                    <p v-if="attachments.length === 0" class="py-6 text-center text-sm text-secondary">Belum ada attachment.</p>
                </div>
            </div>

            <!-- History tab -->
            <div v-else-if="activeTab === 'history'" class="max-h-[60vh] overflow-y-auto pr-1">
                <div v-if="loadingDetail" class="py-8 text-center text-sm text-secondary">Memuat...</div>
                <AuditTrail v-else :audits="audits" />
            </div>

            <div class="mt-6 flex items-center justify-between border-t border-divider pt-4">
                <button v-if="mode === 'edit'" class="flex items-center gap-1.5 text-[13px] font-semibold text-danger hover:underline" @click="deleteTask">
                    <Trash2 :size="13" :stroke-width="2.25" /> Hapus Task
                </button>
                <span v-else />

                <div class="flex gap-2.5">
                    <AppButton variant="secondary" @click="close">Batal</AppButton>
                    <AppButton
                        v-if="activeTab === 'detail'"
                        variant="primary"
                        :disabled="form.processing"
                        @click="submit"
                    >
                        Simpan
                    </AppButton>
                </div>
            </div>
        </div>
    </Modal>
</template>
