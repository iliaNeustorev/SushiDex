<template>
    <DefaultLayout>
        <AdminWrapper>
            <VCard :loading="form.processing" class="mt-3">
                <VCardTitle tag="h1">Create post</VCardTitle>
                <VDivider/>
                <form @submit.prevent="send" class="mb-3 ml-2">
                    <v-text-field
                        v-model="form.url"
                        :counter="10"
                        :error-messages="form.errors.url"
                        label="Url"
                    ></v-text-field>

                    <v-text-field
                        v-model="form.title"
                        :counter="7"
                        :error-messages="form.errors.title"
                        label="Title"
                    ></v-text-field>

                    <v-textarea
                        v-model="form.content"
                        clearable
                        :error-messages="form.errors.content"
                        label="Content">
                    </v-textarea>

                    <v-select
                        v-model="form.category_id"
                        :error-messages="form.errors.category_id"
                        :items="categories"
                        item-title="title"
                        item-value="id"
                        label="Category"
                    ></v-select>

                    <v-select
                        v-model="form.tags"
                        :error-messages="form.errors.tags"
                        :items="tags"
                        item-title="title"
                        item-value="id"
                        multiple
                        label="Tags"
                    ></v-select>
                    <v-btn
                        class="me-4"
                        type="submit"
                    >
                        Create
                    </v-btn>

                    <v-btn @click="resetForm">
                        clear
                    </v-btn>
                </form>
            </VCard>
        </AdminWrapper>
    </DefaultLayout>
</template>

<script setup lang="ts">
import {useForm} from '@inertiajs/vue3'
import DefaultLayout from '~vue/Layouts/DefaultLayout.vue';
import PostsRoutes from '~routes/Admin/PostController'
import type {CategoryCrudResource, PostsSaveReqDTO, TagCrudResource} from "~types/generated";
import AdminWrapper from "~vue/Layouts/AdminWrapper.vue";

const {categories} = defineProps<{
    categories: CategoryCrudResource[],
    tags: TagCrudResource[]
}>();

const form = useForm<PostsSaveReqDTO>({
    url: '',
    title: '',
    content: '',
    category_id: null,
    tags: [],
})

function send() {
    form.submit(PostsRoutes.store());
}

function resetForm() {
    form.reset();
}
</script>
