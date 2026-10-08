<template>
    <AdminLayout>
        <AdminWrapper>
            <VCard class="mt-3">
                <VCardTitle class="d-flex justify-space-between">
                    <span>Завершенные заказы</span>
                </VCardTitle>
                <VDivider/>
                <VCardText>
                    <VRow>
                        <VCol cols="12" md="4">
                            <VTextField
                                :model-value="queryLocal.filter.id"
                                :error-messages="errors ? errors['filter.id'] : ''"
                                label="Поиск по номеру заказа"
                                prepend-inner-icon="$mdiMagnify"
                                variant="outlined"
                                hide-details="auto"
                                type="number"
                                single-line
                                clearable
                                @update:model-value="onIdUpdate"
                            />
                        </VCol>
                        <VCol cols="12" md="4">
                            <VSelect
                                v-model="queryLocal.filter.type_paid"
                                :items="Object.keys(typePaid).map(Number)"
                                :item-title="id => typePaid[id]"
                                :item-value="id => id"
                                label="Тип"
                                variant="outlined"
                                clearable
                            />
                        </VCol>
                        <VCol cols="12" md="4">
                            <VSelect
                                v-model="queryLocal.filter.status"
                                :items="Object.keys(statuses).map(Number)"
                                :item-title="id => statuses[id]"
                                :item-value="id => id"
                                label="Статус"
                                variant="outlined"
                                clearable
                            />
                        </VCol>
                        <VCol cols="12" md="4">
                            <VDateInput
                                v-model="dateCreatedRangeAdapter.inputModel.value"
                                :hide-actions="false"
                                @update:menu="dateCreatedRangeAdapter.onUpdateMenu"
                                @click:clear="dateCreatedRangeAdapter.onClear"
                                label="Дата создания"
                                variant="outlined"
                                multiple="range"
                                clearable
                            />
                        </VCol>
                        <VCol cols="12" md="4">
                            <VDateInput
                                v-model="dateCompletedRangeAdapter.inputModel.value"
                                :hide-actions="false"
                                @update:menu="dateCompletedRangeAdapter.onUpdateMenu"
                                @click:clear="dateCompletedRangeAdapter.onClear"
                                label="Дата завершения"
                                variant="outlined"
                                multiple="range"
                                clearable
                            />
                        </VCol>
                    </VRow>
                </VCardText>
            </VCard>
            <VCard class="mt-3">
                <VCardText>
                    <VDataTableServer
                        :items="orders.data"
                        :items-length="orders.total"
                        :items-per-page="queryLocal.batch ?? 10"
                        :page="queryLocal.page ?? 1"
                        :items-per-page-options="[
                            {title: '10', value: 10},
                            {title: '20', value: 20},
                            {title: '50', value: 50}
                        ]"
                        :headers="[
                            {key: 'id', title: '№ заказа', align: 'center'},
                            {key: 'user', title: 'ФИО заказчика', sortable: false, align: 'center'},
                            {key: 'products', title: 'Список заказа', sortable: false, align: 'center'},
                            {key: 'type_paid', title: 'Тип оплаты', align: 'center'},
                            {key: 'total_price', title: 'Сумма', align: 'center'},
                            {key: 'status', title: 'Статус', align: 'center'},
                            {key: 'created_at', title: 'Дата создания', align: 'center'},
                            {key: 'completed_at', title: 'Дата завершения', align: 'center'},
                            {key: 'remittance', title: 'Оплата', sortable: false, align: 'center'},
                            {key: 'need_delivery', title: 'Доставка', sortable: false, align: 'center'}
                        ]"
                        :sort-by="sortAdapter.sortBy.value"
                        @update:page="queryLocal.page = $event"
                        @update:items-per-page="queryLocal.batch = $event"
                        @update:sort-by="sortAdapter.onSort"
                    >
                        <template #item.user="{ item }">
                            {{ formatUserName(item.user) }}
                        </template>
                        <template #item.created_at="{ item }">
                            {{
                                new Date(item.created_at).toLocaleString()
                            }}
                        </template>
                        <template #item.completed_at="{ item }">
                            {{
                                item.completed_at ? new Date(item.completed_at).toLocaleString() : '-'
                            }}
                        </template>
                        <template #item.total_price="{ item }">
                            {{ formatPrice(item.total_price) }}
                        </template>
                        <template #item.status="{ item }">
                            {{ item.status_text }}
                        </template>
                        <template #item.type_paid="{ item }">
                            {{ item.type_paid_text }}
                        </template>
                        <template #item.products="{ internalItem, isExpanded, toggleExpand }">
                            <VBtn
                                :append-icon="isExpanded(internalItem) ? '$mdiUp' : '$mdiDown'"
                                :text="isExpanded(internalItem) ? 'Свернуть' : 'Подробнее'"
                                class="text-none"
                                color="medium-emphasis"
                                size="small"
                                variant="text"
                                width="105"
                                border
                                slim
                                @click="toggleExpand(internalItem)"
                            />
                        </template>
                        <template #expanded-row="{ columns, item }">
                            <tr>
                                <td
                                    :colspan="columns.length"
                                    class="py-2"
                                >
                                    <VSheet
                                        class="mx-auto"
                                        width="100%"
                                        max-width="1000"
                                        rounded="lg"
                                        border
                                    >
                                        <VDataTableVirtual
                                            :headers="[
                                                {key: 'title', title: 'Название товара', sortable: false},
                                                {key: 'price', title: 'Цена за единицу', sortable: false},
                                                {key: 'count', title: 'Количество', sortable: false},
                                                {key: 'calculateCountPrice', title: 'Сумма', sortable: false},
                                            ]"
                                            :items="item.products"
                                            density="compact"
                                            item-value="id"
                                            fixed-header
                                        >
                                            <template #item.price="{ item: product }">
                                                {{ formatPrice(product.price) }}
                                            </template>

                                            <template #item.calculateCountPrice="{ item: product }">
                                                {{ formatPrice(product.calculateCountPrice) }}
                                            </template>
                                        </VDataTableVirtual>
                                    </VSheet>
                                </td>
                            </tr>
                        </template>
                        <template #item.remittance="{ item }">
                            {{ item.remittance?.status_text ?? '-' }}
                        </template>
                        <template #item.need_delivery="{ item }">
                            {{ item.need_delivery ? 'Да' : '-' }}
                        </template>
                    </VDataTableServer>
                </VCardText>
            </VCard>
        </AdminWrapper>
    </AdminLayout>
