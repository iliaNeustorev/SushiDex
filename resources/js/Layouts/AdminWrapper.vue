<template>
    <VCard class="mt-3">
        <VCardText class="d-flex align-center">
            <Link
                v-for="(item, index) in mainMenu"
                :key="item.url"
                :href="item.url"
                class="text-decoration-none text-sm-h6 pe-2 me-2"
                :class="[
                    isMenuItemActive(item.url) ? 'text-yellow-darken-3 font-weight-bold' : 'text-green-darken-3',
                    { 'border-e': index < mainMenu.length - 1 },
                ]"
            >
                {{ item.title }}
            </Link>
            <VSpacer />
            <VDialog
                v-model="closeModalLogout"
                max-width="600"
                persistent
            >
                <template v-slot:activator="{ props: closeModalLogout }">
                    <VBtn class="text-h6" v-bind="closeModalLogout">
                        Выйти из аккаунта
                    </VBtn>
                </template>

                <VCard
                    prepend-icon="mdi-map-marker"
                    title="Вы действительно хотите выйти?"
                    class="text-center"
                >
                    <template v-slot:actions>
                        <VSpacer></VSpacer>
                        <VBtn @click="closeModalLogout = false">
                            Отмена
                        </VBtn>

                        <VBtn @click="logout">
                            ОК
                        </VBtn>
                    </template>
                </VCard>
            </VDialog>
        </VCardText>
    </VCard>

    <slot></slot>
</template>

<script setup lang="ts">
import {Link, useForm, usePage} from '@inertiajs/vue3';
import PostsRoutes from '~gen/wayfinder/actions/App/Http/Controllers/Admin/PostController';
import Dashboard from '~gen/wayfinder/actions/App/Http/Controllers/Admin/DashboardController';
import ProductRoutes from '~routes/Admin/ProductController';
import CategoriesRoutes from '~routes/Admin/CategoryController';
import TagsRoutes from '~routes/Admin/TagController';
import UsersRoutes from "~routes/Admin/UserController.ts";
import {computed, ref} from "vue";
import SessionRoutes from "~routes/Auth/SessionController.ts";
import OrderRoutes from "~routes/Admin/OrderController.ts";
import {useRoles} from "~vue/composables/useRoles.ts";

const {isDev, isModerator} = useRoles()

const mainMenuItems = [
    {url: Dashboard.index().url, title: 'Админ панель', guard: 'admin'},
    {url: PostsRoutes.index().url, title: 'Посты', guard: 'admin'},
    {url: ProductRoutes.index().url, title: 'Продукты', guard: 'admin'},
    {url: CategoriesRoutes.index().url, title: 'Категории', guard: 'dev'},
    {url: TagsRoutes.index().url, title: 'Тэги', guard: 'dev'},
    {url: UsersRoutes.index().url, title: 'Пользователи', icon: '', guard: 'dev'},
    {url: OrderRoutes.actual().url, title: 'Монитор заказов', icon: '', guard: 'admin'},
] as const

const mainMenu = computed(() => mainMenuItems.filter(item =>
    (item.guard === 'admin' && isModerator.value) || (item.guard === 'dev' && isDev.value)
))
const page = usePage();
let closeModalLogout = ref<boolean>(false);

const logoutForm = useForm({});

function logout() {
    logoutForm.submit(SessionRoutes.logout(), {
        onFinish() {
            closeModalLogout.value = false
        }
    })
}

function isMenuItemActive(url: string): boolean {
    const currentPath = page.url.split('?')[0];

    if (url === OrderRoutes.actual().url) {
        return [
            OrderRoutes.actual().url,
            OrderRoutes.index().url,
        ].includes(currentPath);
    }

    return currentPath.startsWith(url);
}
</script>
