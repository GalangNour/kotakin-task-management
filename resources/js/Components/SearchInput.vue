<script setup>
import { Search } from '@lucide/vue';

const model = defineModel({ type: String, default: '' });

defineProps({
    placeholder: {
        type: String,
        default: 'Cari...',
    },
});
</script>

<template>
    <div class="relative">
        <Search :size="14" :stroke-width="2.25" class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-secondary" />
        <input
            v-model="model"
            type="text"
            :placeholder="placeholder"
            class="w-60 rounded-control border border-border bg-white py-2.5 pl-8 pr-3.5 font-sans text-[13px] text-ink outline-none transition-colors duration-150 ease-out placeholder:text-secondary focus:border-accent"
        />
    </div>
</template>
