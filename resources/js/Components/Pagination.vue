<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    links: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <div v-if="links.length > 3" class="mt-4 flex flex-wrap gap-1">
        <template v-for="(link, index) in links" :key="index">
            <div
                v-if="link.url === null"
                class="rounded-control px-3 py-1 text-sm text-secondary/50"
                v-html="link.label"
            />
            <Link
                v-else
                :href="link.url"
                preserve-scroll
                class="rounded-control px-3 py-1 text-sm font-semibold transition-colors duration-150 ease-out"
                :class="
                    link.active
                        ? 'bg-accent text-white'
                        : 'text-secondary hover:bg-neutral-tint'
                "
                v-html="link.label"
            />
        </template>
    </div>
</template>
