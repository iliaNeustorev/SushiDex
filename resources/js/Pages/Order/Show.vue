<template>
    <MainLayout>
        <VContainer>
            <VCard class="mx-auto"
                   max-width="1000">
                <VCardTitle class="d-flex justify-space-between">
                    <span>Заказ № {{ order.id }}</span>
                    <span
                        :class="[order.type_paid === TypePaid.CARD_ONLINE && order.remittance?.status === RemittanceStatus.AWAIT_PAID ? 'text-orange-darken-3' : 'text-grey-darken-3']">{{
                            order.status_text
                        }}</span>
                </VCardTitle>
                <VCardText>
                    <div class="text-headline-large font-weight-black mb-4">Сумма заказа
                        {{ formatPrice(order.total_price) }}
                    </div>
                    <p class="mb-4">Статус - {{ order.status_text }}</p>
                    <p class="mb-4">Тип оплаты - {{ order.type_paid_text }}</p>
                    <p class="mb-4">Создан - {{ formatDate(order.created_at) }}</p>
                </VCardText>
                <VCardActions>
                    <VBtn
                        :icon="showProductList ? '$mdiUp' : '$mdiDown'"
                        @click="showProductList = !showProductList"
                        title="Список заказа"
                    ></VBtn>
                    <VSpacer></VSpacer>
                    <PaymentBtn :order="order" type="default" size="x-large"/>
                </VCardActions>
                <VExpandTransition>
                    <div v-show="showProductList">
                        <VDivider></VDivider>

                        <VList>
                            <template
                                v-for="item in products"
                                :key="item.id"
                            >
                                <VListItem>
                                    <template #prepend>
                                        <VAvatar
                                            :image="item.previewImage?.url"
                                            rounded="lg"
                                            size="80"
                                        />
                                    </template>

                                    <VListItemTitle>
                                        {{ item.title }}
                                    </VListItemTitle>

                                    <VListItemSubtitle class="text-body-1">
                                        <span>{{
                                                formatPrice(item.price)
                                            }} шт.</span>
                                        <span class="text-teal-darken-4"> ({{
                                                formatPrice(item.calculateCountPrice)
                                            }})</span>
                                    </VListItemSubtitle>
                                </VListItem>

                                <VDivider/>
                            </template>
                        </VList>
                    </div>
                </VExpandTransition>
            </VCard>
        </VContainer>
    </MainLayout>
</template>

<script setup lang="ts">

import MainLayout from "~vue/Layouts/MainLayout.vue";
import {formatDate, formatPrice} from "~vue/shared/formatters.ts";
import {type OrderPublicResource, type ProductOrderItemResource, RemittanceStatus, TypePaid} from "~types/generated.ts";
import {shallowRef} from "vue";
import PaymentBtn from "~vue/components/orders/PaymentBtn.vue";

const {order, products} = defineProps<{ order: OrderPublicResource, products: ProductOrderItemResource[] }>()
const showProductList = shallowRef(false)
</script>
