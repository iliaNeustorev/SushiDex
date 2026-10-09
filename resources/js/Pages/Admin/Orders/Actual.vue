<template>
    <AdminLayout>
        <AdminWrapper>
            <VCard flat class="mt-3">
                <VCardText>
                    <div class="d-flex justify-space-between">
                        <Link
                            :href="OrderRoutes.index().url"
                            class="text-decoration-none text-green-darken-3"
                        >
                            <VBtn
                                rounded="lg"
                                size="large"
                                title="Посмотреть заказы за все время"
                            >
                                Завершенные заказы
                            </VBtn>
                        </Link>

                        <VTextField
                            :model-value="queryLocal.filter.id"
                            label="Поиск по номеру заказа"
                            prepend-inner-icon="$mdiMagnify"
                            variant="outlined"
                            max-width="300"
                            type="number"
                            hide-details="auto"
                            single-line
                            clearable
                            :error-messages="errors ? errors['filter.id'] : ''"
                            @update:model-value="onIdUpdate"
                        />
                    </div>
                </VCardText>

                <VTabs
                    v-model="tab"
                    color="primary"
                >
                    <VTab value="actual">
                        Актуальные заказы
                        <span
                            v-if="actualOrders.total > 0"
                            class="text-red-accent-4"
                        >
                            ({{ actualOrders.total }})
                        </span>
                    </VTab>

                    <VTab value="processing">
                        Заказы в обработке
                        <span
                            v-if="processingOrders.total > 0"
                            class="text-red-accent-4"
                        >
                            ({{ processingOrders.total }})
                        </span>
                    </VTab>
                </VTabs>

                <VDivider/>

                <VTabsWindow v-model="tab">
                    <VTabsWindowItem value="actual">
                        <AdminOrdersTable
                            :orders="actualOrders"
                            :page="queryLocal.actualPage ?? 1"
                            :items-per-page="queryLocal.actualBatch ?? 10"
                            action="start"
                            @update:page="queryLocal.actualPage = $event"
                            @update:items-per-page="changeActualBatch"
                            @primary-action="startOrder($event)"
                            @setting-action="openSettingDialog"
                            @cancel="openCancelDialog"
                        />
                    </VTabsWindowItem>

                    <VTabsWindowItem value="processing">
                        <AdminOrdersTable
                            :orders="processingOrders"
                            :page="queryLocal.processingPage ?? 1"
                            :items-per-page="queryLocal.processingBatch ?? 10"
                            action="complete"
                            @update:page="queryLocal.processingPage = $event"
                            @update:items-per-page="changeProcessingBatch"
                            @primary-action="completeOrder($event)"
                            @setting-action="openSettingDialog"
                            @cancel="openCancelDialog"
                        />
                    </VTabsWindowItem>
                </VTabsWindow>
            </VCard>

            <VDialog
                :model-value="!!orderForCancellation"
                max-width="420"
            >
                <VCard v-if="orderForCancellation">
                    <VCardTitle>
                        Отменить заказ №{{ orderForCancellation.id }}?
                    </VCardTitle>

                    <VCardActions>
                        <VBtn @click="orderForCancellation = null">
                            Закрыть
                        </VBtn>

                        <VBtn
                            color="error"
                            @click="confirmCancelOrder"
                        >
                            Подтвердить
                        </VBtn>
                    </VCardActions>
                </VCard>
            </VDialog>
            <VDialog
                :model-value="!!orderForSetting"
                max-width="420"
            >
                <VCard v-if="orderForSetting">
                    <VCardTitle>
                        Изменить заказ №{{ orderForSetting.id }}
                    </VCardTitle>
                    <VContainer>
                        <VAlert
                            v-if="!hasOrderAddress"
                            class="mb-2"
                            border="top"
                            type="warning"
                            variant="outlined"
                            density="compact"
                            prominent
                            closable
                        >
                            Доставка не возможна пока в профиле не указан адрес!
                        </VAlert>
                        <VBtn
                            class="mb-2"
                            density="comfortable"
                            @click="showFieldChangeAddress = !showFieldChangeAddress"
                        >
                            Изменить адрес
                        </VBtn>

                        <VTextField
                            v-model="formChangeAddress.address"
                            v-show="showFieldChangeAddress"
                            density="comfortable"
                            label="Добавить адрес"
                            :error-messages="formChangeAddress.errors.address"
                            variant="solo-filled"
                            @blur="sendChangeAddress"
                        />

                        <VCheckbox
                            v-model="formChangeSetting.need_delivery"
                            :error-messages="formChangeSetting.errors.need_delivery"
                            color="#df5f45"
                            density="compact"
                            label="Доставка"
                            :disabled="!hasOrderAddress"
                        />

                        <VSelect
                            v-model="formChangeSetting.type"
                            :error-messages="formChangeSetting.errors.type"
                            :items="availablePaymentTypes"
                            :item-title="id => typePaid[id] ?? 'Выберите тип'"
                            :item-value="id => id"
                            label="Тип оплаты"
                            variant="solo-filled"
                            :disabled="orderForSetting.remittance?.paid"
                        />
                    </VContainer>
                    <VCardActions>
                        <VBtn
                            :loading="formChangeSetting.processing"
                            @click="closeSettingDialog"
                        >
                            Закрыть
                        </VBtn>

                        <VBtn
                            color="success"
                            :loading="formChangeSetting.processing"
                            :disabled="!formChangeSetting.isDirty"
                            @click="confirmChangeSettingOrder"
                        >
                            Подтвердить
                        </VBtn>
                    </VCardActions>
                </VCard>
            </VDialog>
        </AdminWrapper>
    </AdminLayout>
