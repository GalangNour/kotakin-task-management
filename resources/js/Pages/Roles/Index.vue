<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import Badge from '@/Components/Badge.vue';
import AppButton from '@/Components/AppButton.vue';
import SearchInput from '@/Components/SearchInput.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    roles: Object,
    filters: Object,
});

const search = ref(props.filters.search ?? '');

const applySearch = () => {
    router.get(
        route('roles.index'),
        { search: search.value || undefined },
        { preserveState: true, replace: true },
    );
};

const destroy = (role) => {
    if (confirm(`Hapus role "${role.name}"?`)) {
        router.delete(route('roles.destroy', role.id));
    }
};
</script>

<template>
    <Head title="Role Management" />

    <AuthenticatedLayout eyebrow="Kelola" title="Roles">
        <template #actions>
            <SearchInput v-model="search" placeholder="Cari role..." @keyup.enter="applySearch" />
            <AppButton variant="primary" :href="route('roles.create')">+ Tambah Role</AppButton>
        </template>

        <div class="grid gap-[18px]" style="grid-template-columns: repeat(auto-fill, minmax(300px, 1fr))">
            <div
                v-for="role in roles.data"
                :key="role.id"
                class="rounded-card border border-border bg-white p-5"
            >
                <div class="mb-2.5 flex items-center justify-between">
                    <span class="text-base font-extrabold">{{ role.name }}</span>
                    <Badge :label="role.is_active ? 'Aktif' : 'Nonaktif'" :tone="role.is_active ? 'success' : 'neutral'" />
                </div>
                <div class="mb-3.5 text-xs text-secondary">{{ role.users_count }} user</div>
                <div class="mb-4 flex flex-wrap gap-1.5">
                    <span
                        v-for="perm in role.permissions ?? []"
                        :key="perm"
                        class="rounded-chip bg-neutral-tint px-2.5 py-1 text-xs text-ink/80"
                    >
                        {{ perm }}
                    </span>
                    <span v-if="(role.permissions ?? []).length === 0" class="text-xs text-secondary">
                        Tidak ada permission.
                    </span>
                </div>
                <div class="flex items-center gap-3.5 text-[13px] font-semibold">
                    <Link :href="route('roles.edit', role.id)" class="text-ink hover:text-accent">Edit Role</Link>
                    <button @click="destroy(role)" class="text-danger hover:underline">Hapus</button>
                </div>
            </div>

            <p v-if="roles.data.length === 0" class="col-span-full py-10 text-center text-sm text-secondary">
                Belum ada role.
            </p>
        </div>

        <Pagination :links="roles.links" />
    </AuthenticatedLayout>
</template>
