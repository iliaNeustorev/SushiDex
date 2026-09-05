<template>
    <AdminLayout>
        <AdminWrapper>
            <VCard :loading="form.processing" class="mt-3">
                <VCardTitle tag="h1">Создать пост</VCardTitle>
                <VDivider class="mb-2"/>
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
                        label="Имя"
                    ></VTextField>

                    <VTextarea
                        v-model="form.content"
                        clearable
                        :error-messages="form.errors.content"
                        label="Контент">
                    </VTextarea>

                    <VSelect
                        v-model="form.category_id"
                        :error-messages="form.errors.category_id"
                        :items="categories"
                        item-title="title"
                        item-value="id"
                        label="Категория"
                    ></VSelect>

                    <VSelect
                        v-model="form.tags"
                        :error-messages="form.errors.tags"
                        :items="tags"
                        item-title="title"
                        item-value="id"
                        multiple
                        label="Теги"
                    ></VSelect>
                    <VBtn
                        class="me-4"
                        type="submit"
                    >
                        Создать
                    </VBtn>

                    <VBtn @click="resetForm">
                        Очистить
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
import type {CategoryCrudResource, TagCrudResource} from "~types/generated";
import type {PostForm} from "~vue/shared/forms.ts";
import AdminWrapper from "~vue/Layouts/AdminWrapper.vue";

const {categories} = defineProps<{
    categories: CategoryCrudResource[],
    tags: TagCrudResource[]
}>();

const form = useForm<PostForm>({
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
