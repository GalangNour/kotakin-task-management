<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import Checkbox from '@/Components/Checkbox.vue';
import AppButton from '@/Components/AppButton.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    is_active: true,
    permissions: '',
});

const submit = () => {
    form.transform((data) => ({
        ...data,
        permissions: data.permissions
            ? data.permissions.split(',').map((p) => p.trim()).filter(Boolean)
            : [],
    })).post(route('roles.store'));
};
</script>

<template>
    <Head title="Tambah Role" />

    <AuthenticatedLayout eyebrow="Kelola" title="Tambah Role">
        <div class="max-w-[640px] rounded-card border border-border bg-white p-6">
            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <InputLabel for="name" value="Nama Role" class="!text-xs !font-semibold !text-secondary" />
                    <TextInput id="name" v-model="form.name" class="mt-1 block w-full" required autofocus />
                    <InputError class="mt-1" :message="form.errors.name" />
                </div>

                <div>
                    <InputLabel for="permissions" value="Permissions (pisah koma)" class="!text-xs !font-semibold !text-secondary" />
                    <TextInput id="permissions" v-model="form.permissions" class="mt-1 block w-full" placeholder="task.view, task.update" />
                    <InputError class="mt-1" :message="form.errors.permissions" />
                </div>

                <label class="flex items-center gap-2">
                    <Checkbox v-model:checked="form.is_active" />
                    <span class="text-sm text-secondary">Aktif</span>
                </label>

                <div class="flex items-center gap-3">
                    <AppButton type="submit" variant="primary" :disabled="form.processing">Simpan</AppButton>
                    <AppButton variant="secondary" :href="route('roles.index')">Batal</AppButton>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
