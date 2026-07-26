<script setup>
import { computed } from 'vue';

const props = defineProps({
    name: {
        type: String,
        required: true,
    },
    variant: {
        type: String,
        default: 'neutral', // 'accent' | 'neutral'
    },
    size: {
        type: Number,
        default: 32,
    },
});

const initials = computed(() =>
    props.name
        .trim()
        .split(/\s+/)
        .map((w) => w[0])
        .slice(0, 2)
        .join('')
        .toUpperCase(),
);

const toneClasses = {
    accent: 'bg-accent-tint text-accent-dark',
    neutral: 'bg-neutral-tint text-ink/70',
};
</script>

<template>
    <div
        class="flex shrink-0 items-center justify-center rounded-full font-bold"
        :class="toneClasses[variant] ?? toneClasses.neutral"
        :style="{ width: `${size}px`, height: `${size}px`, fontSize: `${Math.max(10, Math.round(size * 0.35))}px` }"
    >
        {{ initials }}
    </div>
</template>
