<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Textarea from '@/Components/Textarea.vue';
import SelectInput from '@/Components/SelectInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    project: Object,
    users: Array,
});

const form = useForm({
    title: '',
    description: '',
    status: 'todo',
    priority: 'medium',
    due_date: '',
    assigned_to: '',
});

const submit = () => {
    form.post(route('projects.tasks.store', props.project.id));
};
</script>

<template>
    <Head title="Tambah Task" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Tambah Task — {{ project.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-xl sm:px-6 lg:px-8">
                <div class="bg-white p-6 shadow sm:rounded-lg dark:bg-gray-800">
                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <InputLabel for="title" value="Judul Task" />
                            <TextInput
                                id="title"
                                v-model="form.title"
                                class="mt-1 block w-full"
                                required
                                autofocus
                            />
                            <InputError class="mt-2" :message="form.errors.title" />
                        </div>

                        <div>
                            <InputLabel for="description" value="Deskripsi" />
                            <Textarea id="description" v-model="form.description" class="mt-1 block w-full" rows="3" />
                            <InputError class="mt-2" :message="form.errors.description" />
                        </div>

                        <div>
                            <InputLabel for="assigned_to" value="Assignee" />
                            <SelectInput id="assigned_to" v-model="form.assigned_to" class="mt-1 block w-full">
                                <option value="">- Belum ditugaskan -</option>
                                <option v-for="user in users" :key="user.id" :value="user.id">
                                    {{ user.name }}
                                </option>
                            </SelectInput>
                            <InputError class="mt-2" :message="form.errors.assigned_to" />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <InputLabel for="status" value="Status" />
                                <SelectInput id="status" v-model="form.status" class="mt-1 block w-full">
                                    <option value="todo">Todo</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="done">Done</option>
                                </SelectInput>
                                <InputError class="mt-2" :message="form.errors.status" />
                            </div>
                            <div>
                                <InputLabel for="priority" value="Prioritas" />
                                <SelectInput id="priority" v-model="form.priority" class="mt-1 block w-full">
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                </SelectInput>
                                <InputError class="mt-2" :message="form.errors.priority" />
                            </div>
                        </div>

                        <div>
                            <InputLabel for="due_date" value="Due Date" />
                            <TextInput
                                id="due_date"
                                type="date"
                                v-model="form.due_date"
                                class="mt-1 block w-full"
                            />
                            <InputError class="mt-2" :message="form.errors.due_date" />
                        </div>

                        <div class="flex items-center gap-3">
                            <PrimaryButton :disabled="form.processing">Simpan</PrimaryButton>
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
