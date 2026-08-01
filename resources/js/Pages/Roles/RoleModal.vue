<script setup>
import { watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import AppButton from '@/Components/AppButton.vue';
import { useToast } from '@/composables/useToast';
import { X, Check } from '@lucide/vue';

const props = defineProps({
    show: { type: Boolean, default: false },
    mode: { type: String, default: 'create' }, // 'create' | 'edit'
    role: { type: Object, default: null },
    availablePermissions: { type: Object, required: true },
});

const emit = defineEmits(['close']);

const { show: showToast } = useToast();

const buildFormState = () => ({
    name: props.role?.name ?? '',
    is_active: props.role?.is_active ?? true,
    permissions: [...(props.role?.permissions ?? [])],
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

const togglePermission = (key) => {
    form.permissions = form.permissions.includes(key)
        ? form.permissions.filter((p) => p !== key)
        : [...form.permissions, key];
};

const submit = () => {
    if (!form.name.trim()) {
        showToast('Nama role tidak boleh kosong', 'error');
        return;
    }

    const options = { preserveScroll: true, onSuccess: () => close() };

    if (props.mode === 'edit') {
        form.put(route('roles.update', props.role.id), options);
    } else {
        form.post(route('roles.store'), options);
    }
};
</script>

<template>
    <Modal :show="show" max-width="lg" @close="close">
        <div class="p-6">
            <div class="mb-4 flex items-center justify-between">
                <div class="font-display text-lg font-semibold">{{ mode === 'edit' ? 'Edit Role' : 'Tambah Role' }}</div>
                <button class="rounded-control p-1 text-secondary transition-colors duration-150 ease-out hover:bg-neutral-tint hover:text-ink" @click="close">
                    <X :size="18" :stroke-width="2.25" />
                </button>
            </div>

            <form class="max-h-[70vh] space-y-4 overflow-y-auto pr-1" @submit.prevent="submit">
                <div>
                    <InputLabel for="name" value="Nama Role" />
                    <TextInput id="name" v-model="form.name" class="mt-1 block w-full" required autofocus />
                    <InputError class="mt-1" :message="form.errors.name" />
                </div>

                <div>
                    <InputLabel value="Akses / Permissions" />
                    <div class="mt-2 overflow-hidden rounded-card-sm border border-border">
                        <button
                            v-for="(label, key) in availablePermissions"
                            :key="key"
                            type="button"
                            class="flex min-h-[44px] w-full items-center justify-between border-b border-divider px-3.5 text-left text-[13px] font-semibold transition-colors duration-150 ease-out last:border-b-0"
                            :class="form.permissions.includes(key) ? 'bg-accent-tint text-accent-dark' : 'text-secondary hover:bg-neutral-tint'"
                            @click="togglePermission(key)"
                        >
                            <span>{{ label }}</span>
                            <Check v-if="form.permissions.includes(key)" :size="15" :stroke-width="2.75" />
                        </button>
                    </div>
                    <InputError class="mt-1" :message="form.errors.permissions" />
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
                </div>
            </form>

            <div class="mt-6 flex items-center justify-end gap-2.5 border-t border-divider pt-4">
                <AppButton variant="secondary" @click="close">Batal</AppButton>
                <AppButton variant="primary" :disabled="form.processing" @click="submit">Simpan</AppButton>
            </div>
        </div>
    </Modal>
</template>
