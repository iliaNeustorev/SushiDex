<template>
    <DefaultLayout>
        <AdminWrapper>
            <VCard class="mt-3">
                <VCardTitle tag="h1">Your posts</VCardTitle>
                <VCardSubtitle>
                    <Link :href="PostsRoutes.create().url">Create Post</Link>
                </VCardSubtitle>
                <VDivider/>
                <VCardText>
                    <VRow>
                        <VCol>
                            <VTextField
                                :model-value="queryLocal.filter.title"
                                @update:model-value="onTitleUpd"
                                label="Title"
                                variant="outlined"
                                class="mb-2"
                                clearable
                            />
                        </VCol>
                        <VCol>
                            <VSelect
                                v-model="queryLocal.filter.status"
                                :items="statuses"
                                label="Status"
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
                                label="Date"
                                variant="outlined"
                                class="mb-2"
                                multiple="range"
                                clearable
                            />
                        </VCol>
                        <VCol cols="12">
                            <VAutocomplete
                                :items="tags"
                                :model-value="tagsValue"
                                @update:model-value="onTagsChange"
                                v-model:search="tagSearch"
                                color="blue-grey-lighten-2"
                                item-title="url"
                                item-value="id"
                                label="Choose tags"
                                chips
                                closable-chips
                                multiple
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
							{ key: 'title', title: 'Title' },
							{ key: 'created_at', title: 'Creation date' },
							{ key: 'status', title: 'Status', sortable: false },
							{ key: 'actions', title: 'Actions', sortable: false }
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
                        <template #item.actions="{ item }">
                            <Link :href="PostsRoutes.edit(item.id).url" type="button" class="btn btn-success ms-2">
                                Edit
                            </Link>
                            <button @click="confirmRemove(item)" type="button" class="btn btn-danger ms-2">Delete
                            </button>
                        </template>
                    </VDataTableServer>
                </VCardText>
            </VCard>
            <VDialog
                :model-value="!!postForRemove"
                max-width="400"
                title="Are you sure you want to delete this post?"
                @close="postForRemove = null"
            >
                <template #default v-if="postForRemove">
                    <VCard>
                        <table class="table table-bordered">
                            <tbody>
                            <tr>
                                <td>Id</td>
                                <td>{{ postForRemove.id }}</td>
                            </tr>
                            <tr>
                                <td>Title</td>
                                <td>{{ postForRemove.title }}</td>
                            </tr>
                            </tbody>
                        </table>
                        <button :disabled="deleteForm.processing" @click="removeConfirmed" type="button"
                                class="btn btn-success">Ok
                        </button>
                        <button :disabled="deleteForm.processing" @click="postForRemove = null" type="button"
                                class="btn btn-danger">Cancel
                        </button>
                    </VCard>
                </template>
            </VDialog>
        </AdminWrapper>
    </DefaultLayout>
</template>

<script setup lang="ts">
import {Link, useForm, router} from '@inertiajs/vue3';
import {reactive, ref, watch, computed} from 'vue';
import DefaultLayout from "~vue/Layouts/DefaultLayout.vue";
import AdminWrapper from "~vue/Layouts/AdminWrapper.vue";
import PostsRoutes from "~routes/Admin/PostController"
import type {PostCrudResource, PostsQuery, Status, TagCrudResource} from "~types/generated";
import type {TypedPagination} from '~vue/shared/pagination';
import {debounce, merge} from 'lodash';
import type {RequiredKeys} from "~vue/shared/objects.ts";
import useSpatieDateRangeAdapter from '~vue/composables/useSpatieDateRangeAdapter';
import useSpatieSortAdapter from '~vue/composables/useSpatieSortAdapter';

const {query = {}} = defineProps<{
    posts: TypedPagination<PostCrudResource>,
    query: PostsQuery,
    tags: TagCrudResource[]
}>()

const queryDefaults: RequiredKeys<PostsQuery, 'filter'> = {
    filter: {}
}
const queryLocal = reactive(merge({}, queryDefaults, query));
const onTitleUpd = debounce((v: string | null) => queryLocal.filter.title = v ? v : undefined, 400);

watch(queryLocal, applyReload);

function applyReload() {
    router.visit(PostsRoutes.index({
        query: queryLocal
    }));
}

const statuses: Array<{ title: string, value: Status }> = [
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
const postForRemove = ref<PostCrudResource | null>(null);
const deleteForm = useForm({});

function confirmRemove(post: PostCrudResource | null) {
    postForRemove.value = post;
}

function removeConfirmed() {
    if (postForRemove.value) {
        deleteForm.submit(PostsRoutes.destroy(postForRemove.value), {
            onFinish() {
                postForRemove.value = null;
            }
        });
    }
}

function onTagsChange(value: number[]) {
    queryLocal.filter.tags = value.length ? value.join(',') : undefined
}

const tagsValue = computed(() => queryLocal.filter.tags?.split(',').map(v => +v) ?? []);

const tagSearch = ref('');
const onTagSerach = debounce((v: string) => {
    router.visit(
        PostsRoutes.index({query: {...queryLocal, tagsearch: v ? v : undefined}}),
        {preserveState: true, only: ['tags']}
    );
}, 300);

watch(tagSearch, onTagSerach);
</script>
