<script setup>
import { watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from '@/Components/SelectInput.vue';
import AppButton from '@/Components/AppButton.vue';
import { useToast } from '@/composables/useToast';

const props = defineProps({
    show: { type: Boolean, default: false },
    mode: { type: String, default: 'create' }, // 'create' | 'edit'
    user: { type: Object, default: null },
    roles: { type: Array, default: () => [] },
});

const emit = defineEmits(['close']);

const { show: showToast } = useToast();

const buildFormState = () => ({
    name: props.user?.name ?? '',
    email: props.user?.email ?? '',
    role_id: props.user?.role_id ?? '',
    password: '',
    password_confirmation: '',
    is_active: props.user?.is_active ?? true,
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
    if (!form.name.trim() || !form.email.trim()) {
        showToast('Nama dan email wajib diisi', 'error');
        return;
    }

    const options = { preserveScroll: true, onSuccess: () => close() };

    if (props.mode === 'edit') {
        form.put(route('users.update', props.user.id), options);
    } else {
        form.post(route('users.store'), options);
    }
};
</script>

<template>
    <Modal :show="show" max-width="lg" @close="close">
        <div class="p-6">
            <div class="mb-4 flex items-center justify-between">
                <div class="text-lg font-extrabold">{{ mode === 'edit' ? 'Edit User' : 'Tambah User' }}</div>
                <button class="text-xl leading-none text-secondary hover:text-ink" @click="close">&times;</button>
            </div>

            <form class="max-h-[70vh] space-y-4 overflow-y-auto pr-1" @submit.prevent="submit">
                <div>
                    <InputLabel for="name" value="Nama" class="!text-xs !font-semibold !text-secondary" />
                    <TextInput id="name" v-model="form.name" class="mt-1 block w-full" required autofocus />
                    <InputError class="mt-1" :message="form.errors.name" />
                </div>

                <div>
                    <InputLabel for="email" value="Email" class="!text-xs !font-semibold !text-secondary" />
                    <TextInput id="email" type="email" v-model="form.email" class="mt-1 block w-full" required />
                    <InputError class="mt-1" :message="form.errors.email" />
                </div>

                <div>
                    <InputLabel for="role_id" value="Role" class="!text-xs !font-semibold !text-secondary" />
                    <SelectInput id="role_id" v-model="form.role_id" class="mt-1 block w-full">
                        <option value="">- Pilih Role -</option>
                        <option v-for="role in roles" :key="role.id" :value="role.id">{{ role.name }}</option>
                    </SelectInput>
                    <InputError class="mt-1" :message="form.errors.role_id" />
                </div>

                <div>
                    <InputLabel
                        for="password"
                        :value="mode === 'edit' ? 'Password baru (kosongkan jika tidak diubah)' : 'Password'"
                        class="!text-xs !font-semibold !text-secondary"
                    />
                    <TextInput id="password" type="password" v-model="form.password" class="mt-1 block w-full" :required="mode === 'create'" />
                    <InputError class="mt-1" :message="form.errors.password" />
                </div>

                <div>
                    <InputLabel for="password_confirmation" value="Konfirmasi Password" class="!text-xs !font-semibold !text-secondary" />
                    <TextInput
                        id="password_confirmation"
                        type="password"
                        v-model="form.password_confirmation"
                        class="mt-1 block w-full"
                        :required="mode === 'create'"
                    />
                </div>

                <div>
                    <InputLabel value="Status" class="!text-xs !font-semibold !text-secondary" />
                    <div class="mt-1">
                        <button
                            type="button"
                            class="rounded-control px-3.5 py-1.5 text-[13px] font-bold transition"
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
