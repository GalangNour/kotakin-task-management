<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Textarea from '@/Components/Textarea.vue';
import SelectInput from '@/Components/SelectInput.vue';
import Checkbox from '@/Components/Checkbox.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

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

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Tambah Project
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-xl sm:px-6 lg:px-8">
                <div class="bg-white p-6 shadow sm:rounded-lg dark:bg-gray-800">
                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <InputLabel for="name" value="Nama Project" />
                            <TextInput
                                id="name"
                                v-model="form.name"
                                class="mt-1 block w-full"
                                required
                                autofocus
                            />
                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>

                        <div>
                            <InputLabel for="category_id" value="Kategori" />
                            <SelectInput id="category_id" v-model="form.category_id" class="mt-1 block w-full">
                                <option value="">- Tanpa Kategori -</option>
                                <option v-for="category in categories" :key="category.id" :value="category.id">
                                    {{ category.name }}
                                </option>
                            </SelectInput>
                            <InputError class="mt-2" :message="form.errors.category_id" />
                        </div>

                        <div>
                            <InputLabel for="description" value="Deskripsi" />
                            <Textarea id="description" v-model="form.description" class="mt-1 block w-full" rows="3" />
                            <InputError class="mt-2" :message="form.errors.description" />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <InputLabel for="start_date" value="Mulai" />
                                <TextInput
                                    id="start_date"
                                    type="date"
                                    v-model="form.start_date"
                                    class="mt-1 block w-full"
                                />
                                <InputError class="mt-2" :message="form.errors.start_date" />
                            </div>
                            <div>
                                <InputLabel for="end_date" value="Selesai" />
                                <TextInput
                                    id="end_date"
                                    type="date"
                                    v-model="form.end_date"
                                    class="mt-1 block w-full"
                                />
                                <InputError class="mt-2" :message="form.errors.end_date" />
                            </div>
                        </div>

                        <label class="flex items-center gap-2">
                            <Checkbox v-model:checked="form.is_active" />
                            <span class="text-sm text-gray-600 dark:text-gray-400">Aktif</span>
                        </label>

                        <div class="flex items-center gap-3">
                            <PrimaryButton :disabled="form.processing">Simpan</PrimaryButton>
                            <Link :href="route('projects.index')">
                                <SecondaryButton>Batal</SecondaryButton>
                            </Link>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
