<script setup lang="ts">
import {Link, router} from '@inertiajs/vue3';
import type {PostPublicResource} from '~gen/types/generated';
import Posts from '~routes/PostController';
import DefaultLayout from '~vue/Layouts/DefaultLayout.vue';

const {posts, page, lastPage} = defineProps<{
    posts: PostPublicResource[],
    page: number,
    lastPage: number
}>()

function nextPage() {
    if (page < lastPage) {
        router.visit(
            Posts.index({query: {page: page + 1}}),
            {only: ['posts', 'page']}
        )
    }
}
</script>

<template>
    <DefaultLayout>
        <h1>Blog</h1>
        <VRow class="mb-3">
            <VCol v-for="post in posts" cols="12" md="4">
                <VCard>
                    <VCardTitle>{{ post.title }}</VCardTitle>
                    <VCardSubtitle>
                        <strong>{{ (new Date(post.created_at)).toLocaleString() }}</strong>
                        <span>{{ post.user?.first_name }}</span>
                    </VCardSubtitle>
                    <VDivider/>
                    <VCardText>
                        <Link :href="Posts.show(post.id).url">Read more</Link>
                    </VCardText>
                </VCard>
            </VCol>
        </VRow>
        <VBtn v-if="page < lastPage" @click="nextPage" color="primary">Next page</VBtn>
    </DefaultLayout>
</template>
