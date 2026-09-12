<template>
    <AdminLayout>
        <AdminWrapper>
            <VCard class="mb-4" :loading="form.processing">
                <VCardTitle tag="h1">Редактировать товар</VCardTitle>
                <VDivider class="mb-2"/>
                <VCardText>
                    <VTextField
                        v-model="form.title"
                        :error-messages="form.errors.title"
                        variant="outlined"
                        label="Название"
                        class="mb-1"
                    />
                    <VSelect
                        v-model="form.category_id"
                        :error-messages="form.errors.category_id"
                        :items="categories"
                        item-title="title"
                        item-value="id"
                        variant="outlined"
                        label="Категория"
                        class="mb-1"
                    />
                    <VTextarea
                        v-model="form.description"
                        :error-messages="form.errors.description"
                        variant="outlined"
                        label="Краткое описание"
                        class="mb-1"
                    />
                    <VTextarea
                        v-model="form.content"
                        :error-messages="form.errors.content"
                        variant="outlined"
                        label="Описание"
                        class="mb-1"
                    />
                    <VTextField
                        v-model="form.price"
                        :error-messages="form.errors.price"
                        type="number"
                        step="0.01"
                        variant="outlined"
                        label="Цена"
                        class="mb-1"
                    />
                    <VTextField
                        v-model="form.old_price"
                        :error-messages="form.errors.old_price"
                        type="number"
                        step="0.01"
                        variant="outlined"
                        label="Старая цена"
                        class="mb-1"
                    />
                </VCardText>
                <VCardActions>
                    <VBtn @click="sendEdit" :disabled="form.processing" color="primary">Сохранить</VBtn>
                </VCardActions>
            </VCard>
            <ShowImages :images="images"/>
            <ImagesUploader item="product" :id="product.id"/>
        </AdminWrapper>
    </AdminLayout>
</template>

<script setup lang="ts">
import {useForm} from '@inertiajs/vue3';
import AdminLayout from '~vue/Layouts/AdminLayout.vue';
import AdminWrapper from '~vue/Layouts/AdminWrapper.vue';
import ImagesUploader from '~vue/components/widgets/ImagesUploader.vue';
import ProductRoutes from '~routes/Admin/ProductController';
import type {CategoryCrudResource, ImageCrudResource, ProductCrudResource, ProductsSaveReqDTO} from '~types/generated';
import ShowImages from "~vue/components/widgets/ShowImages.vue";

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
    active: props.product.active
});

function sendEdit() {
    form.submit(ProductRoutes.update(props.product));
}
</script>
