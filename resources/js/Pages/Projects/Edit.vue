<script setup>
import { onMounted, ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ProjectDetailTabs from '@/Components/ProjectDetailTabs.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import Textarea from '@/Components/Textarea.vue';
import SelectInput from '@/Components/SelectInput.vue';
import AppButton from '@/Components/AppButton.vue';
import AuditTrail from '@/Components/AuditTrail.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    project: Object,
    categories: Array,
    audits: Array,
});

const toDateInput = (value) => (value ? value.slice(0, 10) : '');

const form = useForm({
    name: props.project.name,
    category_id: props.project.category_id ?? '',
    description: props.project.description ?? '',
    start_date: toDateInput(props.project.start_date),
    end_date: toDateInput(props.project.end_date),
    is_active: props.project.is_active ? '1' : '0',
});

const submit = () => {
    form.transform((data) => ({ ...data, is_active: data.is_active === '1' })).put(
        route('projects.update', props.project.id),
    );
};

const activeTab = ref('detail');

onMounted(() => {
    if (new URLSearchParams(window.location.search).get('tab') === 'history') {
        activeTab.value = 'history';
    }
});
</script>

<template>
    <Head :title="`Edit Project — ${project.name}`" />

    <AuthenticatedLayout eyebrow="Project" title="Detail">
        <ProjectDetailTabs :project="project" :active="activeTab === 'history' ? 'history' : 'detail'" />

        <div v-if="activeTab === 'detail'" class="max-w-[640px] rounded-card border border-border bg-white p-6">
            <form @submit.prevent="submit" class="space-y-5">
                <div>
                    <InputLabel for="name" value="Nama Project" class="!text-xs !font-semibold !text-secondary" />
                    <TextInput id="name" v-model="form.name" class="mt-1 block w-full" required autofocus />
                    <InputError class="mt-1" :message="form.errors.name" />
                </div>

                <div class="grid grid-cols-2 gap-5">
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
                        <div class="mb-1 text-xs font-semibold text-secondary">Dibuat oleh</div>
                        <div class="text-sm font-semibold">{{ project.creator?.name ?? '-' }}</div>
                    </div>

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

                    <div>
                        <InputLabel for="is_active" value="Status" class="!text-xs !font-semibold !text-secondary" />
                        <SelectInput id="is_active" v-model="form.is_active" class="mt-1 block w-full">
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </SelectInput>
                        <InputError class="mt-1" :message="form.errors.is_active" />
                    </div>

                    <div>
                        <div class="mb-1 text-xs font-semibold text-secondary">Total Task</div>
                        <div class="text-sm font-semibold">{{ project.tasks_count }}</div>
                    </div>
                </div>

                <div>
                    <InputLabel for="description" value="Deskripsi" class="!text-xs !font-semibold !text-secondary" />
                    <Textarea id="description" v-model="form.description" class="mt-1 block w-full" rows="4" />
                    <InputError class="mt-1" :message="form.errors.description" />
                </div>

                <div class="flex items-center gap-3">
                    <AppButton type="submit" variant="primary" :disabled="form.processing">Simpan</AppButton>
                    <AppButton variant="secondary" :href="route('projects.index')">Batal</AppButton>
                </div>
            </form>
        </div>

        <div v-else class="max-w-[640px] rounded-card border border-border bg-white p-6">
            <AuditTrail :audits="audits" />
        </div>
    </AuthenticatedLayout>
</template>
