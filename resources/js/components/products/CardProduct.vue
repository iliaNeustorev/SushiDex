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
            <span>{{ product.content }}</span>
            <button type="button" title="Добавить в корзину">＋</button>
        </div>
    </div>
</template>

<script setup lang="ts">
import type {ProductPublicResource} from "~types/generated.ts";

const {product} = defineProps<{ product: ProductPublicResource }>()
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
    justify-content: space-between;
    gap: 12px;
    margin-top: auto;
    color: #9b8f84;
    font-size: 11px;
}

.product-meta button {
    width: 38px;
    height: 38px;
    flex: none;
    border: 0;
    background: #171716;
    color: #fff;
    font-size: 20px;
    cursor: pointer;
    transition: background-color .2s;
}

.product-meta button:hover {
    background: #df5f45;
}

.product-meta button:focus-visible {
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
