<template>
    <section class="tab-card">
        <header class="tab-heading">
            <div>
                <span>История покупок</span>
                <h2>Мои заказы</h2>
            </div>
            <strong v-if="orders.length" class="orders-count">
                {{ orders.length }}
            </strong>
        </header>

        <div v-if="orders.length" class="orders-list">
            <article
                v-for="order in orders"
                :key="order.id"
                class="order-card"
            >
                <header class="order-header">
                    <div class="order-title">
                        <span class="order-caption">Заказ</span>
                        <h3>№ {{ order.id }}</h3>
                        <time :datetime="order.created_at">
                            {{ formatDate(order.created_at) }}
                        </time>
                    </div>

                    <span class="status-badge">
                        {{ order.status_text }}
                    </span>
                </header>

                <dl class="order-details">
                    <div>
                        <dt>Сумма заказа</dt>
                        <dd class="order-price">{{ formatPrice(order.total_price) }}</dd>
                    </div>
                    <div>
                        <dt>Способ оплаты</dt>
                        <dd>{{ order.type_paid_text }}</dd>
                    </div>
                    <div>
                        <dt>Получение</dt>
                        <dd>{{ order.need_delivery ? 'Доставка курьером' : 'Самовывоз' }}</dd>
                    </div>
                    <div>
                        <dt>Состав</dt>
                        <dd>{{ formatItemsCount(order.items_count) }}</dd>
                    </div>
                </dl>
            </article>
        </div>
        <div v-else class="empty-state">
            <strong>История заказов появится здесь</strong>
            <p>В этом разделе будут собраны ваши заказы, их состав, стоимость и текущий статус.</p>
        </div>
    </section>
</template>

<script setup lang="ts">
import type {OrderPublicResource} from '~types/generated';
import {formatDate, formatItemsCount, formatPrice} from '~vue/shared/formatters';

const {orders} = defineProps<{
    orders: OrderPublicResource[];
}>();
</script>

<style scoped>
.tab-card {
    padding: 31px;
    border: 1px solid #e0d6cb;
    background: #fffaf4;
}

.tab-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 24px;
    padding-bottom: 22px;
    border-bottom: 1px solid #e0d6cb;
}

.tab-heading span {
    color: #9c8f84;
    font-size: 9px;
    font-weight: 700;
    letter-spacing: .14em;
    text-transform: uppercase;
}

.tab-heading h2 {
    margin: 6px 0 0;
    font-family: 'Prata', serif;
    font-size: 28px;
    font-weight: 400;
}

.orders-count {
    display: grid;
    width: 38px;
    height: 38px;
    flex: 0 0 38px;
    place-items: center;
    border: 1px solid #df5f45;
    border-radius: 50%;
    color: #df5f45;
    font-family: 'Prata', serif;
    font-size: 14px;
    font-weight: 400;
}

.orders-list {
    display: grid;
    gap: 18px;
    padding-top: 24px;
}

.order-card {
    border: 1px solid #e0d6cb;
    background: #fff;
}

.order-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 24px;
    padding: 22px 24px;
    border-bottom: 1px solid #ebe3da;
}

.order-title {
    display: grid;
    grid-template-columns: auto auto;
    align-items: baseline;
    gap: 4px 8px;
}

.order-caption,
.order-details dt {
    color: #9c8f84;
    font-size: 9px;
    font-weight: 700;
    letter-spacing: .12em;
    text-transform: uppercase;
}

.order-title h3 {
    margin: 0;
    color: #302b27;
    font-family: 'Prata', serif;
    font-size: 21px;
    font-weight: 400;
}

.order-title time {
    grid-column: 1 / -1;
    color: #81766c;
    font-size: 11px;
}

.status-badge {
    flex: 0 0 auto;
    padding: 7px 10px;
    background: #e7eee0;
    color: #687a50;
    font-size: 9px;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
}

.order-details {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    margin: 0;
}

.order-details > div {
    min-width: 0;
    padding: 20px 24px;
}

.order-details > div + div {
    border-left: 1px solid #ebe3da;
}

.order-details dd {
    overflow-wrap: anywhere;
    margin: 7px 0 0;
    color: #504941;
    font-size: 12px;
    line-height: 1.45;
}

.order-details .order-price {
    color: #302b27;
    font-family: 'Prata', serif;
    font-size: 15px;
}

.empty-state {
    display: flex;
    min-height: 260px;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 40px 20px;
    text-align: center;
}

.empty-state strong {
    font-family: 'Prata', serif;
    font-size: 22px;
    font-weight: 400;
}

.empty-state p {
    max-width: 430px;
    margin: 12px 0 0;
    color: #887d73;
    font-size: 12px;
    line-height: 1.7;
}

@media (max-width: 760px) {
    .order-details {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .order-details > div:nth-child(3) {
        border-left: 0;
    }

    .order-details > div:nth-child(n + 3) {
        border-top: 1px solid #ebe3da;
    }
}

@media (max-width: 520px) {
    .tab-card {
        padding: 22px 18px;
    }

    .order-header {
        flex-direction: column;
        gap: 14px;
        padding: 19px;
    }

    .order-details {
        grid-template-columns: 1fr;
    }

    .order-details > div {
        padding: 17px 19px;
    }

    .order-details > div + div {
        border-top: 1px solid #ebe3da;
        border-left: 0;
    }
}
</style>
