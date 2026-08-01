<script setup>
import { watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import Textarea from '@/Components/Textarea.vue';
import SelectInput from '@/Components/SelectInput.vue';
import AppButton from '@/Components/AppButton.vue';
import { useToast } from '@/composables/useToast';
import { X } from '@lucide/vue';

const props = defineProps({
    show: { type: Boolean, default: false },
    mode: { type: String, default: 'create' }, // 'create' | 'edit'
    project: { type: Object, default: null },
    categories: { type: Array, default: () => [] },
});

const emit = defineEmits(['close']);

const { show: showToast } = useToast();

const toDateInput = (value) => (value ? value.slice(0, 10) : '');

const buildFormState = () => ({
    name: props.project?.name ?? '',
    category_id: props.project?.category_id ?? '',
    description: props.project?.description ?? '',
    start_date: toDateInput(props.project?.start_date),
    end_date: toDateInput(props.project?.end_date),
    is_active: props.project?.is_active ?? true,
});

const form = useForm(buildFormState());

watch(
    () => props.show,
    (isOpen) => {
        if (!isOpen) return;
        form.clearErrors();
        Object.assign(form, buildFormState());
    },
);

const close = () => emit('close');

const submit = () => {
    if (!form.name.trim()) {
        showToast('Nama project tidak boleh kosong', 'error');
        return;
    }

    const options = { preserveScroll: true, onSuccess: () => close() };

    if (props.mode === 'edit') {
        form.put(route('projects.update', props.project.id), options);
    } else {
        form.post(route('projects.store'), options);
    }
};
</script>

<template>
    <Modal :show="show" max-width="lg" @close="close">
        <div class="p-6">
            <div class="mb-4 flex items-center justify-between">
                <div class="font-display text-lg font-semibold">{{ mode === 'edit' ? 'Edit Project' : 'Tambah Project' }}</div>
                <button class="rounded-control p-1 text-secondary transition-colors duration-150 ease-out hover:bg-neutral-tint hover:text-ink" @click="close">
                    <X :size="18" :stroke-width="2.25" />
                </button>
            </div>

            <form class="max-h-[70vh] space-y-4 overflow-y-auto pr-1" @submit.prevent="submit">
                <div>
                    <InputLabel for="name" value="Nama Project" />
                    <TextInput id="name" v-model="form.name" class="mt-1 block w-full" required autofocus />
                    <InputError class="mt-1" :message="form.errors.name" />
                </div>

                <div>
                    <InputLabel for="category_id" value="Kategori" />
                    <SelectInput id="category_id" v-model="form.category_id" class="mt-1 block w-full">
                        <option value="">- Tanpa Kategori -</option>
                        <option v-for="category in categories" :key="category.id" :value="category.id">
                            {{ category.name }}
                        </option>
                    </SelectInput>
                    <InputError class="mt-1" :message="form.errors.category_id" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <InputLabel for="start_date" value="Mulai" />
                        <TextInput id="start_date" type="date" v-model="form.start_date" class="mt-1 block w-full" />
                        <InputError class="mt-1" :message="form.errors.start_date" />
                    </div>
                    <div>
                        <InputLabel for="end_date" value="Selesai" />
                        <TextInput id="end_date" type="date" v-model="form.end_date" class="mt-1 block w-full" />
                        <InputError class="mt-1" :message="form.errors.end_date" />
                    </div>
                </div>

                <div>
                    <InputLabel for="description" value="Deskripsi" />
                    <Textarea id="description" v-model="form.description" class="mt-1 block w-full" rows="3" />
                    <InputError class="mt-1" :message="form.errors.description" />
                </div>

                <div>
                    <InputLabel value="Status" />
                    <div class="mt-1">
                        <button
                            type="button"
                            class="rounded-control px-3.5 py-1.5 text-[13px] font-bold transition-colors duration-150 ease-out"
                            :class="form.is_active ? 'bg-success-tint text-success' : 'bg-neutral-tint text-secondary'"
                            @click="form.is_active = !form.is_active"
                        >
                            {{ form.is_active ? 'Aktif' : 'Nonaktif' }}
                        </button>
                    </div>
                    <InputError class="mt-1" :message="form.errors.is_active" />
                </div>
            </form>

            <div class="mt-6 flex items-center justify-end gap-2.5 border-t border-divider pt-4">
                <AppButton variant="secondary" @click="close">Batal</AppButton>
                <AppButton variant="primary" :disabled="form.processing" @click="submit">Simpan</AppButton>
            </div>
        </div>
    </Modal>
</template>
