<template>
    <AdminLayout>
        <AdminWrapper>
            <VCard class="mt-3">
                <VCardTitle class="d-flex justify-space-between">
                    <span>Тэги</span>
                    <Link :href="TagsRoutes.create().url" class="text-decoration-none text-green-darken-3">
                        Новый тэг
                    </Link>
                </VCardTitle>
                <VDivider/>
                <VCardText>
                    <VRow>
                        <VCol>
                            <VTextField
                                :model-value="queryLocal.filter.url"
                                @update:model-value="onUrlUpd"
                                label="Url"
                                variant="outlined"
                                class="mb-2"
                                :maxlength="64"
                                clearable
                            />
                        </VCol>
                        <VCol>
                            <VTextField
                                :model-value="queryLocal.filter.title"
                                @update:model-value="onTitleUpd"
                                label="Имя"
                                variant="outlined"
                                class="mb-2"
                                :maxlength="64"
                                clearable
                            />
                        </VCol>
                    </VRow>
                </VCardText>
                <VCard class="mt-3">
                    <VCardText>
                        <VDataTableServer
                            :items="tags.data"
                            :items-length="tags.total"
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
                            {key: 'url', title: 'URL'},
                            {key: 'created_at', title: 'Дата создания'},
                            {key: 'actions', title: 'Действия', sortable: false, align: 'center'}
                        ]"
                            :sort-by="sortAdapter.sortBy.value"
                            @update:page="queryLocal.page = $event"
                            @update:items-per-page="queryLocal.batch = $event"
                            @update:sort-by="sortAdapter.onSort">
                            <template #item.created_at="{ item }">{{
                                    new Date(item.created_at).toLocaleString()
                                }}
                            </template>
                            <template #item.actions="{ item }">
                                <VContainer>
                                    <VRow class="align-center justify-center">
                                        <VCol cols="auto">
                                            <VBtn density="compact" color="green-darken-1">
                                                <Link :href="TagsRoutes.edit(item).url"
                                                      class="text-decoration-none">
                                                    <span class="text-white">Редактировать</span>
                                                </Link>
                                            </VBtn>
                                        </VCol>
                                        <VCol cols="auto">
                                            <VBtn @click="tagForRemove = item" density="compact"
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
                    :model-value="!!tagForRemove"
                    max-width="420"
                >
                    <VCard v-if="tagForRemove">
                        <VCardTitle>Удалить тэг?</VCardTitle>
                        <VCardText>«{{ tagForRemove.title }}»</VCardText>
                        <VCardActions>
                            <VBtn
                                :disabled="deleteForm.processing"
                                @click="tagForRemove = null">
                                Отмена
                            </VBtn>
                            <VBtn
                                :loading="deleteForm.processing"
                                color="error"
                                @click="removeConfirmed">
                                Удалить
                            </VBtn>
                        </VCardActions>
                    </VCard>
                </VDialog>
            </VCard>
        </AdminWrapper>
    </AdminLayout>
</template>

<script setup lang="ts">

import AdminLayout from "~vue/Layouts/AdminLayout.vue";
import AdminWrapper from "~vue/Layouts/AdminWrapper.vue";
import TagsRoutes from "~routes/Admin/TagController.ts";
import {Link, router, useForm} from "@inertiajs/vue3";
import type {TypedPagination} from "~vue/shared/pagination.ts";
import type {TagsQuery, TagCrudResource} from "~types/generated";
import type {RequiredKeys} from "~vue/shared/objects.ts";
import {reactive, ref, watch} from "vue";
import {debounce, merge} from "lodash";
import useSpatieSortAdapter from "~vue/composables/useSpatieSortAdapter.ts";

const {query = {}} = defineProps<{
    tags: TypedPagination<TagCrudResource>,
    query: TagsQuery,
}>();


const queryDefaults: RequiredKeys<TagsQuery, 'filter'> = {filter: {}};
const queryLocal = reactive(merge({}, queryDefaults, query));
const onTitleUpd = debounce((value: string | null) => queryLocal.filter.title = value || undefined, 400);
const onUrlUpd = debounce((value: string | null) => queryLocal.filter.url = value || undefined, 400);
const sortAdapter = useSpatieSortAdapter(() => queryLocal.sort, sort => queryLocal.sort = sort);
watch(queryLocal, () => router.visit(TagsRoutes.index({query: queryLocal})));

const tagForRemove = ref<TagCrudResource | null>(null);
const deleteForm = useForm({});

function removeConfirmed() {
    if (tagForRemove.value) {
        deleteForm.submit(TagsRoutes.destroy(tagForRemove.value), {
            onFinish() {
                tagForRemove.value = null;
            }
        });
    }
}
</script>
