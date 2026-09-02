<script setup lang="ts" generic="T extends object">
import { computed } from 'vue';

const { data, headers = [] } = defineProps<{
    data: T[],
    headers: string[]
}>();

const columns = computed(() => headers.length ? headers : Object.keys(data[0] ?? {}));
</script>

<template>
    <table class="table table-bordered table-hover">
        <thead>
        <tr>
            <th v-for="column in columns">
                <slot :name="`header.${column}`">
                    {{ column }}
                </slot>
            </th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="item,i in data">
            <td v-for="column in columns">
                <slot :name="`item.${column}`" :item="item" :i="i">
                    {{ column in item ? item[column as keyof T] : '' }}
                </slot>
            </td>
        </tr>
        </tbody>
    </table>
</template>
