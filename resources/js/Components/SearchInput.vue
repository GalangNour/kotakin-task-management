<script setup>
const model = defineModel({ type: String, default: '' });

defineProps({
    placeholder: {
        type: String,
        default: 'Cari...',
    },
});
</script>

<template>
    <input
        v-model="model"
        type="text"
        :placeholder="placeholder"
        class="w-60 rounded-control border border-border bg-white px-3.5 py-2.5 font-sans text-[13px] text-ink outline-none placeholder:text-secondary focus:border-accent"
    />
</template>
