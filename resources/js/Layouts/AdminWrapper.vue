<template>
    <VCard class="mt-3">
        <VCardText class="d-flex align-center">
            <Link
                v-for="(item, index) in mainMenuItems"
                :key="item.url"
                :href="item.url"
                class="text-decoration-none text-sm-h6 pe-2 me-2"
                :class="[
                    page.url.startsWith(item.url) ? 'text-yellow-darken-3 font-weight-bold' : 'text-green-darken-3',
                    { 'border-e': index < mainMenuItems.length - 1 },
                ]"
            >
                {{ item.title }}
            </Link>
            <VSpacer/>
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
import {ref} from "vue";
import SessionRoutes from "~routes/Auth/SessionController.ts";

const mainMenuItems = [
    {url: Dashboard.index().url, title: 'Админ панель'},
    {url: PostsRoutes.index().url, title: 'Посты'},
    {url: ProductRoutes.index().url, title: 'Продукты'},
    {url: CategoriesRoutes.index().url, title: 'Категории'},
    {url: TagsRoutes.index().url, title: 'Тэги'},
    {url: UsersRoutes.index().url, title: 'Пользователи', icon: '', guard: 'admin'}
] as const

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
</script>
