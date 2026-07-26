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
                class="rounded-md px-3 py-1 text-sm text-gray-400 dark:text-gray-600"
                v-html="link.label"
            />
            <Link
                v-else
                :href="link.url"
                preserve-scroll
                class="rounded-md px-3 py-1 text-sm"
                :class="
                    link.active
                        ? 'bg-gray-800 text-white dark:bg-gray-200 dark:text-gray-800'
                        : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700'
                "
                v-html="link.label"
            />
        </template>
    </div>
</template>
