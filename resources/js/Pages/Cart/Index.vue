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
                                        @click="showConfirmOrder = true"
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
                                <VDialog
                                    :model-value="showConfirmOrder"
                                    max-width="420"
                                >
                                    <VCard v-if="showConfirmOrder">
                                        <VCardTitle>Подтвердите заказ</VCardTitle>
                                        <VContainer>
                                            <VAlert
                                                v-if="user?.address === null || user?.address === ''"
                                                class="mb-2"
                                                border="top"
                                                type="warning"
                                                variant="outlined"
                                                density="compact"
                                                prominent
                                                closable
                                            >
                                                Доставка не возможна пока в профиле не указан адрес!
                                                <Link
                                                    :href="ProfileRoutes.index().url"
                                                    class="text-decoration-none text-green-darken-3"
                                                    title="Перейти на страницу профиля"
                                                >
                                                    Указать адрес доставки
                                                </Link>
                                            </VAlert>
                                            <VCheckbox
                                                v-model="formOrder.need_delivery"
                                                :error-messages="formOrder.errors.need_delivery"
                                                color="#df5f45"
                                                density="compact"
                                                label="Доставка"
                                                :disabled="user?.address === null || user?.address === ''"
                                            />
                                            <VAlert
                                                v-if="formOrder.need_delivery"
                                                border="start"
                                                border-color="success"
                                                elevation="1"
                                                class="mb-4"
                                                density="compact"
                                            >
                                                Заказ будет доставлен по этому адресу
                                                <Link
                                                    :href="ProfileRoutes.index().url"
                                                    class="text-orange-accent-4 text-decoration-none"
                                                    title="Перейти на страницу профиля"
                                                >
                                                    {{ user?.address }}
                                                </Link>
                                            </VAlert>
                                            <VSelect
                                                v-model="formOrder.type"
                                                :error-messages="formOrder.errors.type"
                                                :items="Object.keys(typePaid).map(Number)"
                                                :item-title="id => typePaid[id] ?? 'Выберите тип'"
                                                :item-value="id => id"
                                                label="Тип оплаты"
                                                variant="solo-filled"
                                            />
                                        </VContainer>
                                        <VCardActions>
                                            <VBtn
                                                :disabled="formOrder.processing"
                                                @click="showConfirmOrder = false"
                                            >
                                                Отмена
                                            </VBtn>
                                            <VBtn
                                                :loading="formOrder.processing"
                                                color="success"
                                                :disabled="!formOrder.isDirty"
                                                @click="sendFormOrder"
                                            >
                                                ОК
                                            </VBtn>
                                        </VCardActions>
                                    </VCard>
                                </VDialog>
                            </VCard>
                        </VCol>
                    </VRow>
                </VCol>
            </VRow>
        </VContainer>
    </MainLayout>
</template>

<script setup lang="ts">

import {Link, router, useForm} from '@inertiajs/vue3';
import {computed, ref, shallowRef} from 'vue';
import RegisterRoutes from '~routes/Auth/RegisterController.ts';
import SessionRoutes from '~routes/Auth/SessionController.ts';
import CartRoutes from '~routes/Client/CartController.ts';
import MainLayout from "~vue/Layouts/MainLayout.vue";
import {formatPrice} from "~vue/shared/formatters.ts";
import {useCartStore} from "~vue/stores/cart.ts";
import type {
    CartPublicDetailsResource,
    UserAuthResource
} from "~types/generated.ts";
import type {CartDisplayItem} from '~vue/types/cart';
import ProfileRoutes from "~routes/Client/ProfileController.ts";
import type {CreateOrderForm} from "~vue/shared/forms.ts";
import OrderRoutes from "~routes/Client/OrderController.ts";

const {cartDetails, user, typePaid} = defineProps<{
    cartDetails: CartPublicDetailsResource[],
    user: UserAuthResource | null,
    typePaid: Record<number, string>,
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
    return String(Number(item.price) * cartStore.productCount(item.id));
}

const showConfirmOrder = shallowRef(false);

const formOrder = useForm<CreateOrderForm>({
    type: 0,
    need_delivery: false,
});

function sendFormOrder(): void {
    formOrder.submit(OrderRoutes.store(), {
        preserveScroll: true,
        preserveState: true,
    });
}
</script>
