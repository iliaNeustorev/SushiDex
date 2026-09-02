<script setup lang="ts">
import {Head, Link} from '@inertiajs/vue3';

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

<template>
    <Head title="Yoko — суши и роллы"/>
    <VApp class="sushi-page">
        <header class="site-header">
            <div class="shell header-inner">
                <Link href="/" class="brand" aria-label="Yoko — на главную"><span
                    class="brand-mark">よ</span><span>YOKO</span></Link>
                <nav class="nav-links" aria-label="Основная навигация">
                    <a href="#menu">Меню</a><a href="#about">О нас</a><a href="#delivery">Доставка</a>
                </nav>
                <a class="phone" href="tel:+74951234567"><span>Ежедневно 11:00–23:00</span>+7 495 123-45-67</a>
            </div>
        </header>

        <VMain>
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
                    <div v-for="item in advantages" :key="item.label" class="stat"><strong>{{
                            item.value
                        }}</strong><span>{{ item.label }}</span></div>
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
        </VMain>

        <footer class="site-footer">
            <div class="shell footer-inner">
                <div class="brand light"><span class="brand-mark">よ</span><span>YOKO</span></div>
                <p>Суши и роллы, приготовленные с уважением к продукту.</p><span>© {{
                    new Date().getFullYear()
                }} Yoko</span>
            </div>
        </footer>
    </VApp>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=Prata&display=swap');

.sushi-page {
    --ink: #171716;
    --paper: #f5f0e8;
    --accent: #df5f45;
    background: var(--paper);
    color: var(--ink);
    font-family: 'Manrope', sans-serif
}

.shell {
    width: min(1180px, calc(100% - 48px));
    margin: 0 auto
}

.site-header {
    background: #151514;
    color: #fff;
    border-bottom: 1px solid rgba(255, 255, 255, .12)
}

.header-inner {
    min-height: 78px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 32px
}

.brand {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    color: #171716;
    text-decoration: none;
    font-size: 19px;
    font-weight: 700;
    letter-spacing: .24em
}

.site-header .brand, .brand.light {
    color: #fff
}

.brand-mark {
    width: 36px;
    height: 36px;
    display: grid;
    place-items: center;
    border: 1px solid var(--accent);
    border-radius: 50%;
    color: var(--accent);
    font-family: serif;
    letter-spacing: 0
}

.nav-links {
    display: flex;
    gap: 34px
}

.nav-links a {
    color: rgba(255, 255, 255, .72);
    text-decoration: none;
    font-size: 14px;
    transition: color .2s
}

.nav-links a:hover {
    color: #fff
}

.phone {
    color: #fff;
    text-align: right;
    text-decoration: none;
    font-weight: 600;
    font-size: 14px
}

.phone span {
    display: block;
    color: rgba(255, 255, 255, .5);
    font-size: 10px;
    font-weight: 500;
    letter-spacing: .08em
}

.hero {
    min-height: 720px;
    position: relative;
    display: flex;
    align-items: center;
    overflow: hidden;
    background: #151514;
    color: #fff
}

.hero-image, .hero-shade {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%
}

.hero-image {
    object-fit: cover;
    object-position: center
}

.hero-shade {
    background: linear-gradient(90deg, rgba(15, 15, 14, .97) 0%, rgba(15, 15, 14, .86) 38%, rgba(15, 15, 14, .22) 72%, rgba(15, 15, 14, .16) 100%)
}

.hero-content {
    position: relative;
    z-index: 1;
    padding-bottom: 86px
}

.eyebrow {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 24px;
    color: rgba(255, 255, 255, .65);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .19em;
    text-transform: uppercase
}

.eyebrow span {
    width: 34px;
    height: 1px;
    background: var(--accent)
}

.eyebrow.dark {
    color: #806f62
}

.hero h1, .section-heading h2, .story-section h2 {
    font-family: 'Prata', serif;
    font-weight: 400
}

.hero h1 {
    max-width: 700px;
    margin: 0;
    font-size: clamp(54px, 7vw, 94px);
    line-height: 1.06;
    letter-spacing: -.04em
}

.hero h1 em {
    color: #f09172;
    font-style: normal
}

.hero-copy {
    max-width: 530px;
    margin: 30px 0 36px;
    color: rgba(255, 255, 255, .68);
    font-size: 17px;
    line-height: 1.75
}

.hero-actions {
    display: flex;
    align-items: center;
    gap: 28px
}

.primary-action, .secondary-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    font-weight: 700
}

.primary-action {
    min-height: 56px;
    padding: 0 26px;
    gap: 28px;
    background: var(--accent);
    color: #fff
}

.primary-action:hover {
    background: #ed6c51
}

.secondary-action {
    color: #fff;
    border-bottom: 1px solid rgba(255, 255, 255, .45);
    padding: 10px 0
}

.hero-footer {
    position: absolute;
    z-index: 1;
    left: 50%;
    bottom: 0;
    transform: translateX(-50%);
    display: flex;
    gap: 58px;
    padding: 24px 0;
    border-top: 1px solid rgba(255, 255, 255, .15)
}

