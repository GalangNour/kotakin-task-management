<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    href: {
        type: String,
        default: null,
    },
    variant: {
        type: String,
        default: 'primary', // 'primary' | 'secondary' | 'danger'
    },
    type: {
        type: String,
        default: 'button',
    },
    method: {
        type: String,
        default: null,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});

const variantClasses = {
    primary: 'border border-transparent bg-accent text-white hover:bg-accent-dark',
    secondary: 'border border-border bg-white text-ink hover:bg-neutral-tint',
    danger: 'border border-transparent bg-danger text-white hover:opacity-90',
};
</script>

<template>
    <component
        :is="href ? Link : 'button'"
        v-bind="href ? { href, method, as: method ? 'button' : 'a' } : { type, disabled }"
        class="inline-flex items-center justify-center whitespace-nowrap rounded-control px-4 py-2.5 text-[13px] font-bold transition disabled:cursor-not-allowed disabled:opacity-50"
        :class="variantClasses[variant] ?? variantClasses.primary"
    >
        <slot />
    </component>
</template>
