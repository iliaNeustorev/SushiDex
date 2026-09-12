<template>
    <AdminLayout>
        <AdminWrapper>
            <VCard class="mb-4">
                <VCardTitle tag="h1">Редактирование поста {{ post.url }}</VCardTitle>
                <VDivider class="mb-2"/>
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
            <ShowImages :images="images"/>
            <ImagesUploader item="post" :id="post.id"/>
        </AdminWrapper>
    </AdminLayout>
</template>

<script setup lang="ts">
import type {
    CategoryCrudResource,
    PostCrudResource,
    TagCrudResource,
    ImageCrudResource,
} from "~types/generated";
import AdminLayout from "~vue/Layouts/AdminLayout.vue";
import {useForm} from "@inertiajs/vue3";
import PostsRoutes from "~routes/Admin/PostController.ts";
import ImagesUploader from '~vue/components/widgets/ImagesUploader.vue';
import AdminWrapper from "~vue/Layouts/AdminWrapper.vue";
import type {PostForm} from "~vue/shared/forms.ts";
import ShowImages from "~vue/components/widgets/ShowImages.vue";

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
    form.submit(PostsRoutes.update(props.post));
}
</script>
