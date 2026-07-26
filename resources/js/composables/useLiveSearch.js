import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';

export function useLiveSearch(url, initialValue, delay = 350) {
    const search = ref(initialValue ?? '');
    let timeout = null;

    watch(search, () => {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            router.get(
                url,
                { search: search.value || undefined },
                { preserveState: true, replace: true },
            );
        }, delay);
    });

    return search;
}
