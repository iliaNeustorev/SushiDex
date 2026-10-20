<template>
    <template
        v-if="order.status !== OrderStatus.COMPLETED
            && order.status !== OrderStatus.CANCELLED
            && order.type_paid === TypePaid.CARD_ONLINE
            && !order.remittance?.paid"
    >
        <VBtn
            v-if="type === 'icon'"
            :loading="checkSend"
            icon="$mdiCashClock"
            :size="size"
            class="ml-2 text-green-darken-3"
            title="Оплатить"
            @click="sendRemittance"
        />
        <VBtn
            v-else
            :loading="checkSend"
            prepend-icon="$mdiCashClock"
            color="green-darken-3"
            text="Оплатить"
            :size="size"
            @click="sendRemittance"
        />
    </template>
</template>

<script setup lang="ts">
import {type OrderPublicResource, OrderStatus, TypePaid} from '~types/generated.ts';
import RemittanceController from '~routes/Client/RemittanceController.ts';
import {shallowRef} from 'vue';
import axios from 'axios';
import {router} from '@inertiajs/vue3';
import type {ErrorResponse} from '~vue/types/axios.ts';

const {order, size = 'small', type = 'icon'} = defineProps<{
    order: OrderPublicResource,
    size?: string,
    type?: 'icon' | 'default'
}>(
)
const checkSend = shallowRef<boolean>(false)

async function sendRemittance(): Promise<void> {
    const paymentWindow = window.open('about:blank', '_blank');

    if (!paymentWindow) {
        router.flash({
            type: 'error',
            text: 'Разрешите всплывающие окна для перехода к оплате.',
        });

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
    } catch (error: unknown) {
        paymentWindow.close();

        router.flash({
            type: 'error',
            text: getPaymentErrorMessage(error),
        });
    } finally {
        checkSend.value = false;
    }
}

function getPaymentErrorMessage(error: unknown): string {
    if (!axios.isAxiosError<ErrorResponse>(error)) {
        return 'Произошла неизвестная ошибка.';
    }

    const data = error.response?.data;
    const firstError = Object.values(data?.errors ?? {})
        .flat()
        .at(0);

    return firstError
        ?? data?.message
        ?? 'Не удалось перейти к оплате.';
}
</script>
