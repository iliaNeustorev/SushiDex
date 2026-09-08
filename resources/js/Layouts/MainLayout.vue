<template>
    <VApp class="sushi-page">
        <VAppBar class="site-app-bar px-md-4" color="#151514" flat>
            <template #prepend>
                <VAppBarNavIcon
                    v-if="$vuetify.display.smAndDown"
                    aria-label="Открыть меню"
                    @click="drawer = !drawer"
                />
            </template>

            <Link :href="GeneralRoutes.index().url" class="brand light" aria-label="SushiDex — на главную">
                <span class="brand-mark">よ</span><span>SushiDex</span>
            </Link>

            <template v-if="$vuetify.display.mdAndUp">
                <VBtn
                    v-for="item in mainMenu"
                    :key="item.to.url"
                    :href="item.to.url"
                    class="ms-4 text-none"
                    variant="text"
                >
                    {{ item.title }}
                </VBtn>
            </template>
            <VSpacer/>

            <template #append>
                <a class="phone" href="tel:+74951234567">
                    <span>Ежедневно 11:00–23:00</span>+7 495 123-45-67
                </a>
                <VMenu
                    v-if="user"
                    content-class="user-menu"
                    location="bottom end"
                    :offset="10"
                >
                    <template #activator="{ props: menuProps }">
                        <VBtn
                            v-bind="menuProps"
                            class="avatar-button ms-3"
                            icon
                            aria-label="Открыть меню пользователя"
                        >
                            <VAvatar color="#df5f45" size="36">
                                {{ user.first_name.charAt(0).toUpperCase() }}
                            </VAvatar>
                        </VBtn>
                    </template>

                    <VList class="user-menu-list" density="comfortable">
                        <div class="user-menu-header">
                            <VAvatar color="#df5f45" size="42">
                                {{ user.first_name.charAt(0).toUpperCase() }}
                            </VAvatar>
                            <div>
                                <strong>{{ user.first_name }} {{ user.last_name }}</strong>
                                <span>{{ user.email }}</span>
                            </div>
                        </div>
                        <VDivider />
                        <div class="user-menu-actions">
                            <VListItem
                                :href="GeneralRoutes.profile().url"
                                prepend-icon="mdi-account-outline"
                                title="Личный кабинет"
                            />
                            <VListItem
                                prepend-icon="mdi-logout"
                                title="Выйти"
                                :disabled="logoutForm.processing"
                                @click="logout"
                            />
                        </div>
                    </VList>
                </VMenu>

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
                    v-for="item in mainMenu"
                    :key="item.to.url"
                    :href="item.to.url"
                    :title="item.title"
                    @click="drawer = false"
                />
            </VList>
        </VNavigationDrawer>
        <VMain>
            <slot/>
        </VMain>
        <footer class="site-footer">
            <div class="shell footer-inner">
                <div class="brand light"><span class="brand-mark">よ</span><span>SushiDex</span></div>
                <p>Суши и роллы, приготовленные с уважением к продукту.</p>
                <span>© {{ new Date().getFullYear() }} SushiDex</span>
            </div>
        </footer>
    </VApp>
</template>

<script setup lang="ts">
import {Link, useForm, usePage} from '@inertiajs/vue3';
import {computed, shallowRef} from 'vue';
import SessionRoutes from '~routes/Auth/SessionController.ts';
import type {UserAuthResource} from "~types/generated";
import Posts from "~routes/PostController.ts";
import AdminDashboard from "~routes/Admin/DashboardController.ts";
import GeneralRoutes from '~routes/GeneralController';

const {props} = usePage<{ user: UserAuthResource | null }>();
const user = props.user
const drawer = shallowRef(false);
const logoutForm = useForm({});
const mainMenuBase = [
    {to: GeneralRoutes.menu(), title: 'Меню', icon: '$food', guard: null},
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

</script>

<style src="~css/layouts/main-layout.css"></style>
