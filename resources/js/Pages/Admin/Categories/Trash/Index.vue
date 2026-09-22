<template>
    <AdminLayout>
        <AdminWrapper>
            <VCard class="mt-3">
                <VCardTitle class="d-flex justify-space-between">
                    <span>Удаленные категории</span>
                    <Link :href="CategoriesRoutes.index().url" class="text-decoration-none text-green-darken-3">
                        Назад
                    </Link>
                </VCardTitle>
                <VDivider/>
                <VCardText>
                    <VRow>
                        <VCol>
                            <VTextField
                                :model-value="queryLocal.filter.title"
                                @update:model-value="onTitleUpd"
                                label="Имя"
                                variant="outlined"
                                class="mb-2"
                                clearable
                            />
                        </VCol>
                        <VCol>
                            <VDateInput
                                :hide-actions="false"
                                v-model="dateRangeAdapter.inputModel.value"
                                @update:menu="dateRangeAdapter.onUpdateMenu"
                                @click:clear="dateRangeAdapter.onClear"
                                label="Дата"
                                variant="outlined"
                                class="mb-2"
                                multiple="range"
                                clearable
                            />
                        </VCol>
                    </VRow>
                </VCardText>
            </VCard>
            <VCard class="mt-3">
                <VCardText>
                    <VDataTableServer
                        :items="categories.data"
                        :itemsLength="categories.total"
                        :items-per-page="queryLocal.batch ?? 10"
                        :page="queryLocal.page ?? 1"
                        :items-per-page-options="[
							{ title: '10', value: 10 },
							{ title: '20', value: 20 },
							{ title: '50', value: 50 }
						]"
                        :headers="[
							{ key: 'id', title: 'Id' },
							{ key: 'url', title: 'Url' },
							{ key: 'title', title: 'Название' },
							{ key: 'type', title: 'Тип' },
							{ key: 'created_at', title: 'Дата создания' },
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
                        <template #item.type="{ item }">{{
                                types.find(type => type.value === item.type)?.title
                            }}
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
            <VDialog :model-value="!!categoryForDelete" max-width="420">
                <VCard v-if="categoryForDelete">
                    <VCardTitle>Удалить категорию?</VCardTitle>
                    <VCardText>«{{ categoryForDelete.title }}»</VCardText>
                    <VCardActions>
                        <VBtn :disabled="deleteForm.processing" @click="cancelDelete">Отмена</VBtn>
                        <VBtn :loading="deleteForm.processing" color="error" @click="confirmedDelete()">
                            Удалить
                        </VBtn>
                    </VCardActions>
                </VCard>
            </VDialog>
            <VDialog :model-value="!!categoryForRestore" max-width="420">
                <VCard v-if="categoryForRestore">
                    <VCardTitle>Восстановить категорию?</VCardTitle>
                    <VCardText>«{{ categoryForRestore.title }}»</VCardText>
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

import CategoriesRoutes from "~routes/Admin/CategoryController.ts";
import {Link, router} from "@inertiajs/vue3";
import AdminLayout from "~vue/Layouts/AdminLayout.vue";
import AdminWrapper from "~vue/Layouts/AdminWrapper.vue";
import type {TypedPagination} from "~vue/shared/pagination.ts";
import type {CategoriesTrashQuery, CategoryCrudResource, Type} from "~types/generated.ts";
import type {RequiredKeys} from "~vue/shared/objects.ts";
import {reactive, watch} from "vue";
import {debounce, merge} from "lodash-es";
import useSpatieSortAdapter from "~vue/composables/useSpatieSortAdapter.ts";
import useSpatieDateRangeAdapter from "~vue/composables/useSpatieDateRangeAdapter.ts";
import CategoryTrashRoutes from "~routes/Admin/Trash/CategoryTrashController.ts";
import useTrashActions from "~vue/composables/useTrashActions.ts";

const {query = {}} = defineProps<{
    categories: TypedPagination<CategoryCrudResource>,
    query: CategoriesTrashQuery,
}>()

const queryDefaults: RequiredKeys<CategoriesTrashQuery, 'filter'> = {
    filter: {}
}
const queryLocal = reactive(merge({}, queryDefaults, query));
const onTitleUpd = debounce((v: string | null) => queryLocal.filter.title = v ? v : undefined, 900);

watch(queryLocal, applyReload);

function applyReload() {
    router.visit(CategoryTrashRoutes.index({
        query: queryLocal
    }));
}

const types: Array<{ title: string, value: Type }> = [
    {value: 1, title: 'Продукт'},
    {value: 2, title: 'Блог'},
]
const sortAdapter = useSpatieSortAdapter(() => queryLocal.sort, sort => queryLocal.sort = sort);
const dateRangeAdapter = useSpatieDateRangeAdapter(
    [() => queryLocal.filter.date_from, () => queryLocal.filter.date_to,],
    ([date_from, date_to]) => {
        queryLocal.filter.date_from = date_from;
        queryLocal.filter.date_to = date_to
    }
)
const {
    itemForRestore: categoryForRestore,
    itemForDelete: categoryForDelete,
    restoreForm,
    deleteForm,
    confirmRestore,
    confirmDelete,
    cancelRestore,
    cancelDelete,
    restore: confirmedRestore,
    forceDelete: confirmedDelete,
} = useTrashActions<CategoryCrudResource>({
    restoreRoute: id => CategoryTrashRoutes.update(id),
    forceDeleteRoute: id => CategoryTrashRoutes.destroy(id),
});
</script>
