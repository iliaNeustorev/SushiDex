import {useForm} from '@inertiajs/vue3';
import {shallowRef} from 'vue';
import type {RouteDefinition} from '~gen/wayfinder/wayfinder';

type TrashItem = {
    id: number,
};

type TrashActionsOptions = {
    restoreRoute: (id: number) => RouteDefinition<'put'>,
    forceDeleteRoute: (id: number) => RouteDefinition<'delete'>,
};

export default function useTrashActions<T extends TrashItem>(options: TrashActionsOptions) {
    const itemForRestore = shallowRef<T | null>(null);
    const itemForDelete = shallowRef<T | null>(null);
    const restoreForm = useForm({});
    const deleteForm = useForm({});

    function confirmRestore(item: T) {
        itemForRestore.value = item;
    }

    function confirmDelete(item: T) {
        itemForDelete.value = item;
    }

    function cancelRestore() {
        itemForRestore.value = null;
    }

    function cancelDelete() {
        itemForDelete.value = null;
    }

    function restore() {
        if (!itemForRestore.value) {
            return;
        }

        restoreForm.submit(options.restoreRoute(itemForRestore.value.id), {
            onFinish: cancelRestore,
        });
    }

    function forceDelete() {
        if (!itemForDelete.value) {
            return;
        }

        deleteForm.submit(options.forceDeleteRoute(itemForDelete.value.id), {
            onFinish: cancelDelete,
        });
    }

    return {
        itemForRestore,
        itemForDelete,
        restoreForm,
        deleteForm,
        confirmRestore,
        confirmDelete,
        cancelRestore,
        cancelDelete,
        restore,
        forceDelete,
    };
}
