<script setup lang="ts">
import {Link, router} from '@inertiajs/vue3';
import DefaultLayout from '~vue/Layouts/DefaultLayout.vue';
import AdminWrapper from '~vue/Layouts/AdminWrapper.vue';
import ProductRoutes from '~routes/Admin/ProductController';
import type {ProductCrudResource} from '~types/generated';

defineProps<{ products: ProductCrudResource[] }>();

function remove(product: ProductCrudResource) {
    if (confirm(`Удалить товар «${product.title}»?`)) {
        router.delete(ProductRoutes.destroy(product).url);
    }
}
</script>

<template>
    <DefaultLayout>
        <AdminWrapper>
            <VCard class="mt-3">
                <VCardTitle class="d-flex justify-space-between">
                    <span>Товары</span>
                    <Link :href="ProductRoutes.create().url">Создать товар</Link>
                </VCardTitle>
                <VTable>
                    <thead><tr><th>Название</th><th>Категория</th><th>Цена</th><th></th></tr></thead>
                    <tbody>
                        <tr v-for="product in products" :key="product.id">
                            <td>{{ product.title }}</td>
                            <td>{{ product.category.title }}</td>
                            <td>{{ product.price }}</td>
                            <td class="text-right">
                                <Link :href="ProductRoutes.edit(product).url">Изменить</Link>
                                <VBtn class="ml-3" color="error" size="small" @click="remove(product)">Удалить</VBtn>
                            </td>
                        </tr>
                    </tbody>
                </VTable>
            </VCard>
        </AdminWrapper>
    </DefaultLayout>
</template>
