<template>
    <AdminLayout>
        <AdminWrapper>
            <VCard class="mb-4">
                <VCardTitle>Редактирование поста {{ post.id }}</VCardTitle>
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
                        label="Title"
                        class="mb-1"
                    />
                    <VTextarea
                        v-model.trim.lazy="form.content"
                        :error-messages="form.errors.content"
                        variant="outlined"
                        label="Content"
                        class="mb-1"
                    />
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
                </VCardText>
                <VCardActions>
                    <VBtn @click="sendEdit" :disabled="form.processing" color="primary">Сохранить</VBtn>
                </VCardActions>
            </VCard>
            <VCard class="mb-4">
                <VRow>
                    <VCol v-for="img in images" cols="2">
                        <img :src="'/storage/' + img.path" alt="" class="w-100">
                        <VBtn @click="removeImage(img.id)" color="error">X</VBtn>
                    </VCol>
                </VRow>
            </VCard>
            <ImagesUploader item="post" :id="post.id"/>
        </AdminWrapper>
    </AdminLayout>
</template>

<script setup lang="ts">
import type {CategoryCrudResource, PostCrudResource, TagCrudResource, ImageCrudResource} from "~types/generated";
import AdminLayout from "~vue/Layouts/AdminLayout.vue";
import {useForm, router} from "@inertiajs/vue3";
import PostsRoutes from "~routes/Admin/PostController.ts";
import ImagesUploader from '~vue/components/widgets/ImagesUploader.vue';
import AdminImages from "~routes/Admin/ImagesController.ts";
import AdminWrapper from "~vue/Layouts/AdminWrapper.vue";

const props = defineProps<{
    categories: CategoryCrudResource[],
    tags: TagCrudResource[],
    post: PostCrudResource,
    images: ImageCrudResource[]
}>();

const form = useForm({
    url: props.post.url,
    title: props.post.title,
    content: props.post.content,
    category_id: props.post.category?.id ?? null,
    tags: props.post.tags?.map(tag => tag.id) ?? []
})

function sendEdit() {
    form.submit(PostsRoutes.update(props.post));
}

function removeImage(image: number) {
    router.visit(AdminImages.destroy({image}), {
        only: ['images']/* ,
		preserveScroll: true */
    })
}
</script>
