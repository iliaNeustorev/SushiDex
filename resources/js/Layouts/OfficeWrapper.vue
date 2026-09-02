<script setup lang="ts">
import {Link} from '@inertiajs/vue3';
import PostsRoutes from '~gen/wayfinder/actions/App/Http/Controllers/Admin/PostController';
import CategoriesRoutes from '~gen/wayfinder/actions/App/Http/Controllers/Admin/CategoryController';
import Dashboard from '~gen/wayfinder/actions/App/Http/Controllers/Admin/DashboardController';
import TagRoutes from '~routes/Admin/TagController';
import ProductRoutes from '~routes/Admin/ProductController';
</script>

<template>
    <VCard class="mt-3">
        <VCardText>
            <Link :href="Dashboard.index().url">Админ панель</Link>
            |
            <Link :href="PostsRoutes.index().url">Посты</Link>
            |
            <Link :href="CategoriesRoutes.create().url">Создать категорию</Link>
            |
            <Link :href="TagRoutes.create().url">Создать тег</Link>
            |
            <Link :href="ProductRoutes.index().url">Товары</Link>
        </VCardText>
    </VCard>
    <slot></slot>
</template>