</template>

<script setup lang="ts">
import {Link, router, useForm} from '@inertiajs/vue3';
import {debounce, merge} from 'lodash-es';
import {computed, reactive, ref, shallowRef, watch} from 'vue';
import {
    type ChangeAddressReqDTO,
    type OrderAdminPublicResource,
    type OrdersActualQuery,
    OrderStatus,
    type OrderUpdateSettingsReqDTO,
    TypePaid,
} from '~types/generated';
import OrderRoutes from '~routes/Admin/OrderController';
import AdminOrdersTable from '~vue/components/orders/AdminOrdersTable.vue';
import type {RequiredKeys} from '~vue/shared/objects';
import type {TypedPagination} from '~vue/shared/pagination';
import AdminLayout from "~vue/Layouts/AdminLayout.vue";
import AdminWrapper from "~vue/Layouts/AdminWrapper.vue";
import type {UpdateAdminOrderForm} from "~vue/shared/forms.ts";
import UserController from "~routes/Admin/UserController.ts";

const {actualOrders, processingOrders, query = {}, typePaid} = defineProps<{
    actualOrders: TypedPagination<OrderAdminPublicResource>;
    processingOrders: TypedPagination<OrderAdminPublicResource>;
    query: OrdersActualQuery;
    typePaid: Record<number, string>;
    errors?: Record<string, string>;
}>();

const queryDefaults: RequiredKeys<OrdersActualQuery, 'filter'> = {
    filter: {},
};
const queryLocal = reactive(merge({}, queryDefaults, query));
const tab = ref<'actual' | 'processing'>('actual');
const orderForCancellation = ref<OrderAdminPublicResource | null>(null);
const orderForSetting = ref<OrderAdminPublicResource | null>(null);
const formChangeSetting = useForm<OrderUpdateSettingsReqDTO>({});
const formUpdateOrder = useForm<UpdateAdminOrderForm>({
    status: null,
});
const formChangeAddress = useForm<ChangeAddressReqDTO>({
    address: '',
});
const showFieldChangeAddress = shallowRef<boolean>(false);

