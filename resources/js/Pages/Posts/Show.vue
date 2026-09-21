<template>
    <MainLayout>
        <VContainer class="py-3 text-center">
            <h1>{{ post.title }}</h1>
            <div class="d-flex justify-center">
                <Link class="text-decoration-none link-color" :href="Posts.index().url">
                    Назад
                </Link>
            </div>
        </VContainer>
    </MainLayout>
</template>

<script setup lang="ts">
import {Link} from '@inertiajs/vue3';
import type {PostPublicResource} from '~types/generated';
import Posts from '~routes/PostController';
import MainLayout from "~vue/Layouts/MainLayout.vue";

defineProps<{
    post: PostPublicResource
}>()
</script>

<style scoped>
.link-color {
    color: #247373
}
</style>
