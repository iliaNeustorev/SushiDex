<template>
    <template
        v-if="order.status === OrderStatus.NEW && order.type_paid === TypePaid.CARD_ONLINE && !order.remittance?.paid">
        <VBtn
            v-if="type === 'icon'"
            :loading="checkSend"
            icon="$mdiCashClock" :size="size" class="ml-2 text-green-darken-3"
            title="Оплатить" @click="sendRemittance">
        </VBtn>
        <VBtn
            :loading="checkSend"
            prepend-icon="$mdiCashClock"
            v-else
            color="green-darken-3"
            text="Оплатить"
            :size="size"
            @click="sendRemittance"
        ></VBtn>
    </template>
</template>

<script setup lang="ts">
import {type OrderPublicResource, OrderStatus, TypePaid} from "~types/generated.ts";
import RemittanceController from "~routes/Client/RemittanceController.ts";
import {shallowRef} from "vue";
import axios from "axios";

const {order, size = 'small', type = 'icon'} = defineProps<{
    order: OrderPublicResource,
    size?: string,
    type?: 'icon' | 'default'
}>(
)
const checkSend = shallowRef<boolean>(false)

async function sendRemittance() {
    const paymentWindow = window.open('about:blank', '_blank');

    if (!paymentWindow) {
        // TODO: показать сообщение «Разрешите всплывающие окна»
        return;
    }

    paymentWindow.opener = null;
    checkSend.value = true;

    try {
        const {data} = await axios.post<{ url: string }>(
            RemittanceController.store(order).url,
        );

        if (!data.url) {
            throw new Error('Не получена ссылка на оплату');
        }

        paymentWindow.location.replace(data.url);
    } catch (error) {
        paymentWindow.close();

        // TODO: показать пользователю ошибку оплаты
    } finally {
        checkSend.value = false;
    }
}
</script>
