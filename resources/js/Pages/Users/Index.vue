<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import Avatar from '@/Components/Avatar.vue';
import Badge from '@/Components/Badge.vue';
import AppButton from '@/Components/AppButton.vue';
import SearchInput from '@/Components/SearchInput.vue';
import { useLiveSearch } from '@/composables/useLiveSearch';
import { Head, Link, router, usePage } from '@inertiajs/vue3';

const props = defineProps({
    users: Object,
    filters: Object,
});

const page = usePage();
const currentUserId = page.props.auth.user.id;

const search = useLiveSearch(route('users.index'), props.filters.search);

const destroy = (user) => {
    if (confirm(`Hapus user "${user.name}"?`)) {
        router.delete(route('users.destroy', user.id));
    }
};
</script>

<template>
    <Head title="User Account" />

    <AuthenticatedLayout eyebrow="Kelola" title="User Account">
        <template #actions>
            <SearchInput v-model="search" placeholder="Cari nama/email..." />
            <AppButton variant="primary" :href="route('users.create')">+ Tambah User</AppButton>
        </template>

        <div class="overflow-hidden rounded-card border border-border bg-white">
            <div
                v-for="user in users.data"
                :key="user.id"
                class="flex items-center gap-3.5 border-b border-divider px-5 py-4 last:border-b-0"
            >
                <Avatar :name="user.name" variant="accent" :size="36" />
                <div class="min-w-0 flex-1">
                    <div class="text-sm font-bold">{{ user.name }}</div>
                    <div class="text-xs text-secondary">{{ user.email }}</div>
                </div>
                <Badge :label="user.role?.name ?? '-'" tone="neutral" />
                <Badge :label="user.is_active ? 'Aktif' : 'Nonaktif'" :tone="user.is_active ? 'success' : 'neutral'" />
                <div class="flex w-24 justify-end gap-3.5 text-[13px] font-semibold">
                    <Link :href="route('users.edit', user.id)" class="text-ink hover:text-accent">Edit</Link>
                    <button v-if="user.id !== currentUserId" @click="destroy(user)" class="text-danger hover:underline">
                        Hapus
                    </button>
                </div>
            </div>

            <p v-if="users.data.length === 0" class="py-10 text-center text-sm text-secondary">
                Belum ada user.
            </p>
        </div>

        <Pagination :links="users.links" />
    </AuthenticatedLayout>
</template>
