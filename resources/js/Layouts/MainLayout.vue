<template>
    <Head title="SushiDex — суши и роллы"/>
    <VApp class="sushi-page">
        <VAppBar class="site-app-bar px-md-4" color="#151514" flat>
            <template #prepend>
                <VAppBarNavIcon
                    v-if="$vuetify.display.smAndDown"
                    aria-label="Открыть меню"
                    @click="drawer = !drawer"
                />
            </template>

            <Link href="/Project12/public" class="brand light" aria-label="SushiDex — на главную">
                <span class="brand-mark">よ</span><span>SushiDex</span>
            </Link>

            <!--            <template v-if="$vuetify.display.mdAndUp">-->
            <!--                <VBtn-->
            <!--                    v-for="item in items"-->
            <!--                    :key="item.href"-->
            <!--                    :href="item.href"-->
            <!--                    class="ms-6 text-none"-->
            <!--                    variant="text"-->
            <!--                >{{ item.title }}-->
            <!--                </VBtn>-->

            <!--            </template>-->
            <VListItem v-for="item in mainMenu" :key="item.to.url" color="primary" rounded="xl">
                <VListItemTitle>
                    <Link :href="item.to.url" class="text-decoration-none text-white ms-6">{{
                            item.title
                        }}
                    </Link>
                </VListItemTitle>
            </VListItem>
            <VSpacer/>

            <template #append>
                <a class="phone" href="tel:+74951234567">
                    <span>Ежедневно 11:00–23:00</span>+7 495 123-45-67
                </a>
                <VBtn v-if="user" class="ms-3" icon aria-label="Меню пользователя">
                    <VAvatar color="primary" size="36">
                        {{ user.first_name.charAt(0).toUpperCase() }}
                    </VAvatar>

                    <VMenu activator="parent" origin="top end">
                        <VList>
                            <VListItem
                                :href="DashboardRoutes.index.url()"
                                prepend-icon="mdi-account-outline"
                                title="Личный кабинет"
                            />
                            <VListItem
                                prepend-icon="mdi-logout"
                                title="Выйти"
                                :disabled="logoutForm.processing"
                                @click="logout"
                            />
                        </VList>
                    </VMenu>
                </VBtn>

                <VBtn
                    v-else
                    class="ms-3 text-none"
                    :href="SessionRoutes.create.url()"
                    variant="outlined"
                >
                    Войти
                </VBtn>
            </template>
        </VAppBar>

        <VNavigationDrawer
            v-if="$vuetify.display.smAndDown"
            v-model="drawer"
            location="left"
            temporary
            width="300"
        >
            <VList nav>
                <VListItem
                    v-for="item in items"
                    :key="item.href"
                    :href="item.href"
                    :title="item.title"
                    @click="drawer = false"
                />
            </VList>
        </VNavigationDrawer>
        <VMain class="appMain">
            <div class="ps-4 pe-4">
                <slot/>
            </div>
        </VMain>
        <footer class="site-footer">
            <div class="shell footer-inner">
                <div class="brand light"><span class="brand-mark">よ</span><span>YOKO</span></div>
                <p>Суши и роллы, приготовленные с уважением к продукту.</p>
                <span>© {{ new Date().getFullYear() }} Yoko</span>
            </div>
        </footer>
    </VApp>
</template>

<script setup lang="ts">
import {Head, Link, useForm, usePage} from '@inertiajs/vue3';
import {computed, shallowRef} from 'vue';
import DashboardRoutes from '~routes/Admin/DashboardController.ts';
import SessionRoutes from '~routes/Auth/SessionController.ts';
import type {UserAuthResource} from "~types/generated";
import Posts from "~routes/PostController.ts";
import AdminDashboard from "~routes/Admin/DashboardController.ts";

const {props} = usePage<{ user: UserAuthResource | null }>();
const user = props.user
const drawer = shallowRef(false);
const logoutForm = useForm({});
const mainMenuBase = [
    // {to: General.home(), title: 'Home', icon: '', guard: null},
    {to: Posts.index(), title: 'Посты', icon: '$newspaper', guard: null},
    {to: AdminDashboard.index(), title: 'Администрирование', icon: '', guard: 'admin'}
] as const;

const mainMenu = computed(() => mainMenuBase.filter(item =>
    item.guard === null ||
    (item.guard === 'admin' && props.user)
))

function logout() {
    logoutForm.submit(SessionRoutes.logout());
}

const items = [
    {title: 'Меню', href: '#menu'},
    {title: 'О нас', href: '#about'},
    {title: 'Доставка', href: '#delivery'},
];
</script>

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

.brand.light {
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
