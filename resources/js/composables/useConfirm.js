import { ref } from 'vue';

const confirmState = ref(null); // null | { message, onConfirm }

export function useConfirm() {
    const askConfirm = (message, onConfirm) => {
        confirmState.value = { message, onConfirm };
    };

    const cancel = () => {
        confirmState.value = null;
    };

    const confirmAction = () => {
        const onConfirm = confirmState.value?.onConfirm;
        confirmState.value = null;
        onConfirm?.();
    };

    return { confirmState, askConfirm, cancel, confirmAction };
}
