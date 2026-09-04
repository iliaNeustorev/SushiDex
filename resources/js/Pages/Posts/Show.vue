<script setup lang="ts">
import {Link} from '@inertiajs/vue3';
import type {PostPublicResource} from '~types/generated';
import Posts from '~routes/PostController';
import MainLayout from "~vue/Layouts/MainLayout.vue";

defineProps<{
    post: PostPublicResource
}>()
</script>

<template>
    <MainLayout>
        <h1>{{ post.title }}</h1>
        <Link :href="Posts.index().url">Back</Link>
    </MainLayout>
</template>
