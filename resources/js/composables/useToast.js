import { ref } from 'vue';

const toast = ref(null); // null | { message, tone }
let timer = null;

export function useToast() {
    const show = (message, tone = 'success') => {
        clearTimeout(timer);
        toast.value = { message, tone };
        timer = setTimeout(() => {
            toast.value = null;
        }, 2600);
    };

    const clear = () => {
        clearTimeout(timer);
        toast.value = null;
    };

    return { toast, show, clear };
}
