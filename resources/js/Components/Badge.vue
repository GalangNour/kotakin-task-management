<script setup>
defineProps({
    label: {
        type: String,
        required: true,
    },
    tone: {
        type: String,
        default: 'neutral', // 'accent' | 'success' | 'info' | 'warning' | 'danger' | 'neutral'
    },
});

const toneClasses = {
    accent: 'bg-accent-tint text-accent-dark',
    success: 'bg-success-tint text-success',
    info: 'bg-info-tint text-info',
    warning: 'bg-warning-tint text-warning',
    danger: 'bg-danger-tint text-danger',
    neutral: 'bg-neutral-tint text-secondary',
};
</script>

<template>
    <span
        class="inline-flex items-center whitespace-nowrap rounded-full px-2.5 py-0.5 text-[11px] font-bold"
        :class="toneClasses[tone] ?? toneClasses.neutral"
    >
        {{ label }}
    </span>
</template>
