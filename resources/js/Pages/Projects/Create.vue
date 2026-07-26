<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import Textarea from '@/Components/Textarea.vue';
import SelectInput from '@/Components/SelectInput.vue';
import Checkbox from '@/Components/Checkbox.vue';
import AppButton from '@/Components/AppButton.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    categories: Array,
});

const form = useForm({
    category_id: '',
    name: '',
    description: '',
    start_date: '',
    end_date: '',
    is_active: true,
});

const submit = () => {
    form.post(route('projects.store'));
};
</script>

<template>
    <Head title="Tambah Project" />

    <AuthenticatedLayout eyebrow="Kelola" title="Tambah Project">
        <div class="max-w-[640px] rounded-card border border-border bg-white p-6">
            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <InputLabel for="name" value="Nama Project" class="!text-xs !font-semibold !text-secondary" />
                    <TextInput id="name" v-model="form.name" class="mt-1 block w-full" required autofocus />
                    <InputError class="mt-1" :message="form.errors.name" />
                </div>

                <div>
                    <InputLabel for="category_id" value="Kategori" class="!text-xs !font-semibold !text-secondary" />
                    <SelectInput id="category_id" v-model="form.category_id" class="mt-1 block w-full">
                        <option value="">- Tanpa Kategori -</option>
                        <option v-for="category in categories" :key="category.id" :value="category.id">
                            {{ category.name }}
                        </option>
                    </SelectInput>
                    <InputError class="mt-1" :message="form.errors.category_id" />
                </div>

                <div>
                    <InputLabel for="description" value="Deskripsi" class="!text-xs !font-semibold !text-secondary" />
                    <Textarea id="description" v-model="form.description" class="mt-1 block w-full" rows="3" />
                    <InputError class="mt-1" :message="form.errors.description" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <InputLabel for="start_date" value="Mulai" class="!text-xs !font-semibold !text-secondary" />
                        <TextInput id="start_date" type="date" v-model="form.start_date" class="mt-1 block w-full" />
                        <InputError class="mt-1" :message="form.errors.start_date" />
                    </div>
                    <div>
                        <InputLabel for="end_date" value="Selesai" class="!text-xs !font-semibold !text-secondary" />
                        <TextInput id="end_date" type="date" v-model="form.end_date" class="mt-1 block w-full" />
                        <InputError class="mt-1" :message="form.errors.end_date" />
                    </div>
                </div>

                <label class="flex items-center gap-2">
                    <Checkbox v-model:checked="form.is_active" />
                    <span class="text-sm text-secondary">Aktif</span>
                </label>

                <div class="flex items-center gap-3">
                    <AppButton type="submit" variant="primary" :disabled="form.processing">Simpan</AppButton>
                    <AppButton variant="secondary" :href="route('projects.index')">Batal</AppButton>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
