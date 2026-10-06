<template>
    <MainLayout>
        <VContainer>
            <VTable striped="even">
                <thead>
                <tr>
                    <th class="text-center">
                        № заказа
                    </th>
                    <th class="text-center">
                        Сумма
                    </th>
                    <th class="text-center">
                        Статус
                    </th>
                    <th class="text-center">
                        Тип оплаты
                    </th>
                    <th class="text-center">
                        Доставка
                    </th>
                    <th class="text-center">
                        Адрес доставки
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr
                    v-for="item in orders"
                    :key="item.id"
                    class="text-center"
                >
                    <td>{{ item.id }}</td>
                    <td>{{ formatPrice(item.total_price) }}</td>
                    <td>
                        <Link :href="OrderController.show(item.id).url"
                              class="text-decoration-none text-green-darken-3" title="Подробнее"> {{ item.status_text }}
                        </Link>
                    </td>
                    <td>{{ item.type_paid_text }}
                        <PaymentBtn :order="item"/>
                    </td>
                    <td>{{ item.need_delivery ? 'Да' : 'Нет' }}</td>
                    <td>{{ item.need_delivery ? user.address : '-' }}</td>
                </tr>
                </tbody>
            </VTable>
        </VContainer>
    </MainLayout>
</template>

<script setup lang="ts">

import MainLayout from "~vue/Layouts/MainLayout.vue";
import type {OrderPublicResource, UserAuthResource} from "~types/generated.ts";
import {formatPrice} from "~vue/shared/formatters.ts";
import {Link} from "@inertiajs/vue3";
import OrderController from "~routes/Client/OrderController.ts";
import PaymentBtn from "~vue/components/orders/PaymentBtn.vue";

const {orders, user} = defineProps<{ orders: OrderPublicResource[], user: UserAuthResource }>()
</script>
