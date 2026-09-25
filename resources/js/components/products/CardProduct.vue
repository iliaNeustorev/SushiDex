<template>
    <div class="product-art">
        <VCarousel
            v-if="product.images.length"
            height="100%"
            :transition-duration="600"
            hide-delimiters
        >
            <VCarouselItem
                v-for="image in product.images"
                :key="image.id"
                :src="image.url"
                cover
            />
        </VCarousel>
        <div v-else class="product-art-empty" aria-hidden="true">よ</div>
    </div>
    <div class="product-copy">
        <div class="product-title">
            <h3>{{ product.title }}</h3>
            <strong>{{ product.price }} ₽</strong>
        </div>
        <p>{{ product.description }}</p>
        <div class="product-meta">
            <span class="product-content">{{ product.content }}</span>

            <div class="product-cart-controls">
                <button
                    v-if="productCount > 0"
                    :disabled="isProductUpdating"
                    type="button"
                    title="Удалить из корзины"
                    @click="updateCount('remove', product.id)"
                >
                    −
                </button>

                <span
                    v-if="productCount > 0"
                    class="product-cart-count"
                >
                    {{ productCount }}
                </span>

                <button
                    type="button"
                    title="Добавить в корзину"
                    :disabled="isProductUpdating || productCount === 100"
                    @click="updateCount('add', product.id)"
                >
                    ＋
                </button>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import type {ProductPublicResource} from "~types/generated.ts";
import {useCartStore} from "~vue/stores/cart.ts";
import {computed} from 'vue';

const {product} = defineProps<{ product: ProductPublicResource }>()
const cartStore = useCartStore()

const productCount = computed(() => {
    return cartStore.productCount(product.id);
});

const isProductUpdating = computed(() => {
    return cartStore.isProductUpdating(product.id);
});

type MethodCard = 'add' | 'remove'

async function updateCount(method: MethodCard, id: number): Promise<void> {
    let currentCount = productCount.value
    if (method === 'add') {
        currentCount += 1
    }
    if (method === 'remove') {
        if (currentCount != 0) {
            currentCount -= 1

        }
    }
    try {
        await cartStore.updateProductCount(id, currentCount, product)
    } catch (e) {

    }
}
</script>

<style scoped>
.product-art-empty {
    display: grid;
    position: absolute;
    inset: 0;
    height: 100%;
    place-items: center;
    background: #d9a183;
    color: rgba(255, 255, 255, .78);
    font-family: 'Prata', serif;
    font-size: 64px;
}

.product-copy {
    display: flex;
    flex-direction: column;
    min-width: 0;
    padding: 25px;
}

.product-copy > p {
    margin: 15px 0 20px;
    color: #81766c;
    font-size: 13px;
    line-height: 1.65;
}

.product-title {
    display: flex;
    align-items: start;
    justify-content: space-between;
    gap: 14px;
}

.product-art {
    position: relative;
    min-width: 0;
    min-height: 240px;
    overflow: hidden;
    background: #e6d9c9;
}

.product-meta {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-top: auto;
    color: #9b8f84;
    font-size: 11px;
}

.product-content {
    min-width: 0;
}

.product-cart-controls {
    display: flex;
    align-items: center;
    flex: none;
    gap: 8px;
    margin-left: auto;
}

.product-cart-controls button {
    display: inline-grid;
    width: 38px;
    height: 38px;
    padding: 0;
    place-items: center;
    flex: none;
    border: 0;
    line-height: 1;
    background: #171716;
    color: #fff;
    font-size: 20px;
    cursor: pointer;
    transition: background-color .2s;
}

.product-cart-count {
    min-width: 28px;
    color: #4f4943;
    font-size: 17px;
    font-weight: 600;
    line-height: 1;
    text-align: center;
}

.product-cart-controls button:hover {
    background: #df5f45;
}

.product-cart-controls button:focus-visible {
    outline: 2px solid #df5f45;
    outline-offset: 3px;
}

.product-art :deep(.v-carousel) {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100% !important;
}

.product-art :deep(.v-carousel__controls) {
    background: rgba(23, 23, 22, .34);
}

.product-title h3 {
    margin: 0;
    font-family: 'Prata', serif;
    font-size: 19px;
    font-weight: 400;
    line-height: 1.35;
}

.product-title strong {
    color: #df5f45;
    font-size: 15px;
    white-space: nowrap;
}

.product-cart-controls button:disabled {
    opacity: .5;
}

@media (max-width: 560px) {

    .product-art {
        height: 220px;
        min-height: 220px;
    }

    .product-copy {
        padding: 22px;
    }
}
</style>
