<script setup>
import { useConfirm } from '@/composables/useConfirm';
import Modal from '@/Components/Modal.vue';
import AppButton from '@/Components/AppButton.vue';

const { confirmState, cancel, confirmAction } = useConfirm();
</script>

<template>
    <Modal :show="!!confirmState" max-width="sm" @close="cancel">
        <div class="p-6">
            <div class="mb-2 text-base font-extrabold">Konfirmasi</div>
            <p class="mb-6 text-sm text-secondary">{{ confirmState?.message }}</p>
            <div class="flex justify-end gap-2.5">
                <AppButton variant="secondary" @click="cancel">Batal</AppButton>
                <AppButton variant="danger" @click="confirmAction">Hapus</AppButton>
            </div>
        </div>
    </Modal>
</template>
