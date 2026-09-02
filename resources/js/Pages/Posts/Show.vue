<script setup lang="ts">
import {Link} from '@inertiajs/vue3';
import type {PostPublicResource} from '~types/generated';
import Posts from '~routes/PostController';
import DefaultLayout from '~vue/Layouts/DefaultLayout.vue';

defineProps<{
    post: PostPublicResource
}>()
</script>

<template>
    <DefaultLayout>
        <h1>{{ post.title }}</h1>
        <Link :href="Posts.index().url">Back</Link>
    </DefaultLayout>
</template>
