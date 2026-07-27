<script setup>
import { useToast } from '@/composables/useToast';

const { toast } = useToast();

const toneClasses = {
    success: 'bg-success',
    error: 'bg-danger',
    info: 'bg-accent',
};
</script>

<template>
    <Transition
        enter-active-class="transition duration-150 ease-out"
        enter-from-class="opacity-0 translate-y-2"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition duration-150 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div
            v-if="toast"
            class="fixed bottom-6 right-6 z-[100] rounded-[11px] px-5 py-3.5 text-[13px] font-bold text-white shadow-[0_8px_24px_oklch(0.2_0.02_260_/_25%)]"
            :class="toneClasses[toast.tone] ?? toneClasses.success"
        >
            {{ toast.message }}
        </div>
    </Transition>
</template>