const hasOrderAddress = computed<boolean>(() => {
    const address = orderForSetting.value?.user.address;

    return typeof address === 'string' && address.trim() !== '';
});

const availablePaymentTypes = computed<number[]>(() => {
    const courierPaymentTypes = new Set<number>([
        TypePaid.CARD_COURIER,
        TypePaid.CASH_COURIER,
    ]);

    return Object.keys(typePaid)
        .map(Number)
        .filter(type => hasOrderAddress.value || !courierPaymentTypes.has(type));
});

const onIdUpdate = debounce((value: string) => {
    const id = Number.parseInt(value, 10);

    queryLocal.filter.id = Number.isNaN(id)
        ? undefined
        : id;
    queryLocal.actualPage = 1;
    queryLocal.processingPage = 1;
}, 900);

watch(queryLocal, applyReload);

function applyReload(): void {
    router.visit(OrderRoutes.actual({
        query: queryLocal,
    }), {
        preserveScroll: true,
        preserveState: true,
        replace: true,
    });
}

function changeActualBatch(value: number): void {
    queryLocal.actualBatch = value;
    queryLocal.actualPage = 1;
}

function changeProcessingBatch(value: number): void {
    queryLocal.processingBatch = value;
    queryLocal.processingPage = 1;
}

function startOrder(order: OrderAdminPublicResource): void {
    sendFormUpdateOrder(order.id, OrderStatus.PROCESSING);
}

function completeOrder(order: OrderAdminPublicResource): void {
    sendFormUpdateOrder(order.id, OrderStatus.COMPLETED);
}

function openCancelDialog(order: OrderAdminPublicResource): void {
    orderForCancellation.value = order;
}

function confirmCancelOrder(): void {
    const order = orderForCancellation.value;

    if (order) {
        sendFormUpdateOrder(order.id, OrderStatus.CANCELLED);
    }

    orderForCancellation.value = null;
}

function sendFormUpdateOrder(orderId: number, status: OrderStatus): void {
    formUpdateOrder.status = status;
    formUpdateOrder.submit(OrderRoutes.update(orderId), {
        preserveState: true,
        preserveScroll: true,
        only: ['actualOrders', 'processingOrders'],
    });
}

function openSettingDialog(order: OrderAdminPublicResource): void {
    orderForSetting.value = order;

    formChangeSetting.defaults({
        type: order.type_paid,
        need_delivery: order.need_delivery,
    });
    formChangeAddress.defaults({
        address: order.user.address ?? '',
    });
    formChangeAddress.reset();
    formChangeAddress.clearErrors();

    formChangeSetting.reset();
    formChangeSetting.clearErrors();
}

function closeSettingDialog(): void {
    orderForSetting.value = null;
    showFieldChangeAddress.value = false;
}

function confirmChangeSettingOrder(): void {
    const order = orderForSetting.value;

    if (!order) {
        return;
    }

    formChangeSetting.submit(OrderRoutes.updateSettings(order.id), {
        preserveScroll: true,
        onSuccess: () => {
            orderForSetting.value = null;
            formChangeSetting.reset();
        },
    });
}

function sendChangeAddress(): void {
    const order = orderForSetting.value;

    if (!order || !formChangeAddress.isDirty) {
        return;
    }

    const address = formChangeAddress.address.trim();

    formChangeAddress.submit(UserController.changeAddress(order.user.id), {
        preserveScroll: true,
        preserveState: true,
        only: ['actualOrders', 'processingOrders'],
        onSuccess: () => {
            const currentOrder = orderForSetting.value;

            if (!currentOrder || currentOrder.id !== order.id) {
                return;
            }

            orderForSetting.value = {
                ...currentOrder,
                user: {
                    ...currentOrder.user,
                    address,
                },
            };

            formChangeAddress.defaults({address});
            formChangeAddress.reset();
            formChangeAddress.clearErrors();

            if (address === '') {
                formChangeSetting.need_delivery = false;
            }
        },
    });
}
</script>
