<template>
    <VDataTableServer
        :headers="headers"
        :items="orders.data"
        :items-length="orders.total"
        :items-per-page="itemsPerPage"
        :page="page"
        :items-per-page-options="[
            {title: '10', value: 10},
            {title: '20', value: 20},
            {title: '50', value: 50},
        ]"
        @update:page="emit('update:page', $event)"
        @update:items-per-page="emit('update:items-per-page', $event)"
    >
        <template #item.total_price="{ item }">
            {{ formatPrice(item.total_price) }}
        </template>

        <template #item.created_at="{ item }">
            {{ new Date(item.created_at).toLocaleString() }}
        </template>

        <template #item.type_paid_text="{ item }">
            <VAlert width="150" :type="item.remittance?.paid ? 'success' : 'warning'" density="compact"
                    v-if="item.type_paid === TypePaid.CARD_ONLINE">
                <span :title="item.remittance?.paid ? 'Оплата принята' : 'Оплата ожидается'">{{
                        item.type_paid_text
                    }}</span>
            </VAlert>
            <VAlert v-else type="success" density="compact">
                {{ item.type_paid_text }}
            </VAlert>
        </template>

        <template #item.user="{ item }">
            {{ formatUserName(item) }}
        </template>

        <template #item.need_delivery="{ item }">
            {{ item.need_delivery ? 'Да' : 'Нет' }}
        </template>

        <template #item.address="{ item }">
            {{ item.need_delivery ? item.user.address : '-' }}
        </template>

        <template #item.phone="{ item }">
            {{ item.user.phone?.phone ?? 'Не указан' }}
        </template>

        <template #item.actions="{ item }">
            <div class="d-flex flex-nowrap justify-center ga-2 align-center">
                <VBtn
                    rounded="lg"
                    color="success"
                    prepend-icon="$mdiCheckOutline"
                    @click="emit('primary-action', item)"
                >
                    {{ action === 'start' ? 'В работу' : 'Завершить' }}
                </VBtn>
                <VBtn
                    rounded="lg"
                    color="error"
                    prepend-icon="$mdiCloseOutline"
                    @click="emit('cancel', item)"
                >
                    Отменить
                </VBtn>
                <VBtn
                    title="Изменить настройки"
                    color="warning"
                    icon="$mdiCogs"
                    @click="emit('setting-action', item)"
                >
                </VBtn>
            </div>
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
                            :headers="productHeaders"
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
    </VDataTableServer>
</template>

<script setup lang="ts">
import {type OrderAdminPublicResource, TypePaid} from '~types/generated';
import type {TypedPagination} from '~vue/shared/pagination';
import {formatPrice} from '~vue/shared/formatters';

defineProps<{
    orders: TypedPagination<OrderAdminPublicResource>;
    page: number;
    itemsPerPage: number;
    action: 'start' | 'complete';
}>();

const emit = defineEmits<{
    'update:page': [value: number];
    'update:items-per-page': [value: number];
    'primary-action': [order: OrderAdminPublicResource];
    cancel: [order: OrderAdminPublicResource];
    'setting-action': [order: OrderAdminPublicResource];
}>();

const headers = [
    {key: 'id', title: 'Номер заказа', sortable: false},
    {key: 'total_price', title: 'Сумма', sortable: false},
    {key: 'products', title: 'Список заказа', sortable: false},
    {key: 'status_text', title: 'Текущий статус', sortable: false},
    {key: 'type_paid_text', title: 'Тип оплаты', sortable: false},
    {key: 'need_delivery', title: 'Доставка', sortable: false},
    {key: 'address', title: 'Адрес доставки', sortable: false},
    {key: 'user', title: 'ФИО заказчика', sortable: false},
    {key: 'phone', title: 'Телефон заказчика', sortable: false},
    {key: 'created_at', title: 'Дата создания', sortable: false},
    {key: 'actions', title: 'Действия', sortable: false, align: 'center' as const},
];

const productHeaders = [
    {key: 'title', title: 'Название товара', sortable: false},
    {key: 'price', title: 'Цена за единицу', sortable: false},
    {key: 'count', title: 'Количество', sortable: false},
    {key: 'calculateCountPrice', title: 'Сумма', sortable: false},
];

function formatUserName(order: OrderAdminPublicResource): string {
    return [order.user.last_name, order.user.first_name, order.user.middle_name]
        .filter(Boolean)
        .join(' ');
}
</script>
