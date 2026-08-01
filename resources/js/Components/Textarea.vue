<script setup>
const model = defineModel({ type: String });
</script>

<template>
    <textarea
        v-model="model"
        class="w-full rounded-control border-border bg-app text-sm text-ink shadow-none transition-colors duration-150 ease-out placeholder:text-secondary/60 focus:border-accent focus:ring-1 focus:ring-accent"
    />
</template>
