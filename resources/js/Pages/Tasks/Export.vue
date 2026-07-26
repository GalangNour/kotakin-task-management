<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    project: Object,
    availableColumns: Object,
});

const form = useForm({
    columns: Object.keys(props.availableColumns),
});

const submit = () => {
    form.post(route('projects.tasks.export.store', props.project.id));
};
</script>

<template>
    <Head title="Export Task" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Export Task — {{ project.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-xl sm:px-6 lg:px-8">
                <div class="bg-white p-6 shadow sm:rounded-lg dark:bg-gray-800">
                    <form @submit.prevent="submit" class="space-y-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Pilih kolom yang ingin diikutkan dalam file export.
                        </p>

                        <div class="space-y-2">
                            <label
                                v-for="(label, key) in availableColumns"
                                :key="key"
                                class="flex items-center gap-2"
                            >
                                <input
                                    type="checkbox"
                                    :value="key"
                                    v-model="form.columns"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900"
                                />
                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ label }}</span>
                            </label>
                        </div>
                        <InputError :message="form.errors.columns" />

                        <div class="flex items-center gap-3">
                            <PrimaryButton :disabled="form.processing || form.columns.length === 0">
                                Mulai Export
                            </PrimaryButton>
                            <Link :href="route('projects.tasks.index', project.id)">
                                <SecondaryButton>Batal</SecondaryButton>
                            </Link>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
