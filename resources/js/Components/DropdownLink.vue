<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    href: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Link
        :href="href"
        class="block w-full px-4 py-2 text-start text-sm font-semibold text-ink transition-colors duration-150 ease-out hover:bg-neutral-tint focus:bg-neutral-tint focus:outline-none"
    >
        <slot />
    </Link>
</template>
