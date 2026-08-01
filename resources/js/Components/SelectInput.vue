<script setup>
const model = defineModel({ type: [String, Number, null] });
</script>

<template>
    <select
        v-model="model"
        class="w-full rounded-control border-border bg-app text-sm text-ink shadow-none transition-colors duration-150 ease-out focus:border-accent focus:ring-1 focus:ring-accent"
    >
        <slot />
    </select>
</template>
