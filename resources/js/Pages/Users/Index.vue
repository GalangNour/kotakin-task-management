<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';

const props = defineProps({
    users: Object,
    filters: Object,
});

const page = usePage();
const currentUserId = page.props.auth.user.id;

const search = ref(props.filters.search ?? '');

const applySearch = () => {
    router.get(
        route('users.index'),
        { search: search.value || undefined },
        { preserveState: true, replace: true },
    );
};

const destroy = (user) => {
    if (confirm(`Hapus user "${user.name}"?`)) {
        router.delete(route('users.destroy', user.id));
    }
};
</script>

<template>
    <Head title="User Account" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                User Account
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <TextInput
                        v-model="search"
                        type="text"
                        placeholder="Cari nama/email..."
                        class="w-64"
                        @keyup.enter="applySearch"
                    />
                    <Link :href="route('users.create')">
                        <PrimaryButton>Tambah User</PrimaryButton>
                    </Link>
                </div>

                <div class="overflow-x-auto rounded-lg bg-white shadow dark:bg-gray-800">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b border-gray-200 text-xs uppercase text-gray-500 dark:border-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-4 py-3">Nama</th>
                                <th class="px-4 py-3">Email</th>
                                <th class="px-4 py-3">Role</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr v-for="user in users.data" :key="user.id">
                                <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-200">
                                    {{ user.name }}
                                </td>
                                <td class="px-4 py-3 text-gray-500 dark:text-gray-400">
                                    {{ user.email }}
                                </td>
                                <td class="px-4 py-3">{{ user.role?.name ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <span
                                        class="rounded-full px-2 py-0.5 text-xs font-semibold"
                                        :class="user.is_active
                                            ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                                            : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300'"
                                    >
                                        {{ user.is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <Link
                                        :href="route('users.edit', user.id)"
                                        class="me-3 text-indigo-600 hover:underline dark:text-indigo-400"
                                    >
                                        Edit
                                    </Link>
                                    <button
                                        v-if="user.id !== currentUserId"
                                        @click="destroy(user)"
                                        class="text-red-600 hover:underline dark:text-red-400"
                                    >
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="users.data.length === 0">
                                <td colspan="5" class="px-4 py-6 text-center text-gray-400">
                                    Belum ada user.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <Pagination :links="users.links" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
