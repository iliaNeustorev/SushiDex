<template>
    <template
        v-if="order.type_paid === TypePaid.CARD_ONLINE && order.remittance?.status === RemittanceStatus.AWAIT_PAID">
        <VBtn
            v-if="type === 'icon'"
            icon="$mdiCashClock" :size="size" class="ml-2 text-green-darken-3"
            title="Оплатить" @click="sendRemittance"></VBtn>
        <VBtn
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
import {type OrderPublicResource, RemittanceStatus, TypePaid} from "~types/generated.ts";
import {router} from "@inertiajs/vue3";
import RemittanceController from "~routes/Client/RemittanceController.ts";

const {order, size = 'small', type = 'icon'} = defineProps<{
    order: OrderPublicResource,
    size?: string,
    type?: 'icon' | 'default'
}>(
)

function sendRemittance() {
    router.visit(RemittanceController.store(order), {
        preserveScroll: true,
        preserveState: true
    })
}
</script>