.stat {
    display: flex;
    align-items: baseline;
    gap: 10px
}

.stat strong {
    font-family: 'Prata', serif;
    font-size: 23px;
    font-weight: 400
}

.stat span {
    color: rgba(255, 255, 255, .5);
    font-size: 11px
}

.menu-section {
    padding: 112px 0 124px
}

.section-heading {
    display: flex;
    align-items: end;
    justify-content: space-between;
    gap: 40px;
    margin-bottom: 48px
}

.section-heading h2, .story-section h2 {
    margin: 0;
    font-size: clamp(38px, 5vw, 58px);
    line-height: 1.15
}

.section-heading > p {
    max-width: 360px;
    color: #786f66;
    line-height: 1.7
}

.menu-grid {
    display: grid;
    grid-template-columns:repeat(3, 1fr);
    gap: 22px
}

.menu-card {
    overflow: hidden;
    background: #fffaf4;
    border: 1px solid #e7ded3;
    transition: transform .25s, box-shadow .25s
}

.menu-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 45px rgba(51, 38, 27, .09)
}

.dish-art {
    height: 210px;
    display: grid;
    place-items: center;
    position: relative;
    overflow: hidden
}

.dish-art:before, .dish-art:after {
    content: '';
    position: absolute;
    width: 145px;
    height: 145px;
    border: 1px solid rgba(255, 255, 255, .42);
    border-radius: 50%
}

.dish-art:after {
    width: 110px;
    height: 110px;
    border-color: rgba(20, 20, 20, .13)
}

.dish-art span {
    position: relative;
    z-index: 1;
    color: rgba(255, 255, 255, .88);
    font-family: serif;
    font-size: 66px
}

.dish-body {
    padding: 27px
}

.dish-title {
    display: flex;
    align-items: start;
    justify-content: space-between;
    gap: 16px
}

.dish-title h3 {
    margin: 0;
    font-family: 'Prata', serif;
    font-size: 20px;
    font-weight: 400
}

.dish-title strong {
    white-space: nowrap;
    color: var(--accent)
}

.dish-body p {
    min-height: 48px;
    margin: 14px 0 22px;
    color: #81766c;
    font-size: 13px;
    line-height: 1.65
}

.add-button {
    width: 100%;
    padding: 13px 16px;
    display: flex;
    justify-content: space-between;
    border: 1px solid #d7ccc0;
    background: transparent;
    color: var(--ink);
    cursor: pointer;
    font: inherit;
    font-weight: 700
}

.add-button:hover {
    border-color: var(--accent);
    color: var(--accent)
}

.story-section {
    padding: 100px 0;
    background: #252522;
    color: #fff
}

.story-grid {
    display: grid;
    grid-template-columns:1.1fr .9fr;
    gap: 100px;
    align-items: center
}

.story-copy > p {
    color: rgba(255, 255, 255, .65);
    font-size: 17px;
    line-height: 1.8
}

.delivery-note {
    margin-top: 32px;
    padding: 22px 0;
    display: flex;
    flex-direction: column;
    gap: 5px;
    border-top: 1px solid rgba(255, 255, 255, .15);
    border-bottom: 1px solid rgba(255, 255, 255, .15)
}

.delivery-note strong {
    color: #f09172
}

.delivery-note span {
    color: rgba(255, 255, 255, .52);
    font-size: 13px
}

.site-footer {
    background: #151514;
    color: rgba(255, 255, 255, .5)
}

.footer-inner {
    min-height: 116px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 28px;
    font-size: 12px
}

@media (max-width: 850px) {
    .nav-links {
        display: none
    }

    .hero {
        min-height: 760px
    }

    .hero-image {
        object-position: 65% center
    }

    .hero-shade {
        background: linear-gradient(90deg, rgba(15, 15, 14, .95), rgba(15, 15, 14, .65))
    }

    .hero-footer {
        gap: 25px
    }

    .stat {
        display: block
    }

    .stat span {
        display: block;
        margin-top: 4px
    }

    .menu-grid {
        grid-template-columns:1fr
    }

    .dish-art {
        height: 180px
    }

    .story-grid {
        grid-template-columns:1fr;
        gap: 44px
    }
}

@media (max-width: 560px) {
    .shell {
        width: min(100% - 32px, 1180px)
    }

    .header-inner {
        min-height: 68px
    }

    .phone span {
        display: none
    }

    .phone {
        font-size: 12px
    }

    .hero {
        min-height: 730px;
        align-items: start;
        padding-top: 90px
    }

    .hero h1 {
        font-size: 48px
    }

    .hero-copy {
        font-size: 15px
    }

    .hero-actions {
        align-items: stretch;
        flex-direction: column;
        gap: 12px;
        width: 100%
    }

    .hero-footer {
        display: none
    }

    .section-heading {
        align-items: start;
        flex-direction: column
    }

    .menu-section, .story-section {
        padding: 78px 0
    }

    .footer-inner {
        padding: 28px 0;
        align-items: start;
        flex-direction: column
    }
}
</style>
