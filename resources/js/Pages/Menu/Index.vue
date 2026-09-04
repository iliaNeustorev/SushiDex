<template>
    <Head title="Меню — SushiDex" />
    <MainLayout>
        <main class="inner-page menu-page"><div class="shell">
            <header class="page-intro">
                <p class="eyebrow dark"><span></span> Меню SushiDex</p><h1>Выберите свой вкус</h1>
                <p>Роллы, суши и тёплые блюда готовим только после заказа. Здесь пока тестовое меню без корзины и фильтрации.</p>
            </header>
            <nav class="category-list" aria-label="Категории меню">
                <span v-for="category in categories" :key="category.title" :class="{ active: category.active }">{{ category.title }} <small>{{ category.count }}</small></span>
            </nav>
            <section class="catalog-section">
                <div class="catalog-heading"><div><p>Категория 01</p><h2>Роллы</h2></div><span>8 позиций</span></div>
                <div class="product-grid">
                    <article v-for="product in products" :key="product.name" class="product-card">
                        <div class="product-art" :style="{ background: product.background }"><span>{{ product.symbol }}</span><small v-if="product.badge">{{ product.badge }}</small></div>
                        <div class="product-copy">
                            <div class="product-title"><h3>{{ product.name }}</h3><strong>{{ product.price }}</strong></div><p>{{ product.description }}</p>
                            <div class="product-meta"><span>{{ product.weight }}</span><button type="button" aria-label="Добавить товар">＋</button></div>
                        </div>
                    </article>
                </div>
            </section>
        </div></main>
    </MainLayout>
</template>
<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import MainLayout from '~vue/Layouts/MainLayout.vue';
const categories = [{ title: 'Роллы', count: '08', active: true }, { title: 'Суши', count: '06', active: false }, { title: 'Сеты', count: '05', active: false }, { title: 'Горячее', count: '07', active: false }, { title: 'Напитки', count: '09', active: false }];
const products = [
    { name: 'Филадельфия Classic', description: 'Лосось, сливочный сыр, огурец, японский рис', price: '590 ₽', weight: '260 г', symbol: '鮭', badge: 'Хит', background: '#e89472' },
    { name: 'Тунец Tataki', description: 'Опалённый тунец, авокадо, кунжут и соус понзу', price: '640 ₽', weight: '245 г', symbol: '鮪', badge: '', background: '#c95c62' },
    { name: 'Green Maki', description: 'Авокадо, огурец, чука и нежный ореховый соус', price: '390 ₽', weight: '220 г', symbol: '緑', badge: 'Veg', background: '#879866' },
    { name: 'Креветка Tempura', description: 'Хрустящая креветка, манго, тобико и спайси-соус', price: '620 ₽', weight: '275 г', symbol: '海', badge: 'New', background: '#d7a65d' },
    { name: 'Угорь Unagi', description: 'Копчёный угорь, авокадо, кунжут и соус унаги', price: '680 ₽', weight: '250 г', symbol: '鰻', badge: '', background: '#8b6954' },
    { name: 'Краб & Манго', description: 'Снежный краб, спелое манго, огурец и икра масаго', price: '560 ₽', weight: '240 г', symbol: '蟹', badge: '', background: '#cf795d' },
];
</script>
<style scoped>
.category-list{display:flex;gap:10px;margin-bottom:68px;overflow-x:auto;padding-bottom:8px}.category-list span{min-width:max-content;padding:13px 19px;border:1px solid #d9cec2;color:#756b62;font-size:13px;font-weight:700}.category-list span.active{border-color:#171716;background:#171716;color:#fff}.category-list small{margin-left:8px;color:#a99c91;font-size:9px}.catalog-heading{display:flex;align-items:end;justify-content:space-between;padding-bottom:22px;border-bottom:1px solid #d9cec2}.catalog-heading p{margin:0 0 8px;color:#b16a56;font-size:10px;font-weight:700;letter-spacing:.16em;text-transform:uppercase}.catalog-heading h2{margin:0;font-family:'Prata',serif;font-size:38px;font-weight:400}.catalog-heading>span{color:#8b8076;font-size:12px}.product-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:22px;margin-top:28px}.product-card{display:grid;grid-template-columns:210px 1fr;min-height:240px;overflow:hidden;border:1px solid #e0d6cb;background:#fffaf4;transition:transform .25s,box-shadow .25s}.product-card:hover{transform:translateY(-4px);box-shadow:0 18px 40px rgba(51,38,27,.08)}.product-art{position:relative;display:grid;place-items:center;overflow:hidden}.product-art::before,.product-art::after{content:'';position:absolute;width:130px;height:130px;border:1px solid rgba(255,255,255,.42);border-radius:50%}.product-art::after{width:94px;height:94px;border-color:rgba(23,23,22,.13)}.product-art>span{position:relative;z-index:1;color:rgba(255,255,255,.9);font-family:serif;font-size:58px}.product-art small{position:absolute;top:16px;left:16px;z-index:2;padding:5px 8px;background:#fffaf4;color:#332d28;font-size:9px;font-weight:800;letter-spacing:.1em;text-transform:uppercase}.product-copy{display:flex;flex-direction:column;padding:27px}.product-title{display:flex;align-items:start;justify-content:space-between;gap:12px}.product-title h3{margin:0;font-family:'Prata',serif;font-size:19px;font-weight:400;line-height:1.35}.product-title strong{color:#df5f45;white-space:nowrap}.product-copy>p{margin:15px 0;color:#81766c;font-size:12px;line-height:1.65}.product-meta{display:flex;align-items:center;justify-content:space-between;margin-top:auto;color:#9b8f84;font-size:11px}.product-meta button{width:38px;height:38px;border:0;background:#171716;color:#fff;font-size:20px;cursor:pointer}@media(max-width:1000px){.product-grid{grid-template-columns:1fr}}@media(max-width:560px){.category-list{margin-bottom:45px}.product-card{grid-template-columns:1fr}.product-art{min-height:190px}}
</style>
