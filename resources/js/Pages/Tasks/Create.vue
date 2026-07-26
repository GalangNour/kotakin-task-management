<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import Textarea from '@/Components/Textarea.vue';
import SelectInput from '@/Components/SelectInput.vue';
import AppButton from '@/Components/AppButton.vue';
import { Head, useForm } from '@inertiajs/vue3';

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

    <AuthenticatedLayout eyebrow="Project" :title="`Tambah Task — ${project.name}`">
        <div class="max-w-[640px] rounded-card border border-border bg-white p-6">
            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <InputLabel for="title" value="Judul Task" class="!text-xs !font-semibold !text-secondary" />
                    <TextInput id="title" v-model="form.title" class="mt-1 block w-full" required autofocus />
                    <InputError class="mt-1" :message="form.errors.title" />
                </div>

                <div>
                    <InputLabel for="description" value="Deskripsi" class="!text-xs !font-semibold !text-secondary" />
                    <Textarea id="description" v-model="form.description" class="mt-1 block w-full" rows="3" />
                    <InputError class="mt-1" :message="form.errors.description" />
                </div>

                <div>
                    <InputLabel for="assigned_to" value="Assignee" class="!text-xs !font-semibold !text-secondary" />
                    <SelectInput id="assigned_to" v-model="form.assigned_to" class="mt-1 block w-full">
                        <option value="">- Belum ditugaskan -</option>
                        <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                    </SelectInput>
                    <InputError class="mt-1" :message="form.errors.assigned_to" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <InputLabel for="status" value="Status" class="!text-xs !font-semibold !text-secondary" />
                        <SelectInput id="status" v-model="form.status" class="mt-1 block w-full">
                            <option value="todo">Todo</option>
                            <option value="in_progress">In Progress</option>
                            <option value="done">Done</option>
                        </SelectInput>
                        <InputError class="mt-1" :message="form.errors.status" />
                    </div>
                    <div>
                        <InputLabel for="priority" value="Prioritas" class="!text-xs !font-semibold !text-secondary" />
                        <SelectInput id="priority" v-model="form.priority" class="mt-1 block w-full">
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                        </SelectInput>
                        <InputError class="mt-1" :message="form.errors.priority" />
                    </div>
                </div>

                <div>
                    <InputLabel for="due_date" value="Due Date" class="!text-xs !font-semibold !text-secondary" />
                    <TextInput id="due_date" type="date" v-model="form.due_date" class="mt-1 block w-full" />
                    <InputError class="mt-1" :message="form.errors.due_date" />
                </div>

                <div class="flex items-center gap-3">
                    <AppButton type="submit" variant="primary" :disabled="form.processing">Simpan</AppButton>
                    <AppButton variant="secondary" :href="route('projects.tasks.index', project.id)">Batal</AppButton>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
