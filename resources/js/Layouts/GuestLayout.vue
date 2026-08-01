<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="flex min-h-screen flex-col items-center justify-center bg-app px-4 py-10 font-sans text-ink">
        <div class="flex flex-col items-center gap-3">
            <Link href="/">
                <ApplicationLogo :size="48" />
            </Link>
            <span class="font-display text-lg font-semibold tracking-tight">Task Management</span>
        </div>

        <div class="mt-8 w-full overflow-hidden rounded-card border border-border bg-white px-8 py-7 shadow-card sm:max-w-md">
            <slot />
        </div>
    </div>
</template>
