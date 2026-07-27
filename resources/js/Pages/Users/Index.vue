<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import Avatar from '@/Components/Avatar.vue';
import Badge from '@/Components/Badge.vue';
import AppButton from '@/Components/AppButton.vue';
import SearchInput from '@/Components/SearchInput.vue';
import UserModal from '@/Pages/Users/UserModal.vue';
import { useLiveSearch } from '@/composables/useLiveSearch';
import { useConfirm } from '@/composables/useConfirm';
import { Head, router, usePage } from '@inertiajs/vue3';

const props = defineProps({
    users: Object,
    roles: Array,
    filters: Object,
});

const page = usePage();
const currentUserId = page.props.auth.user.id;

const search = useLiveSearch(route('users.index'), props.filters.search);
const { askConfirm } = useConfirm();

const destroy = (user) => {
    askConfirm(`Hapus user "${user.name}"?`, () => {
        router.delete(route('users.destroy', user.id));
    });
};

const userModal = ref(null); // null | { mode, user }

const openCreateUser = () => {
    userModal.value = { mode: 'create', user: null };
};

const openEditUser = (user) => {
    userModal.value = { mode: 'edit', user };
};

const closeUserModal = () => {
    userModal.value = null;
};
</script>

<template>
    <Head title="User Account" />

    <AuthenticatedLayout eyebrow="Kelola" title="User Account">
        <template #actions>
            <SearchInput v-model="search" placeholder="Cari nama/email..." />
            <AppButton variant="primary" @click="openCreateUser">+ Tambah User</AppButton>
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
                    <button @click="openEditUser(user)" class="text-ink hover:text-accent">Edit</button>
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

        <UserModal
            :show="!!userModal"
            :mode="userModal?.mode ?? 'create'"
            :user="userModal?.user ?? null"
            :roles="roles"
            @close="closeUserModal"
        />
    </AuthenticatedLayout>
</template>
