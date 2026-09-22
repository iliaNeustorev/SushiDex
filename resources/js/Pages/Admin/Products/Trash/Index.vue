<template>
    <AdminLayout>
        <AdminWrapper>
            <VCard class="mt-3">
                <VCardTitle class="d-flex justify-space-between">
                    <span>Удаленные посты</span>
                    <Link :href="ProductRoutes.index().url" class="text-decoration-none text-green-darken-3">
                        Назад
                    </Link>
                </VCardTitle>
                <VDivider/>
                <VCardText>
                    <VRow>
                        <VCol cols="12" md="4">
                            <VTextField :model-value="queryLocal.filter.title" @update:model-value="onTitleUpd"
                                        label="Название" variant="outlined" clearable/>
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
                        :itemsLength="products.total"
                        :items-per-page="queryLocal.batch ?? 10"
                        :page="queryLocal.page ?? 1"
                        :items-per-page-options="[
							{ title: '10', value: 10 },
							{ title: '20', value: 20 },
							{ title: '50', value: 50 }
						]"
                        :headers="[
							{ key: 'id', title: 'Id' },
							{ key: 'title', title: 'Название' },
							{ key: 'created_at', title: 'Дата создания' },
	                        { key:'price', title: 'Цена', sortable: false },
							{ key: 'category', title: 'Категория', sortable: false},
							{ key: 'actions', title: 'Действия', sortable: false, align: 'center'}
						]"
                        :sort-by="sortAdapter.sortBy.value"
                        @update:page="queryLocal.page = $event"
                        @update:items-per-page="queryLocal.batch = $event"
                        @update:sort-by="sortAdapter.onSort"
                    >
                        <template #item.created_at="{ item }">
                            {{ (new Date(item.created_at)).toLocaleString() }}
                        </template>
                        <template #item.category="{item}">
                            {{ item.category.title }}
                        </template>
                        <template #item.actions="{ item }">
                            <VContainer>
                                <VRow class="align-center justify-center">
                                    <VCol cols="auto">
                                        <VBtn density="compact" color="green-darken-1" @click="confirmRestore(item)">
                                            Вернуть из корзины
                                        </VBtn>
                                    </VCol>
                                    <VCol cols="auto">
                                        <VBtn @click="confirmDelete(item)" density="compact"
                                              color="deep-orange-lighten-1">
                                            <span class="text-white">Удалить навсегда</span>
                                        </VBtn>
                                    </VCol>
                                </VRow>
                            </VContainer>
                        </template>
                    </VDataTableServer>
                </VCardText>
            </VCard>
            <VDialog :model-value="!!productForDelete" max-width="420">
                <VCard v-if="productForDelete">
                    <VCardTitle>Удалить пост?</VCardTitle>
                    <VCardText>«{{ productForDelete.title }}»</VCardText>
                    <VCardActions>
                        <VBtn :disabled="deleteForm.processing" @click="cancelDelete">Отмена</VBtn>
                        <VBtn :loading="deleteForm.processing" color="error" @click="confirmedDelete()">
                            Удалить
                        </VBtn>
                    </VCardActions>
                </VCard>
            </VDialog>
            <VDialog :model-value="!!productForRestore" max-width="420">
                <VCard v-if="productForRestore">
                    <VCardTitle>Восстановить продукт?</VCardTitle>
                    <VCardText>«{{ productForRestore.title }}»</VCardText>
                    <VCardActions>
                        <VBtn :disabled="restoreForm.processing" @click="cancelRestore">Отмена</VBtn>
                        <VBtn :loading="restoreForm.processing" color="error" @click="confirmedRestore()">
                            ОК
                        </VBtn>
                    </VCardActions>
                </VCard>
            </VDialog>
        </AdminWrapper>
    </AdminLayout>
</template>

<script setup lang="ts">
import {reactive, watch} from 'vue';
import {Link, router} from '@inertiajs/vue3';
import {debounce, merge} from 'lodash-es';
import AdminLayout from '~vue/Layouts/AdminLayout.vue';
import AdminWrapper from '~vue/Layouts/AdminWrapper.vue';
import ProductRoutes from '~routes/Admin/ProductController';
import type {
    ProductsTrashQuery,
    ProductTrashResource
} from '~types/generated';
import type {TypedPagination} from '~vue/shared/pagination';
import type {RequiredKeys} from '~vue/shared/objects';
import useSpatieDateRangeAdapter from '~vue/composables/useSpatieDateRangeAdapter';
import useSpatieSortAdapter from '~vue/composables/useSpatieSortAdapter';
import ProductTrashRoutes from '~routes/Admin/Trash/ProductTrashController.ts';
import useTrashActions from '~vue/composables/useTrashActions';

const {query = {}} = defineProps<{
    products: TypedPagination<ProductTrashResource>,
    query: ProductsTrashQuery,
}>();

const queryDefaults: RequiredKeys<ProductsTrashQuery, 'filter'> = {
    filter: {}
}
const queryLocal = reactive(merge({}, queryDefaults, query));
const onTitleUpd = debounce((v: string | null) => queryLocal.filter.title = v ? v : undefined, 900);

watch(queryLocal, applyReload);

function applyReload() {
    router.visit(ProductTrashRoutes.index({
        query: queryLocal
    }));
}

const sortAdapter = useSpatieSortAdapter(() => queryLocal.sort, sort => queryLocal.sort = sort);
const dateRangeAdapter = useSpatieDateRangeAdapter(
    [() => queryLocal.filter.date_from, () => queryLocal.filter.date_to,],
    ([date_from, date_to]) => {
        queryLocal.filter.date_from = date_from;
        queryLocal.filter.date_to = date_to
    }
)
const {
    itemForRestore: productForRestore,
    itemForDelete: productForDelete,
    restoreForm,
    deleteForm,
    confirmRestore,
    confirmDelete,
    cancelRestore,
    cancelDelete,
    restore: confirmedRestore,
    forceDelete: confirmedDelete,
} = useTrashActions<ProductTrashResource>({
    restoreRoute: id => ProductTrashRoutes.update(id),
    forceDeleteRoute: id => ProductTrashRoutes.destroy(id),
});
</script>
