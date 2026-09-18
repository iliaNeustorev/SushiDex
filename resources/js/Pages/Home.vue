<template>
    <Head title="SushiDex — суши и роллы"/>
    <MainLayout>
        <section class="hero">
            <img class="hero-image" :src="'/images/sushi/hero.png'" alt="Ассорти свежих суши и роллов">
            <div class="hero-shade"></div>
            <div class="shell hero-content">
                <p class="eyebrow">
                    <span></span>
                    Японская кухня с характером
                </p>
                <h1>
                    Свежие роллы.<br>
                    <em>Честный вкус.</em>
                </h1>
                <p class="hero-copy">
                    Готовим после вашего заказа из охлаждённой рыбы, правильного риса и продуктов, которым доверяем.
                </p>
                <div class="hero-actions">
                    <Link :href="GeneralRoutes.menu().url" class="primary-action">
                        Смотреть меню
                        <span>→</span>
                    </Link>
                    <a href="#about" class="secondary-action">Почему SushiDex</a>
                </div>
            </div>
            <div class="shell hero-footer">
                <div v-for="item in advantages" :key="item.label" class="stat">
                    <strong>{{ item.value }}</strong>
                    <span>{{ item.label }}</span>
                </div>
            </div>
        </section>

        <section id="menu" class="menu-section">
            <div class="shell">
                <div class="section-heading">
                    <div>
                        <p class="eyebrow dark">
                            <span></span>
                            Выбор гостей
                        </p>
                        <h2>Начните с любимых</h2>
                    </div>
                    <p>Три хита, с которых приятно начать знакомство с SushiDex.</p>
                </div>
                <div v-if="topProducts.length > 0" class="menu-grid">
                    <article v-for="product in topProducts" :key="product.id" class="menu-card">
                        <CardProduct :product="product"/>
                    </article>
                </div>
                <div v-else class="menu-grid">
                    <h3>Пока пусто</h3>
                </div>
            </div>
        </section>

        <section id="about" class="story-section">
            <div class="shell story-grid">
                <div>
                    <p class="eyebrow">
                        <span></span>
                        Наша философия
                    </p>
                    <h2>Просто хорошие продукты — без компромиссов</h2>
                </div>
                <div class="story-copy">
                    <p>
                        Мы не готовим заранее. Каждый сет собирается только после заказа, поэтому рис остаётся
                        тёплым, нори — хрустящим, а рыба — свежей.
                    </p>
                    <div id="delivery" class="delivery-note">
                        <strong>Бесплатная доставка</strong>
                        <span>При заказе от 1 500 ₽ в пределах города</span>
                    </div>
                </div>
            </div>
        </section>
    </MainLayout>
</template>

<script setup lang="ts">
import {Head, Link} from '@inertiajs/vue3';
import MainLayout from "~vue/Layouts/MainLayout.vue";
import GeneralRoutes from '~routes/GeneralController';
import CardProduct from "~vue/components/products/CardProduct.vue";
import type {ProductPublicResource} from "~types/generated.ts";

const {topProducts} = defineProps<{ topProducts: ProductPublicResource[] }>();

const advantages = [
    {value: '35 мин', label: 'среднее время доставки'},
    {value: '4.9', label: 'рейтинг наших гостей'},
    {value: '7 дней', label: 'готовим без выходных'},
];
</script>

<style scoped src="~css/pages/home.css"></style>
