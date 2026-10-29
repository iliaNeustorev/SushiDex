<template>
    <MainLayout>
        <VContainer>
            <VBtn color="green-darken-1 mb-2" size="large">
                <Link :href="PostsRoutes.index().url" class="text-decoration-none text-white">
                    Мои посты
                </Link>
            </VBtn>
            <VCard class="mb-4">
                <VCardTitle tag="h1">Редактирование поста {{ post.url }}</VCardTitle>
                <VDivider class="mb-2" />
                <VCardText>
                    <VTextField
                        v-model.trim.lazy="form.url"
                        :error-messages="form.errors.url"
                        variant="outlined"
                        label="Url"
                        class="mb-1"
                    />
                    <VTextField
                        v-model.trim.lazy="form.title"
                        :error-messages="form.errors.title"
                        variant="outlined"
                        label="Название"
                        class="mb-1"
                    />
                    <VTextarea
                        v-model.trim.lazy="form.content"
                        :error-messages="form.errors.content"
                        variant="outlined"
                        label="Контент"
                        class="mb-1"
                    />
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
                </VCardText>
                <VCardActions>
                    <VBtn @click="sendEdit" :disabled="form.processing" color="primary">Сохранить</VBtn>
                </VCardActions>
            </VCard>
        </VContainer>
    </MainLayout>
</template>

<script setup lang="ts">

import MainLayout from "~vue/Layouts/MainLayout.vue";
import type {CategoryCrudResource, ImageCrudResource, PostCrudResource, TagCrudResource} from "~types/generated.ts";
import {Link, useForm} from "@inertiajs/vue3";
import type {PostForm} from "~vue/shared/forms.ts";
import PostsRoutes from "~routes/Client/PostController.ts";

const props = defineProps<{
    categories: CategoryCrudResource[],
    tags: TagCrudResource[],
    post: PostCrudResource,
    images: ImageCrudResource[]
}>();
const form = useForm<PostForm>({
    url: props.post.url,
    title: props.post.title,
    content: props.post.content,
    category_id: props.post.category?.id ?? null,
    tags: props.post.tags?.map(tag => tag.id) ?? []
})

function sendEdit() {
    form.submit(PostsRoutes.update(props.post.id));
}
</script>
