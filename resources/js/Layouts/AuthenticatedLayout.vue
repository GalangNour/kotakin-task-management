<script setup>
import { computed, watch } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { LayoutDashboard, FolderKanban, Users as UsersIcon, ShieldCheck, LogOut, UserRound } from '@lucide/vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Avatar from '@/Components/Avatar.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import Toast from '@/Components/Toast.vue';
import ConfirmDialog from '@/Components/ConfirmDialog.vue';
import { useToast } from '@/composables/useToast';

defineProps({
    eyebrow: {
        type: String,
        default: '',
    },
    title: {
        type: String,
        default: '',
    },
});

const page = usePage();
const user = computed(() => page.props.auth.user);
const isAdministrator = computed(() => user.value?.role?.name === 'Administrator');
const flashSuccess = computed(() => page.props.flash?.success);
const flashError = computed(() => page.props.flash?.error);

const { show: showToast } = useToast();

watch(flashSuccess, (message) => {
    if (message) showToast(message, 'success');
}, { immediate: true });

watch(flashError, (message) => {
    if (message) showToast(message, 'error');
}, { immediate: true });

const navItems = computed(() => {
    const items = [
        { key: 'dashboard', label: 'Dashboard', icon: LayoutDashboard, href: route('dashboard'), active: route().current('dashboard') },
        {
            key: 'projects',
            label: 'Projects',
            icon: FolderKanban,
            href: route('projects.index'),
            active: route().current('projects.*') || route().current('project-categories.*'),
        },
    ];

    if (isAdministrator.value) {
        items.push({ key: 'users', label: 'Users', icon: UsersIcon, href: route('users.index'), active: route().current('users.*') });
    }

    items.push({ key: 'roles', label: 'Roles', icon: ShieldCheck, href: route('roles.index'), active: route().current('roles.*') });

    return items;
});
</script>

<template>
    <div class="flex min-h-screen bg-app font-sans text-ink">
        <aside class="sticky top-0 flex h-screen w-[240px] shrink-0 flex-col border-r border-border bg-sidebar p-4">
            <div class="mb-7 flex items-center gap-2.5 px-1">
                <ApplicationLogo :size="34" />
                <span class="font-display text-[15px] font-semibold tracking-tight">Task Management</span>
            </div>

            <nav class="flex flex-col gap-0.5">
                <Link
                    v-for="item in navItems"
                    :key="item.key"
                    :href="item.href"
                    class="flex items-center gap-2.5 rounded-control px-3 py-2.5 text-sm font-bold transition-colors duration-150 ease-out"
                    :class="item.active ? 'bg-accent-tint text-accent-dark' : 'text-secondary hover:bg-black/[0.03]'"
                >
                    <component :is="item.icon" :size="17" :stroke-width="2.25" />
                    {{ item.label }}
                </Link>
            </nav>

            <div class="flex-1"></div>

            <Dropdown align="left" width="48" direction="up" content-classes="py-1 bg-white">
                <template #trigger>
                    <div class="flex cursor-pointer items-center gap-2.5 border-t border-border px-1 pb-1 pt-3 transition-opacity duration-150 ease-out hover:opacity-70">
                        <Avatar :name="user.name" variant="accent" :size="32" />
                        <div class="min-w-0">
                            <div class="truncate text-[13px] font-bold">{{ user.name }}</div>
                            <div class="text-xs text-secondary">{{ user.role?.name ?? '-' }}</div>
                        </div>
                    </div>
                </template>
                <template #content>
                    <DropdownLink :href="route('profile.edit')" class="flex items-center gap-2">
                        <UserRound :size="15" :stroke-width="2.25" /> Profile
                    </DropdownLink>
                    <DropdownLink :href="route('logout')" method="post" as="button" class="flex items-center gap-2">
                        <LogOut :size="15" :stroke-width="2.25" /> Log Out
                    </DropdownLink>
                </template>
            </Dropdown>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col">
            <header class="sticky top-0 z-10 flex items-center justify-between border-b border-border bg-app px-9 py-[22px]">
                <div>
                    <div v-if="eyebrow" class="mb-0.5 text-xs font-semibold uppercase tracking-wide text-secondary">{{ eyebrow }}</div>
                    <div class="font-display text-[23px] font-semibold tracking-tight">
                        <slot name="header">{{ title }}</slot>
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <slot name="actions" />
                </div>
            </header>

            <main class="flex-1 px-9 py-8">
                <slot />
            </main>
        </div>

        <Toast />
        <ConfirmDialog />
    </div>
</template>
