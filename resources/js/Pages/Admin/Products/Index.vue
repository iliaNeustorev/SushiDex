<template>
    <AdminLayout>
        <AdminWrapper>
            <VCard class="mt-3">
                <VCardTitle class="d-flex justify-space-between">
                    <span>Товары</span>
                    <Link :href="ProductRoutes.create().url" class="text-decoration-none text-green-darken-3">Создать
                        товар
                    </Link>
                </VCardTitle>
                <VDivider/>
                <VCardText>
                    <VRow>
                        <VCol cols="12" md="4">
                            <VTextField :model-value="queryLocal.filter.title" @update:model-value="onTitleUpdate"
                                        label="Название" variant="outlined" clearable/>
                        </VCol>
                        <VCol cols="12" md="4">
                            <VSelect v-model="queryLocal.filter.category_id" :items="categories" item-title="title"
                                     item-value="id" label="Категория" variant="outlined" clearable/>
                        </VCol>
                        <VCol cols="12" md="4">
                            <VDateInput v-model="dateRangeAdapter.inputModel.value" :hide-actions="false"
                                        @update:menu="dateRangeAdapter.onUpdateMenu"
                                        @click:clear="dateRangeAdapter.onClear" label="Дата создания" variant="outlined"
                                        multiple="range" clearable/>
                        </VCol>
                    </VRow>
                </VCardText>
            </VCard>
            <VCard class="mt-3">
                <VCardText>
                    <VDataTableServer
                        :items="products.data"
                        :items-length="products.total"
                        :items-per-page="queryLocal.batch ?? 10"
                        :page="queryLocal.page ?? 1"
                        :items-per-page-options="[
                            {title: '10', value: 10},
                            {title: '20', value: 20},
                            {title: '50', value: 50}
                        ]"
                        :headers="[
                            {key: 'id', title: 'ID'},
                            {key: 'title', title: 'Название'},
                            {key: 'category.title', title: 'Категория', sortable: false},
                            {key: 'price', title: 'Цена'},
                            {key: 'created_at', title: 'Дата создания'},
                            {key: 'actions', title: 'Действия', sortable: false, align: 'center'}
                        ]"
                        :sort-by="sortAdapter.sortBy.value"
                        @update:page="queryLocal.page = $event"
                        @update:items-per-page="queryLocal.batch = $event"
                        @update:sort-by="sortAdapter.onSort"
                    >
                        <template #item.price="{ item }">{{ item.price }} ₽</template>
                        <template #item.created_at="{ item }">{{
                                new Date(item.created_at).toLocaleString()
                            }}
                        </template>
                        <template #item.actions="{ item }">
                            <VContainer>
                                <VRow class="align-center justify-center">
                                    <VCol cols="auto">
                                        <VBtn density="compact" color="green-darken-1">
                                            <Link :href="ProductRoutes.edit(item).url" class="text-decoration-none">
                                                <span class="text-white">Редактировать</span>
                                            </Link>
                                        </VBtn>
                                    </VCol>
                                    <VCol cols="auto">
                                        <VBtn @click="productForRemove = item" density="compact"
                                              color="deep-orange-lighten-1">
                                            <span class="text-white">Удалить</span>
                                        </VBtn>
                                    </VCol>
                                </VRow>
                            </VContainer>
                        </template>
                    </VDataTableServer>
                </VCardText>
            </VCard>
            <VDialog
                :model-value="!!productForRemove"
                max-width="420"
            >
                <VCard v-if="productForRemove">
                    <VCardTitle>Удалить товар?</VCardTitle>
                    <VCardText>«{{ productForRemove.title }}»</VCardText>
                    <VCardActions>
                        <VBtn :disabled="deleteForm.processing" @click="productForRemove = null">Отмена</VBtn>
                        <VBtn :loading="deleteForm.processing" color="error" @click="removeConfirmed">Удалить</VBtn>
                    </VCardActions>
                </VCard>
            </VDialog>
        </AdminWrapper>
    </AdminLayout>
</template>

<script setup lang="ts">
import {reactive, ref, watch} from 'vue';
import {Link, router, useForm} from '@inertiajs/vue3';
import {debounce, merge} from 'lodash';
import AdminLayout from '~vue/Layouts/AdminLayout.vue';
import AdminWrapper from '~vue/Layouts/AdminWrapper.vue';
import ProductRoutes from '~routes/Admin/ProductController';
import type {CategoryCrudResource, ProductCrudResource, ProductsQuery} from '~types/generated';
import type {TypedPagination} from '~vue/shared/pagination';
import type {RequiredKeys} from '~vue/shared/objects';
import useSpatieDateRangeAdapter from '~vue/composables/useSpatieDateRangeAdapter';
import useSpatieSortAdapter from '~vue/composables/useSpatieSortAdapter';

const {query = {}} = defineProps<{
    products: TypedPagination<ProductCrudResource>,
    categories: CategoryCrudResource[],
    query: ProductsQuery
}>();
const queryDefaults: RequiredKeys<ProductsQuery, 'filter'> = {filter: {}};
const queryLocal = reactive(merge({}, queryDefaults, query));
const onTitleUpdate = debounce((value: string | null) => queryLocal.filter.title = value || undefined, 400);
const sortAdapter = useSpatieSortAdapter(() => queryLocal.sort, sort => queryLocal.sort = sort);
const dateRangeAdapter = useSpatieDateRangeAdapter([() => queryLocal.filter.date_from, () => queryLocal.filter.date_to], ([dateFrom, dateTo]) => {
    queryLocal.filter.date_from = dateFrom;
    queryLocal.filter.date_to = dateTo;
});
watch(queryLocal, () => router.visit(ProductRoutes.index({query: queryLocal})));

const productForRemove = ref<ProductCrudResource | null>(null);
const deleteForm = useForm({});

function removeConfirmed() {
    if (productForRemove.value) {
        deleteForm.submit(ProductRoutes.destroy(productForRemove.value), {
            onFinish() {
                productForRemove.value = null;
            }
        });
    }
}

</script>
