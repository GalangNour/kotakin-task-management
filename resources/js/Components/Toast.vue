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
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="opacity-0 translate-y-2 scale-95"
        enter-to-class="opacity-100 translate-y-0 scale-100"
        leave-active-class="transition duration-150 ease-out"
        leave-from-class="opacity-100 scale-100"
        leave-to-class="opacity-0 scale-95"
    >
        <div
            v-if="toast"
            class="fixed bottom-6 right-6 z-[100] rounded-card-sm px-5 py-3.5 text-[13px] font-bold text-white shadow-toast"
            :class="toneClasses[toast.tone] ?? toneClasses.success"
        >
            {{ toast.message }}
        </div>
    </Transition>
</template>
