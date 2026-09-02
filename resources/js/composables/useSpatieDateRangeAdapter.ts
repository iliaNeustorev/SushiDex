import {ref, watch} from "vue";
import {toStandartDate} from "~vue/shared/datetime";

type SourceFrom = () => string | undefined
type SourceTo = () => string | undefined
type DateSource = [SourceFrom, SourceTo]

export default function useSpatieDateRangeAdapter(
    sources: DateSource,
    updated: (dt: [string | undefined, string | undefined]) => void
) {
    const inputModel = ref<Date[]>([]);

    watch(sources, ([from, to]) => {
        inputModel.value = from && to ? [new Date(from), new Date(to)] : []
    }, {immediate: true});

    function onUpdateMenu(menuIsOpen: boolean) {
        if (!menuIsOpen) {
            const from = inputModel.value[0] ? toStandartDate(inputModel.value[0]) : undefined;
            const to = inputModel.value.length > 1 ? toStandartDate(inputModel.value[inputModel.value.length - 1]) : from;

            if (sources[0]() !== from || sources[1]() !== to) {
                updated([from, to]);
            }
        }
    }

    const onClear = () => onUpdateMenu(false);

    return {inputModel, onUpdateMenu, onClear};
}
