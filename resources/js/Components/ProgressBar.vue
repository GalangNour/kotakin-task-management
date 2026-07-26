<script setup>
defineProps({
    value: {
        type: Number,
        required: true, // 0-100
    },
    height: {
        type: Number,
        default: 8,
    },
});
</script>

<template>
    <div
        class="overflow-hidden rounded-full bg-neutral-tint"
        :style="{ height: `${height}px` }"
    >
        <div
            class="h-full rounded-full bg-accent transition-all"
            :style="{ width: `${Math.min(100, Math.max(0, value))}%` }"
        />
    </div>
</template>
