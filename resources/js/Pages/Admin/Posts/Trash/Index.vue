<template>
    <AdminLayout>
        <AdminWrapper>
            <VCard class="mt-3">
                <VCardTitle class="d-flex justify-space-between">
                    <span>Удаленные посты</span>
                    <Link :href="PostsRoutes.index().url" class="text-decoration-none text-green-darken-3">
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
                        :items="posts.data"
                        :itemsLength="posts.total"
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
							{ key: 'status', title: 'Статус', sortable: false, align: 'center' },
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
                        <template #item.status="{ item }">
                            {{ statuses.find(s => s.value === item.status)?.title }}
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
            <VDialog :model-value="!!postForDelete" max-width="420">
                <VCard v-if="postForDelete">
                    <VCardTitle>Удалить пост?</VCardTitle>
                    <VCardText>«{{ postForDelete.title }}»</VCardText>
                    <VCardActions>
                        <VBtn :disabled="deleteForm.processing" @click="cancelDelete">Отмена</VBtn>
                        <VBtn :loading="deleteForm.processing" color="error" @click="confirmedDelete()">
                            Удалить
                        </VBtn>
                    </VCardActions>
                </VCard>
            </VDialog>
            <VDialog :model-value="!!postForRestore" max-width="420">
                <VCard v-if="postForRestore">
                    <VCardTitle>Восстановить пост?</VCardTitle>
                    <VCardText>«{{ postForRestore.title }}»</VCardText>
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

import PostsRoutes from "~routes/Admin/PostController.ts";
import {Link, router} from "@inertiajs/vue3";
import AdminLayout from "~vue/Layouts/AdminLayout.vue";
import AdminWrapper from "~vue/Layouts/AdminWrapper.vue";
import type {TypedPagination} from "~vue/shared/pagination.ts";
import type {PostStatus, PostsTrashQuery, PostTrashResource} from "~types/generated.ts";
import type {RequiredKeys} from "~vue/shared/objects.ts";
import {reactive, watch} from "vue";
import {debounce, merge} from "lodash-es";
import useSpatieSortAdapter from "~vue/composables/useSpatieSortAdapter.ts";
import useSpatieDateRangeAdapter from "~vue/composables/useSpatieDateRangeAdapter.ts";
import PostTrashRoutes from "~routes/Admin/Trash/PostTrashController.ts";
import useTrashActions from "~vue/composables/useTrashActions.ts";

const {query = {}} = defineProps<{
    posts: TypedPagination<PostTrashResource>,
    query: PostsTrashQuery,
}>()

const queryDefaults: RequiredKeys<PostsTrashQuery, 'filter'> = {
    filter: {}
}
const queryLocal = reactive(merge({}, queryDefaults, query));
const onTitleUpd = debounce((v: string | null) => queryLocal.filter.title = v ? v : undefined, 900);

watch(queryLocal, applyReload);

function applyReload() {
    router.visit(PostTrashRoutes.index({
        query: queryLocal
    }));
}

const statuses: Array<{ title: string, value: PostStatus }> = [
    {value: 0, title: 'Опубликованные'},
    {value: 5, title: 'Черновики'},
    {value: 10, title: 'На модерации'},
    {value: 15, title: 'Отклонённые'}
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
    itemForRestore: postForRestore,
    itemForDelete: postForDelete,
    restoreForm,
    deleteForm,
    confirmRestore,
    confirmDelete,
    cancelRestore,
    cancelDelete,
    restore: confirmedRestore,
    forceDelete: confirmedDelete,
} = useTrashActions<PostTrashResource>({
    restoreRoute: id => PostTrashRoutes.update(id),
    forceDeleteRoute: id => PostTrashRoutes.destroy(id),
});
</script>
