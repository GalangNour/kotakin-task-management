<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        class="w-full rounded-control border-border bg-app text-sm text-ink shadow-none transition-colors duration-150 ease-out placeholder:text-secondary/60 focus:border-accent focus:ring-1 focus:ring-accent"
        v-model="model"
        ref="input"
    />
</template>
