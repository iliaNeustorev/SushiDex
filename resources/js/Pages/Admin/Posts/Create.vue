<template>
    <AdminLayout>
        <AdminWrapper>
            <VCard :loading="form.processing" class="mt-3">
                <VCardTitle tag="h1">Create post</VCardTitle>
                <VDivider/>
                <form @submit.prevent="send" class="mb-3 ml-2">
                    <VTextField
                        v-model="form.url"
                        :counter="10"
                        :error-messages="form.errors.url"
                        label="Url"
                    ></VTextField>

                    <VTextField
                        v-model="form.title"
                        :counter="7"
                        :error-messages="form.errors.title"
                        label="Title"
                    ></VTextField>

                    <VTextarea
                        v-model="form.content"
                        clearable
                        :error-messages="form.errors.content"
                        label="Content">
                    </VTextarea>

                    <VSelect
                        v-model="form.category_id"
                        :error-messages="form.errors.category_id"
                        :items="categories"
                        item-title="title"
                        item-value="id"
                        label="Category"
                    ></VSelect>

                    <VSelect
                        v-model="form.tags"
                        :error-messages="form.errors.tags"
                        :items="tags"
                        item-title="title"
                        item-value="id"
                        multiple
                        label="Tags"
                    ></VSelect>
                    <VBtn
                        class="me-4"
                        type="submit"
                    >
                        Create
                    </VBtn>

                    <VBtn @click="resetForm">
                        clear
                    </VBtn>
                </form>
            </VCard>
        </AdminWrapper>
    </AdminLayout>
</template>

<script setup lang="ts">
import {useForm} from '@inertiajs/vue3'
import AdminLayout from '~vue/Layouts/AdminLayout.vue';
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
