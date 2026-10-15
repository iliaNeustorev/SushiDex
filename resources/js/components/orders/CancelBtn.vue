<template>
    <VBtn
        v-if="order.status !== OrderStatus.CANCELLED && order.status !== OrderStatus.COMPLETED"
        color="red-lighten-1"
        density="comfortable"
        icon="$deleteOutline"
        :loading="loading"
        @click="showDialog = true"
    >
    </VBtn>
    <VDialog
        :model-value="showDialog"
        max-width="420"
    >
        <VCard v-if="showDialog">
            <VCardTitle>
                Отменить заказ №{{ order.id }}?
            </VCardTitle>

            <VCardActions>
                <VBtn @click="showDialog = false">
                    Закрыть
                </VBtn>

                <VBtn
                    color="error"
                    @click="confirmCancel"
                >
                    Подтвердить
                </VBtn>
            </VCardActions>
        </VCard>
    </VDialog>
</template>

<script setup lang="ts">
import {type OrderPublicResource, OrderStatus} from "~types/generated.ts";
import {shallowRef} from "vue";
import {router} from "@inertiajs/vue3";
import OrderController from "~routes/Client/OrderController.ts";

const {order} = defineProps<{ order: OrderPublicResource }>()

const showDialog = shallowRef<boolean>(false)
const loading = shallowRef<boolean>(false)

function confirmCancel() {
    loading.value = true
    router.visit(OrderController.cancel(order.id), {
        onError: () => {
            // TODO: Обработка ошибок
        },
        onFinish: () => {
            loading.value = false
        },
    })
}
</script>
