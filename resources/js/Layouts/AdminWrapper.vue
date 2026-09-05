<template>
    <VCard class="mt-3">
        <VCardText>
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
        </VCardText>
    </VCard>
    <slot></slot>
</template>

<script setup lang="ts">
import {Link, usePage} from '@inertiajs/vue3';
import PostsRoutes from '~gen/wayfinder/actions/App/Http/Controllers/Admin/PostController';
import Dashboard from '~gen/wayfinder/actions/App/Http/Controllers/Admin/DashboardController';
import ProductRoutes from '~routes/Admin/ProductController';
import CategoriesRoutes from '~routes/Admin/CategoryController';

const mainMenuItems = [
    {url: Dashboard.index().url, title: 'Админ панель'},
    {url: PostsRoutes.index().url, title: 'Посты'},
    {url: ProductRoutes.index().url, title: 'Продукты'},
    {url: CategoriesRoutes.index().url, title: 'Категории'}
] as const

const page = usePage();

</script>