</template>

<script setup lang="ts">
import AdminLayout from "~vue/Layouts/AdminLayout.vue";
import AdminWrapper from "~vue/Layouts/AdminWrapper.vue";
import {
    type OrderAdminHistoryResource,
    type OrdersCompletedQuery,
} from "~types/generated.ts";
import type {TypedPagination} from "~vue/shared/pagination.ts";
import type {RequiredKeys} from "~vue/shared/objects.ts";
import {reactive, watch} from "vue";
import {debounce, merge} from "lodash-es";
import useSpatieSortAdapter from "~vue/composables/useSpatieSortAdapter.ts";
import useSpatieDateRangeAdapter from "~vue/composables/useSpatieDateRangeAdapter.ts";
import {formatPrice, formatUserName} from "~vue/shared/formatters.ts";
import {router} from "@inertiajs/vue3";
import OrderRoutes from "~routes/Admin/OrderController.ts";

const {orders, query = {}, typePaid, statuses} = defineProps<{
    orders: TypedPagination<OrderAdminHistoryResource>;
    query: OrdersCompletedQuery;
    typePaid: Record<number, string>;
    statuses: Record<number, string>;
    errors?: Record<string, string>;
}>();
const queryDefaults: RequiredKeys<OrdersCompletedQuery, 'filter'> = {
    filter: {},
};
const queryLocal = reactive(merge({}, queryDefaults, query));

const onIdUpdate = debounce((value: string) => {
    const id = Number.parseInt(value, 10);

    queryLocal.filter.id = Number.isNaN(id)
        ? undefined
        : id;
    queryLocal.page = 1;
}, 900);

const sortAdapter = useSpatieSortAdapter(() => queryLocal.sort, sort => queryLocal.sort = sort);
const dateCreatedRangeAdapter = useSpatieDateRangeAdapter(
    [() => queryLocal.filter.date_created_from, () => queryLocal.filter.date_created_to],
    ([date_from, date_to]) => {
        queryLocal.filter.date_created_from = date_from;
        queryLocal.filter.date_created_to = date_to;
    },
);
const dateCompletedRangeAdapter = useSpatieDateRangeAdapter(
    [() => queryLocal.filter.date_completed_from, () => queryLocal.filter.date_completed_to],
    ([date_from, date_to]) => {
        queryLocal.filter.date_completed_from = date_from;
        queryLocal.filter.date_completed_to = date_to;
    },
);

watch(queryLocal, applyReload);

function applyReload(): void {
    router.visit(OrderRoutes.index({
        query: queryLocal,
    }), {
        preserveScroll: true,
        preserveState: true,
        replace: true,
    });
}
</script>
