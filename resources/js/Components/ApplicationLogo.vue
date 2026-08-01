<script setup>
defineProps({
    size: {
        type: Number,
        default: 40,
    },
});
</script>

<template>
    <div
        class="flex shrink-0 items-center justify-center rounded-control bg-accent font-display text-white"
        :style="{ width: `${size}px`, height: `${size}px`, fontSize: `${size * 0.5}px` }"
    >
        T
    </div>
</template>
