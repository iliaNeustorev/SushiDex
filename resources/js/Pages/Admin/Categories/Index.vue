<template>
    <AdminLayout>
        <AdminWrapper>
            <VCard class="mt-3">
                <VCardTitle class="d-flex justify-space-between">
                    <span>Категории</span>
                    <Link :href="CategoriesRoutes.create().url" class="text-decoration-none text-green-darken-3">Новая
                        категория
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
                            <VTextField :model-value="queryLocal.filter.url" @update:model-value="onUrlUpdate"
                                        label="URL" variant="outlined" clearable/>
                        </VCol>
                        <VCol cols="12" md="4">
                            <VSelect v-model="queryLocal.filter.type" :items="types" label="Тип" variant="outlined"
                                     clearable/>
                        </VCol>
                        <VCol cols="12">
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
                        :items="categories.data"
                        :items-length="categories.total"
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
                            {key: 'url', title: 'URL', sortable: false},
                            {key: 'type', title: 'Тип'},
                            {key: 'created_at', title: 'Дата создания'},
                            {key: 'actions', title: 'Действия', sortable: false, align: 'center'}
                        ]"
                        :sort-by="sortAdapter.sortBy.value"
                        @update:page="queryLocal.page = $event"
                        @update:items-per-page="queryLocal.batch = $event"
                        @update:sort-by="sortAdapter.onSort">
                        <template #item.type="{ item }">{{
                                types.find(type => type.value === item.type)?.title
                            }}
                        </template>
                        <template #item.created_at="{ item }">{{
                                new Date(item.created_at).toLocaleString()
                            }}
                        </template>
                        <template #item.actions="{ item }">
                            <VContainer>
                                <VRow class="align-center justify-center">
                                    <VCol cols="auto">
                                        <VBtn density="compact" color="green-darken-1">
                                            <Link :href="CategoriesRoutes.edit(item).url" class="text-decoration-none">
                                                <span class="text-white">Редактировать</span>
                                            </Link>
                                        </VBtn>
                                    </VCol>
                                    <VCol cols="auto">
                                        <VBtn @click="categoryForRemove = item" density="compact"
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
                :model-value="!!categoryForRemove"
                max-width="420"
            >
                <VCard v-if="categoryForRemove">
                    <VCardTitle>Удалить категорию?</VCardTitle>
                    <VCardText>«{{ categoryForRemove.title }}»</VCardText>
                    <VCardActions>
                        <VBtn :disabled="deleteForm.processing" @click="categoryForRemove = null">Отмена</VBtn>
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
import CategoriesRoutes from '~routes/Admin/CategoryController';
import type {CategoriesQuery, CategoryCrudResource, Type} from '~types/generated';
import type {TypedPagination} from '~vue/shared/pagination';
import type {RequiredKeys} from '~vue/shared/objects';
import useSpatieDateRangeAdapter from '~vue/composables/useSpatieDateRangeAdapter';
import useSpatieSortAdapter from '~vue/composables/useSpatieSortAdapter';

const {query = {}} = defineProps<{
    categories: TypedPagination<CategoryCrudResource>,
    query: CategoriesQuery,
}>();

const types: Array<{ title: string, value: Type }> = [
    {value: 1, title: 'Продукт'},
    {value: 2, title: 'Блог'},
]

const queryDefaults: RequiredKeys<CategoriesQuery, 'filter'> = {filter: {}};
const queryLocal = reactive(merge({}, queryDefaults, query));
const onTitleUpdate = debounce((value: string | null) => queryLocal.filter.title = value || undefined, 400);
const onUrlUpdate = debounce((value: string | null) => queryLocal.filter.url = value || undefined, 400);
const sortAdapter = useSpatieSortAdapter(() => queryLocal.sort, sort => queryLocal.sort = sort);
const dateRangeAdapter = useSpatieDateRangeAdapter([() => queryLocal.filter.date_from, () => queryLocal.filter.date_to], ([dateFrom, dateTo]) => {
    queryLocal.filter.date_from = dateFrom;
    queryLocal.filter.date_to = dateTo;
});
watch(queryLocal, () => router.visit(CategoriesRoutes.index({query: queryLocal})));

const categoryForRemove = ref<CategoryCrudResource | null>(null);
const deleteForm = useForm({});

function removeConfirmed() {
    if (categoryForRemove.value) {
        deleteForm.submit(CategoriesRoutes.destroy(categoryForRemove.value), {
            onFinish() {
                categoryForRemove.value = null;
            }
        });
    }
}

</script>
