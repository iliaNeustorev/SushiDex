<template>
    <Head :title="`${status} — ${page.title} | SushiDex`" />
    <div class="error-page">
        <header class="error-header">
            <a href="/" class="error-brand" aria-label="SushiDex — на главную">
                <span class="error-brand-mark">よ</span><span>SushiDex</span>
            </a>
            <a href="/menu" class="error-menu-link">Наше меню <span aria-hidden="true">↗</span></a>
        </header>

        <main class="error-main">
            <section class="error-card" aria-labelledby="error-title">
                <div class="error-art" aria-hidden="true">
                    <span class="error-art-label">SushiDex · маленькая пауза</span>
                    <div class="error-orbit error-orbit-outer"></div>
                    <div class="error-orbit error-orbit-inner"></div>
                    <span class="error-number">{{ status }}</span>
                    <div class="error-art-caption"><span></span>{{ page.caption }}</div>
                </div>

                <div class="error-content">
                    <p class="error-kicker"><span></span>Ошибка {{ status }}</p>
                    <h1 id="error-title">{{ page.title }}</h1>
                    <p class="error-description">{{ page.description }}</p>
                    <div class="error-actions">
                        <a href="/" class="error-primary">На главную <span aria-hidden="true">↗</span></a>
                        <button v-if="status === 500" type="button" class="error-secondary" @click="reload">
                            Обновить страницу <span aria-hidden="true">↻</span>
                        </button>
                        <a v-else href="/menu" class="error-secondary">Посмотреть меню <span aria-hidden="true">→</span></a>
                    </div>
                    <p class="error-note">{{ page.note }}</p>
                </div>
            </section>
            <p class="error-signature">С уважением к вашему времени. И с любовью к хорошей еде.</p>
        </main>

        <footer class="error-footer">
            <span>Суши и роллы, приготовленные с уважением к продукту.</span>
            <span>© {{ new Date().getFullYear() }} SushiDex</span>
        </footer>
    </div>
</template>

<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{ status: 403 | 404 | 500 }>();

const pages = {
    403: {
        title: 'Здесь только для своих',
        description: 'У вас нет доступа к этой странице. Если вы считаете, что это ошибка, обратитесь к администратору.',
        caption: 'За этой дверью — служебная зона',
        note: 'А наше меню всегда открыто для вас.',
    },
    404: {
        title: 'Кажется, здесь пусто',
        description: 'Мы не нашли эту страницу. Возможно, она переехала или в адресе закралась опечатка.',
        caption: 'Этой страницы нет в нашем меню',
        note: 'Проверьте адрес или выберите что-нибудь вкусное.',
    },
    500: {
        title: 'Небольшая заминка',
        description: 'На сервере произошла ошибка, и мы не смогли открыть страницу. Попробуйте обновить её чуть позже.',
        caption: 'Нам нужно немного времени',
        note: 'Спасибо за ваше терпение.',
    },
};

const page = computed(() => pages[props.status]);

