<template>
    <AdminLayout>
        <AdminWrapper>
            <VCard class="mt-3" :loading="form.processing">
                <VCardTitle tag="h1">Редактировать товар</VCardTitle>
                <VDivider class="mb-2"/>
                <VCardText>
                    <form @submit.prevent="form.submit(ProductRoutes.update(product))">
                        <VTextField v-model="form.title" label="Название" :error-messages="form.errors.title"/>
                        <VSelect v-model="form.category_id" :items="categories" item-title="title" item-value="id"
                                 label="Категория" :error-messages="form.errors.category_id"/>
                        <VTextarea v-model="form.description" label="Краткое описание"
                                   :error-messages="form.errors.description"/>
                        <VTextarea v-model="form.content" label="Описание" :error-messages="form.errors.content"/>
                        <VTextField v-model="form.price" type="number" step="0.01" label="Цена"
                                    :error-messages="form.errors.price"/>
                        <VTextField v-model="form.old_price" type="number" step="0.01" label="Старая цена"
                                    :error-messages="form.errors.old_price"/>
                        <VBtn type="submit" color="primary" :disabled="form.processing">Сохранить</VBtn>
                    </form>
                </VCardText>
            </VCard>
            <VCard class="my-4">
                <VCardText>
                    <VRow>
                        <VCol v-for="image in images" :key="image.id" cols="2">
                            <img :src="`/storage/${image.path}`" class="w-100" alt="">
                            <VBtn color="error" @click="removeImage(image.id)">Удалить</VBtn>
                        </VCol>
                    </VRow>
                </VCardText>
            </VCard>
            <ImagesUploader item="product" :id="product.id"/>
        </AdminWrapper>
    </AdminLayout>
</template>

<script setup lang="ts">
import {router, useForm} from '@inertiajs/vue3';
import AdminLayout from '~vue/Layouts/AdminLayout.vue';
import AdminWrapper from '~vue/Layouts/AdminWrapper.vue';
import ImagesUploader from '~vue/components/widgets/ImagesUploader.vue';
import ProductRoutes from '~routes/Admin/ProductController';
import ImageRoutes from '~routes/Admin/ImagesController';
import type {CategoryCrudResource, ImageCrudResource, ProductCrudResource, ProductsSaveReqDTO} from '~types/generated';

const props = defineProps<{
    product: ProductCrudResource,
    categories: CategoryCrudResource[],
    images: ImageCrudResource[]
}>();
const form = useForm<ProductsSaveReqDTO>({
    title: props.product.title,
    description: props.product.description,
    content: props.product.content,
    price: props.product.price,
    old_price: props.product.old_price,
    category_id: props.product.category.id,
});

function removeImage(image: number) {
    router.delete(ImageRoutes.destroy({image}).url, {only: ['images'], preserveScroll: true});
}
</script>
