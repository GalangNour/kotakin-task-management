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
import Checkbox from '@/Components/Checkbox.vue';
import AuditTrail from '@/Components/AuditTrail.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    project: Object,
    categories: Array,
    audits: Array,
});

const toDateInput = (value) => (value ? value.slice(0, 10) : '');

const form = useForm({
    category_id: props.project.category_id ?? '',
    name: props.project.name,
    description: props.project.description ?? '',
    start_date: toDateInput(props.project.start_date),
    end_date: toDateInput(props.project.end_date),
    is_active: props.project.is_active,
});

const submit = () => {
    form.put(route('projects.update', props.project.id));
};

const activeTab = ref('form');
</script>

<template>
    <Head title="Edit Project" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Edit Project — {{ project.name }}
            </h2>
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

                        <p class="text-xs text-gray-400">
                            Dibuat oleh {{ project.creator?.name ?? '-' }}
                        </p>

                        <div class="flex items-center gap-3">
                            <PrimaryButton :disabled="form.processing">Simpan</PrimaryButton>
                            <Link :href="route('projects.index')">
                                <SecondaryButton>Batal</SecondaryButton>
                            </Link>
                        </div>
                    </form>
                </div>

                <div v-else class="bg-white p-6 shadow sm:rounded-lg dark:bg-gray-800">
                    <AuditTrail :audits="audits" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
