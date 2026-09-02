import {ref, watch, type WatchSource} from "vue";
import type {SortItem} from "vuetify/lib/components/VDataTable/composables/sort.mjs";

export default function useSpatieSortAdapter(
    source: WatchSource<string | undefined>,
    updated: (newSort: string | undefined) => void
) {
    const sortBy = ref<SortItem[]>([]);

    watch(source, current => {
        sortBy.value = !current ? [] : [
            current.indexOf('-') == 0
                ? {order: 'desc' as const, key: current.substring(1)}
                : {order: 'asc' as const, key: current}
        ]
    }, {immediate: true});

    function onSort([sort]: SortItem[]) {
        updated(sort
            ? sort.order == 'asc'
                ? sort.key
                : `-${sort.key}`
            : undefined)
    }

    return {sortBy, onSort};
}
