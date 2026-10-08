<template>
    <VApp>
        <VAppBar app height="54">
            <VContainer fluid>
                <VToolbarTitle>
                    <VIcon icon="$castEducation" class="me-2"></VIcon>
                    SushiDex
                </VToolbarTitle>
            </VContainer>
        </VAppBar>
        <VNavigationDrawer permanent width="300">
            <div class="d-flex flex-column fill-height">
                <VList>
                    <VListSubheader>Админ панель</VListSubheader>
                    <VListItem
                        v-for="(item,index) in mainMenu"
                        :key="item.to.url"
                        color="primary"
                        rounded="xl"
                    >
                        <template #prepend>
                            <VIcon :icon="item.icon"/>
                        </template>
                        <VListItemTitle class="text-wrap">
                            <Link
                                :href="item.to.url"
                                class="d-block text-decoration-none text-h6 text-wrap"
                                :class="[isMenuItemActive(item.to.url) && index !== 0 ? 'text-yellow-darken-3 font-weight-bold' : 'text-grey-darken-4']"
                            >
                                {{
                                    item.title
                                }}
                            </Link>
                        </VListItemTitle>
                    </VListItem>
                </VList>
            </div>
        </VNavigationDrawer>
        <VMain class="appMain">
            <div class="ps-4 pe-4">
                <slot/>
            </div>
        </VMain>
        <VFooter app class="flex-grow-0">
            <VContainer fluid class="py-1">
                Админ панель &copy;
            </VContainer>
        </VFooter>
    </VApp>
</template>

<script setup lang="ts">
import {Link, usePage} from '@inertiajs/vue3';
import {computed} from 'vue';
import type {UserAuthResource} from "~types/generated";
import PostsRoutes from "~routes/Admin/PostController.ts";
import GeneralController from "~routes/GeneralController.ts";
import ProductRoutes from "~routes/Admin/ProductController.ts";
import CategoryRoutes from "~routes/Admin/CategoryController.ts";
import TagsRoutes from "~routes/Admin/TagController.ts";
import UsersRoutes from "~routes/Admin/UserController.ts";
import OrderRoutes from "~routes/Admin/OrderController.ts";

const {props} = usePage<{ user: UserAuthResource | null }>();
const page = usePage();
const mainMenuBase = [
    {to: GeneralController.index(), title: 'Главная', icon: '', guard: 'admin'},
    {to: PostsRoutes.index(), title: 'Посты', icon: '', guard: 'admin'},
    {to: ProductRoutes.index(), title: 'Продукты', icon: '', guard: 'admin'},
    {to: CategoryRoutes.index(), title: 'Категории', icon: '', guard: 'admin'},
    {to: TagsRoutes.index(), title: 'Тэги', icon: '', guard: 'admin'},
    {to: UsersRoutes.index(), title: 'Пользователи', icon: '', guard: 'admin'},
    {to: OrderRoutes.actual(), title: 'Монитор заказов', icon: '', guard: 'admin'},
] as const;

const mainMenu = computed(() => mainMenuBase.filter(item =>
    (item.guard === 'admin' && props.user)
));

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
