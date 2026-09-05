<template>
    <AdminLayout>
        <AdminWrapper>
            <VCard class="mt-3" :loading="form.processing">
                <VCardTitle tag="h1">Создать товар</VCardTitle>
                <VDivider class="mb-2"/>
                <VCardText>
                    <form @submit.prevent="form.submit(ProductRoutes.store())">
                        <VTextField
                            v-model="form.title"
                            label="Название"
                            :error-messages="form.errors.title"
                        />
                        <VSelect
                            v-model="form.category_id"
                            :items="categories"
                            item-title="title"
                            item-value="id"
                            label="Категория"
                            :error-messages="form.errors.category_id"/>
                        <VTextarea
                            v-model="form.description"
                            label="Краткое описание"
                            :error-messages="form.errors.description"/>
                        <VTextarea
                            v-model="form.content"
                            label="Описание"
                            :error-messages="form.errors.content"/>
                        <VTextField
                            v-model="form.price"
                            type="number"
                            step="0.01"
                            label="Цена"
                            :error-messages="form.errors.price"/>
                        <VTextField
                            v-model="form.old_price"
                            type="number"
                            step="0.01"
                            label="Старая цена"
                            :error-messages="form.errors.old_price"/>
                        <VBtn
                            type="submit"
                            color="primary"
                            :disabled="form.processing">Создать
                        </VBtn>
                    </form>
                </VCardText>
            </VCard>
        </AdminWrapper>
    </AdminLayout>
</template>

<script setup lang="ts">
import {useForm} from '@inertiajs/vue3';
import AdminLayout from '~vue/Layouts/AdminLayout.vue';
import AdminWrapper from '~vue/Layouts/AdminWrapper.vue';
import ProductRoutes from '~routes/Admin/ProductController';
import type {CategoryCrudResource} from '~types/generated';
import type {ProductForm} from "~vue/shared/forms.ts";

defineProps<{ categories: CategoryCrudResource[] }>();

const form = useForm<ProductForm>({
    title: '',
    description: null,
    content: null,
    price: '',
    old_price: null,
    category_id: null,
});
</script>
