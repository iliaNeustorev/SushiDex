<template>
    <Head title="SushiDex — суши и роллы"/>
    <MainLayout>
        <section class="hero">
            <img class="hero-image" :src="'/images/sushi/hero.png'" alt="Ассорти свежих суши и роллов">
            <div class="hero-shade"></div>
            <div class="shell hero-content">
                <p class="eyebrow"><span></span> Японская кухня с характером</p>
                <h1>Свежие роллы.<br><em>Честный вкус.</em></h1>
                <p class="hero-copy">Готовим после вашего заказа из охлаждённой рыбы, правильного риса и продуктов,
                    которым доверяем.</p>
                <div class="hero-actions">
                    <a href="#menu" class="primary-action">Смотреть меню <span>→</span></a>
                    <a href="#about" class="secondary-action">Почему Yoko</a>
                </div>
            </div>
            <div class="shell hero-footer">
                <div v-for="item in advantages" :key="item.label" class="stat">
                    <strong>{{ item.value }}</strong><span>{{ item.label }}</span>
                </div>
            </div>
        </section>

        <section id="menu" class="menu-section">
            <div class="shell">
                <div class="section-heading">
                    <div><p class="eyebrow dark"><span></span> Выбор гостей</p>
                        <h2>Начните с любимых</h2></div>
                    <p>Три хита, с которых приятно начать знакомство с Yoko.</p>
                </div>
                <div class="menu-grid">
                    <article v-for="item in menuItems" :key="item.name" class="menu-card">
                        <div class="dish-art" :style="{backgroundColor: item.accent}"><span>{{ item.symbol }}</span>
                        </div>
                        <div class="dish-body">
                            <div class="dish-title"><h3>{{ item.name }}</h3><strong>{{ item.price }}</strong></div>
                            <p>{{ item.description }}</p>
                            <button type="button" class="add-button">Добавить <span>＋</span></button>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <section id="about" class="story-section">
            <div class="shell story-grid">
                <div><p class="eyebrow"><span></span> Наша философия</p>
                    <h2>Просто хорошие продукты — без компромиссов</h2></div>
                <div class="story-copy">
                    <p>Мы не готовим заранее. Каждый сет собирается только после заказа, поэтому рис остаётся
                        тёплым, нори — хрустящим, а рыба — свежей.</p>
                    <div id="delivery" class="delivery-note"><strong>Бесплатная доставка</strong><span>При заказе от 1 500 ₽ в пределах города</span>
                    </div>
                </div>
            </div>
        </section>
    </MainLayout>
</template>
<script setup lang="ts">
import {Head} from '@inertiajs/vue3';
import MainLayout from "~vue/Layouts/MainLayout.vue";

const advantages = [
    {value: '35 мин', label: 'среднее время доставки'},
    {value: '4.9', label: 'рейтинг наших гостей'},
    {value: '7 дней', label: 'готовим без выходных'},
];
const menuItems = [
    {
        name: 'Филадельфия Classic',
        description: 'Лосось, сливочный сыр, огурец и японский рис',
        price: '590 ₽',
        accent: '#f29b76',
        symbol: '鮭'
    },
    {
        name: 'Тунец Tataki',
        description: 'Опалённый тунец, авокадо, кунжут и соус понзу',
        price: '640 ₽',
        accent: '#d85c64',
        symbol: '鮪'
    },
    {
        name: 'Green Maki',
        description: 'Авокадо, огурец, чука и ореховый соус',
        price: '390 ₽',
        accent: '#81965c',
        symbol: '緑'
    },
];
</script>
