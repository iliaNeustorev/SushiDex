<template>
    <MainLayout>
        <VContainer>
            <VRow>
                <VCol
                    cols="12"
                >
                    <VCard>
                        <VCardTitle
                            v-if="visibleCartDetails.length > 0"
                            class="d-flex align-center"
                        >
                            <span>Корзина</span>

                            <VSpacer/>

                            <VBtn
                                prepend-icon="$deleteOutline"
                                color="warning"
                                size="medium"
                                variant="tonal"
                                :loading="isClearingCart"
                                @click="clearCart"
                            >
                                Очистить корзину
                            </VBtn>
                        </VCardTitle>

                        <VList v-if="visibleCartDetails.length > 0">
                            <template
                                v-for="item in visibleCartDetails"
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
                                        <span class="text-teal-darken-4"
                                              v-if="cartStore.productCount(item.id) > 1"> ({{
                                                formatPrice(calculateItemTotal(item))
                                            }})</span>
                                    </VListItemSubtitle>

                                    <template #append>
                                        <VNumberInput
                                            :model-value="cartStore.productCount(item.id)"
                                            :disabled="cartStore.isProductUpdating(item.id)"
                                            :min="0"
                                            :max="100"
                                            control-variant="split"
                                            density="compact"
                                            hide-details
                                            @update:model-value="cartStore.updateProductCount(item.id, $event)"
                                        />

                                        <VBtn
                                            icon="$deleteOutline"
                                            color="error"
                                            variant="text"
                                            @click="cartStore.updateProductCount(item.id, 0)"
                                        />
                                    </template>
                                </VListItem>

                                <VDivider/>
                            </template>
                        </VList>
                        <VAlert v-else>
                            Корзина пуста
                        </VAlert>
                    </VCard>

                    <VRow
                        v-if="visibleCartDetails.length > 0"
                        class="mt-2"
                        justify="end"
                    >
                        <VCol
                            cols="12"
                            md="5"
                        >
                            <VCard>
                                <VCardTitle>Ваш заказ</VCardTitle>

                                <VCardText>
                                    Итого: {{ formatPrice(cartStore.totalAmount.toString()) }}
                                </VCardText>

                                <VCardActions>
                                    <VBtn
                                        v-if="user"
                                        color="success"
                                        variant="flat"
                                        block
                                    >
                                        Оформить заказ
                                    </VBtn>

                                    <template v-else>
                                        <VBtn
                                            :href="SessionRoutes.create().url"
                                            color="success"
                                            variant="flat"
                                        >
                                            Войти
                                        </VBtn>

                                        <VBtn
                                            :href="RegisterRoutes.create().url"
                                            color="primary"
                                            variant="outlined"
                                        >
                                            Зарегистрироваться
                                        </VBtn>
                                    </template>
                                </VCardActions>
                            </VCard>
                        </VCol>
                    </VRow>
                </VCol>
            </VRow>
        </VContainer>
    </MainLayout>
</template>

<script setup lang="ts">

import {router} from '@inertiajs/vue3';
import {computed, ref} from 'vue';
import RegisterRoutes from '~routes/Auth/RegisterController.ts';
import SessionRoutes from '~routes/Auth/SessionController.ts';
import CartRoutes from '~routes/CartController.ts';
import MainLayout from "~vue/Layouts/MainLayout.vue";
import {formatPrice} from "~vue/shared/formatters.ts";
import {useCartStore} from "~vue/stores/cart.ts";
import type {CartPublicDetailsResource, UserAuthResource} from "~types/generated.ts";
import type {CartDisplayItem} from '~vue/types/cart';

const {cartDetails, user} = defineProps<{
    cartDetails: CartPublicDetailsResource[],
    user: UserAuthResource | null,
}>();
const cartStore = useCartStore();
const isClearingCart = ref(false);

const visibleCartDetails = computed<CartDisplayItem[]>(() => {
    if (user) {
        return cartDetails.filter(item =>
            cartStore.items.some(cartItem => cartItem.id === item.id),
        );
    }

    return cartStore.items.map(item => ({
        id: item.id,
        title: item.title,
        price: item.price,
        count: item.count,
        previewImage: 'previewImage' in item
            ? item.previewImage
            : null,
    }));
});

function clearCart(): void {
    if (!user) {
        cartStore.clearGuestCart();

        return;
    }

    router.delete(CartRoutes.destroy(), {
        preserveScroll: true,
        onStart: () => isClearingCart.value = true,
        onSuccess: () => cartStore.setCart({
            items: [],
            total_price: 0,
        }),
        onFinish: () => isClearingCart.value = false,
    });
}

function calculateItemTotal(item: CartDisplayItem): string {
    return String(Number(item.price) * cartStore.productCount(item.id))
}
</script>