function reload() {
    window.location.reload();
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=Prata&display=swap');

.error-page { min-height: 100svh; display: flex; flex-direction: column; background: #f5f0e8; color: #171716; font-family: 'Manrope', sans-serif; }
.error-header { min-height: 80px; padding: 20px max(24px, calc((100% - 1180px) / 2)); display: flex; align-items: center; justify-content: space-between; gap: 24px; background: #151514; }
.error-brand { display: inline-flex; align-items: center; gap: 12px; color: #fff; text-decoration: none; font-size: 19px; font-weight: 700; letter-spacing: .24em; }
.error-brand-mark { width: 36px; height: 36px; display: grid; place-items: center; border: 1px solid #df5f45; border-radius: 50%; color: #df5f45; font-family: serif; letter-spacing: 0; }
.error-menu-link { display: flex; gap: 24px; color: #eee5dc; font-size: 12px; text-decoration: none; }
.error-menu-link:hover { color: #f09172; }
.error-main { width: min(1180px, calc(100% - 48px)); flex: 1; align-content: center; margin: 0 auto; padding: 64px 0 40px; }
.error-card { display: grid; grid-template-columns: 1fr 1.1fr; min-height: 540px; background: #fffaf4; box-shadow: 0 24px 70px rgba(51, 38, 27, .07); }
.error-art { position: relative; display: flex; flex-direction: column; justify-content: space-between; align-items: center; overflow: hidden; padding: 38px; background: #252522; color: #f09172; }
.error-art-label { align-self: flex-start; color: #c1b8ac; font-size: 10px; letter-spacing: .15em; text-transform: uppercase; }
.error-number { position: relative; font-family: 'Prata', serif; font-size: clamp(110px, 14vw, 190px); line-height: 1.2; letter-spacing: -.07em; }
.error-orbit { position: absolute; top: 50%; left: 50%; width: 380px; height: 380px; transform: translate(-50%, -50%); border: 1px solid rgba(240, 145, 114, .23); border-radius: 50%; }
.error-orbit-inner { width: 285px; height: 285px; border-color: rgba(255, 255, 255, .1); }
.error-art-caption { position: relative; display: flex; align-items: center; gap: 12px; color: #c1b8ac; font-size: 11px; }
.error-art-caption span { width: 6px; height: 6px; flex-shrink: 0; border-radius: 50%; background: #df5f45; }
.error-content { display: flex; flex-direction: column; justify-content: center; padding: 56px clamp(28px, 5vw, 64px); }
.error-kicker { display: flex; align-items: center; gap: 12px; margin: 0 0 24px; color: #a44f3a; font-size: 11px; font-weight: 700; letter-spacing: .17em; text-transform: uppercase; }
.error-kicker span { width: 30px; height: 1px; background: #df5f45; }
.error-content h1 { margin: 0; font-family: 'Prata', serif; font-size: clamp(36px, 4vw, 52px); font-weight: 400; line-height: 1.2; letter-spacing: -.035em; }
.error-description { margin: 24px 0 32px; color: #786f66; font-size: 14px; line-height: 1.85; }
.error-actions { display: flex; flex-wrap: wrap; align-items: center; gap: 20px; }
.error-primary { display: inline-flex; align-items: center; justify-content: space-between; min-height: 54px; padding: 0 24px; gap: 32px; background: #df5f45; color: #fff; font-size: 13px; font-weight: 700; text-decoration: none; }
.error-primary:hover { background: #c54b33; }
.error-secondary { display: inline-flex; align-items: center; gap: 12px; padding: 10px 0; border: 0; border-bottom: 1px solid #c9bbad; background: transparent; color: #63584e; font: inherit; font-size: 12px; font-weight: 700; text-decoration: none; cursor: pointer; }
.error-secondary:hover { color: #a44f3a; border-color: #a44f3a; }
.error-page a:focus-visible, .error-page button:focus-visible { outline: 2px solid #df5f45; outline-offset: 5px; }
.error-note { margin: 32px 0 0; padding-top: 24px; border-top: 1px solid #e7ded3; color: #786f66; font-size: 11px; line-height: 1.7; }
.error-signature { margin: 28px 0 0; color: #786f66; font-size: 11px; line-height: 1.7; text-align: center; }
.error-footer { display: flex; justify-content: space-between; gap: 24px; padding: 28px max(24px, calc((100% - 1180px) / 2)); border-top: 1px solid #e3d9ce; color: #786f66; font-size: 11px; line-height: 1.7; }
@media (max-width: 760px) {
    .error-main { padding-top: 32px; }
    .error-card { grid-template-columns: 1fr; }
    .error-art { min-height: 300px; padding: 24px; }
    .error-number { font-size: 130px; }
    .error-orbit { width: 260px; height: 260px; }
    .error-orbit-inner { width: 200px; height: 200px; }
    .error-content { padding: 36px 28px; }
    .error-footer { flex-direction: column; gap: 8px; }
}
@media (max-width: 400px) {
    .error-header { padding-inline: 16px; gap: 12px; }
    .error-brand { font-size: 15px; gap: 8px; }
    .error-menu-link { gap: 8px; }
    .error-main { width: calc(100% - 32px); }
    .error-content { padding-inline: 22px; }
}
</style>
