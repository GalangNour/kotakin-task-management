<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from '@/Components/SelectInput.vue';
import Checkbox from '@/Components/Checkbox.vue';
import AppButton from '@/Components/AppButton.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    user: Object,
    roles: Array,
});

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    password: '',
    password_confirmation: '',
    role_id: props.user.role_id ?? '',
    is_active: props.user.is_active,
});

const submit = () => {
    form.put(route('users.update', props.user.id));
};
</script>

<template>
    <Head title="Edit User" />

    <AuthenticatedLayout eyebrow="Kelola" title="Edit User">
        <div class="max-w-[640px] rounded-card border border-border bg-white p-6">
            <form @submit.prevent="submit" class="space-y-4">
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
                    <InputLabel for="password" value="Password baru (kosongkan jika tidak diubah)" class="!text-xs !font-semibold !text-secondary" />
                    <TextInput id="password" type="password" v-model="form.password" class="mt-1 block w-full" />
                    <InputError class="mt-1" :message="form.errors.password" />
                </div>

                <div>
                    <InputLabel for="password_confirmation" value="Konfirmasi Password" class="!text-xs !font-semibold !text-secondary" />
                    <TextInput id="password_confirmation" type="password" v-model="form.password_confirmation" class="mt-1 block w-full" />
                </div>

                <label class="flex items-center gap-2">
                    <Checkbox v-model:checked="form.is_active" />
                    <span class="text-sm text-secondary">Aktif</span>
                </label>

                <div class="flex items-center gap-3">
                    <AppButton type="submit" variant="primary" :disabled="form.processing">Simpan</AppButton>
                    <AppButton variant="secondary" :href="route('users.index')">Batal</AppButton>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
