<template>
    <div class="overview-grid">
        <section class="order-section" v-if="lastOrder">
            <div class="block-heading">
                <div>
                    <span>Последний заказ</span>
                    <h2>Заказ № {{ lastOrder.id }}</h2>
                </div>
                <strong>{{ lastOrder.status_text }}</strong>
            </div>
            <div class="order-details">
                <div><span>Дата</span><strong>{{ formatDate(lastOrder.created_at) }}</strong>
                </div>
                <div><span>Состав</span><strong>{{ formatItemsCount(lastOrder.items_count) }}</strong></div>
                <div><span>Сумма</span><strong>{{ formatPrice(lastOrder.total_price) }}</strong></div>
            </div>
            <!--            <div class="order-products">-->
            <!--                <span>Филадельфия Classic × 1</span>-->
            <!--                <span>Тунец Tataki × 1</span>-->
            <!--                <span>Моти Манго × 2</span>-->
            <!--            </div>-->
        </section>

        <section class="address-card">
            <div v-if="client.address">
                <span>Основной адрес</span>
                <h2>{{ client.address }}</h2>
            </div>
            <div v-else>
                <span>Основной адрес</span>
                <h2>Адрес не указан</h2>
            </div>
            <span class="address-icon">⌖</span>
        </section>
        <section class="address-card">
            <div v-if="client.phone">
                <span>Подтвержденный номер</span>
                <h2>{{ client.phone.phone }}</h2>
                <p>Дата подтверждения — {{ formatDate(client.phone.verified_at) }}</p>
            </div>
            <div v-else>
                <span>Подтвержденный номер</span>
                <h2>Нет подвержденого номера</h2>
            </div>
            <span class="address-icon">⌖</span>
        </section>
    </div>
</template>

<script setup lang="ts">
import type {OrderPublicResource, UserProfileResource} from '~types/generated';
import {computed} from 'vue';
import {formatDate, formatItemsCount, formatPrice} from '~vue/shared/formatters';

const {client, orders} = defineProps<{
    client: UserProfileResource,
    orders: OrderPublicResource[]
}>();

const lastOrder = computed(() =>
    orders.at(0) ?? null
)
</script>

<style scoped>
.overview-grid {
    display: grid;
    grid-template-columns: 1.25fr .75fr;
    gap: 22px;
}

.order-section,
.address-card {
    padding: 31px;
    border: 1px solid #e0d6cb;
    background: #fffaf4;
}

.block-heading {
    display: flex;
    justify-content: space-between;
    gap: 20px;
    padding-bottom: 22px;
    border-bottom: 1px solid #e0d6cb;
}

.block-heading span,
.address-card span {
    color: #9c8f84;
    font-size: 9px;
    font-weight: 700;
    letter-spacing: .14em;
    text-transform: uppercase;
}

.block-heading h2,
.address-card h2 {
    margin: 6px 0 0;
    font-family: 'Prata', serif;
    font-size: 23px;
    font-weight: 400;
}

.block-heading > strong {
    align-self: start;
    padding: 7px 10px;
    background: #e7eee0;
    color: #687a50;
    font-size: 9px;
    letter-spacing: .08em;
    text-transform: uppercase;
}

.order-details {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
    padding: 22px 0;
}

.order-details div {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.order-details span {
    color: #a0958a;
    font-size: 10px;
}

.order-details strong {
    font-size: 11px;
}

.order-products {
    display: flex;
    flex-direction: column;
    gap: 9px;
    padding-top: 18px;
    border-top: 1px solid #e0d6cb;
    color: #70665d;
    font-size: 11px;
}

.address-card {
    position: relative;
}

.address-icon {
    position: absolute;
    right: 28px;
    bottom: 25px;
    color: #df5f45 !important;
    font-size: 28px !important;
}

@media (max-width: 900px) {
    .overview-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 560px) {
    .order-details {
        grid-template-columns: 1fr;
    }
}
</style>
