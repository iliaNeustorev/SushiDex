<template>
    <AdminLayout>
        <AdminWrapper>
            <VCard class="mt-3">
                <VCardTitle class="d-flex justify-space-between">
                    <span>Пользователи</span>
                </VCardTitle>
                <VDivider/>
                <VCardText>
                    <VRow>
                        <VCol>
                            <VTextField
                                :model-value="queryLocal.filter.name"
                                @update:model-value="onNameUpd"
                                label="Имя"
                                variant="outlined"
                                :maxlength="255"
                                class="mb-2"
                                clearable
                            />
                        </VCol>
                        <VCol>
                            <VTextField
                                :model-value="queryLocal.filter.phone"
                                @update:model-value="onPhoneUpd"
                                label="Телефон"
                                variant="outlined"
                                :maxlength="255"
                                class="mb-2"
                                clearable
                            />
                        </VCol>
                        <VCol>
                            <VTextField
                                :model-value="queryLocal.filter.address"
                                @update:model-value="onAddressUpd"
                                label="Адрес"
                                variant="outlined"
                                :maxlength="255"
                                class="mb-2"
                                clearable
                            />
                        </VCol>
                    </VRow>
                </VCardText>
            </VCard>
            <VCard class="mt-3">
                <VCardText>
                    <VDataTableServer
                        :items="users.data"
                        :itemsLength="users.total"
                        :items-per-page="queryLocal.batch ?? 10"
                        :page="queryLocal.page ?? 1"
                        :items-per-page-options="[
							{ title: '10', value: 10 },
							{ title: '20', value: 20 },
							{ title: '50', value: 50 }
						]"
                        :headers="[
							{ key: 'id', title: 'ID' },
							{ key: 'name', title: 'ФИО', sortable: false },
							{ key: 'address', title: 'Адрес', sortable: false },
							{ key: 'phone', title: 'Телефон', sortable: false },
							{ key: 'block', title: 'Бан', sortable: false },
							{ key: 'roles', title: 'Роли' },
							{ key: 'created_at', title: 'Дата регистрации' },
							//{ key: 'actions', title: 'Действия', sortable: false, align: 'center'}
						]"
                        :sort-by="sortAdapter.sortBy.value"
                        @update:page="queryLocal.page = $event"
                        @update:items-per-page="queryLocal.batch = $event"
                        @update:sort-by="sortAdapter.onSort"
                    >
                        <template #item.created_at="{ item }">
                            {{ (new Date(item.created_at)).toLocaleString() }}
                        </template>
                        <template #item.name="{ item }">
                            {{
                                String(item.last_name ?? '') + ' ' + String(item.first_name ?? '') + ' ' + String(item.middle_name ?? '')
                            }}
                        </template>
                        <template #item.roles="{ item }">
                            <p v-for="(role, i) in item.roles" :key="i">
                                {{ role.description }}
                            </p>
                        </template>
                        <!--                        <template #item.actions="{ item }">-->
                        <!--                            <VContainer>-->
                        <!--                                <VRow class="align-center justify-center">-->
                        <!--                                    <VCol cols="auto">-->
                        <!--                                        <VBtn density="compact" color="green-darken-1">-->
                        <!--                                            <Link :href="PostsRoutes.edit(item.id).url" class="text-decoration-none">-->
                        <!--                                                <span class="text-white">Редактировать</span>-->
                        <!--                                            </Link>-->
                        <!--                                        </VBtn>-->
                        <!--                                    </VCol>-->
                        <!--                                    <VCol cols="auto">-->
                        <!--                                        <VBtn @click="confirmRemove(item)" density="compact"-->
                        <!--                                              color="deep-orange-lighten-1">-->
                        <!--                                            <span class="text-white">Удалить</span>-->
                        <!--                                        </VBtn>-->
                        <!--                                    </VCol>-->
                        <!--                                </VRow>-->
                        <!--                            </VContainer>-->
                        <!--                        </template>-->
                    </VDataTableServer>
                </VCardText>
            </VCard>
            <!--            <VDialog :model-value="!!postForRemove" max-width="420">-->
            <!--                <VCard v-if="postForRemove">-->
            <!--                    <VCardTitle>Удалить пост?</VCardTitle>-->
            <!--                    <VCardText>«{{ postForRemove.title }}»</VCardText>-->
            <!--                    <VCardActions>-->
            <!--                        <VBtn-->
            <!--                            :disabled="deleteForm.processing"-->
            <!--                            @click="postForRemove = null">-->
            <!--                            Отмена-->
            <!--                        </VBtn>-->
            <!--                        <VBtn-->
            <!--                            :loading="deleteForm.processing"-->
            <!--                            color="error"-->
            <!--                            @click="removeConfirmed">-->
            <!--                            Удалить-->
            <!--                        </VBtn>-->
            <!--                    </VCardActions>-->
            <!--                </VCard>-->
            <!--            </VDialog>-->
        </AdminWrapper>
    </AdminLayout>
</template>

<script setup lang="ts">

import {router} from '@inertiajs/vue3';
import {reactive, watch} from 'vue';
import AdminLayout from "~vue/Layouts/AdminLayout.vue";
import UsersRoutes from "~routes/Admin/UserController"
import type {RoleCrudResource, UserCrudResource, UsersQuery} from "~types/generated";
import type {TypedPagination} from '~vue/shared/pagination';
import {debounce, merge} from 'lodash';
import type {RequiredKeys} from "~vue/shared/objects.ts";
import useSpatieSortAdapter from '~vue/composables/useSpatieSortAdapter';
import AdminWrapper from "~vue/Layouts/AdminWrapper.vue";

const {query = {}} = defineProps<{
    users: TypedPagination<UserCrudResource>,
    query: UsersQuery,
    rolesForSelect: RoleCrudResource[]
}>()

const queryDefaults: RequiredKeys<UsersQuery, 'filter'> = {
    filter: {}
}
const queryLocal = reactive(merge({}, queryDefaults, query));
const onNameUpd = debounce((v: string | null) => queryLocal.filter.name = v ? v : undefined, 400);
const onAddressUpd = debounce((v: string | null) => queryLocal.filter.address = v ? v : undefined, 400);
const onPhoneUpd = debounce((v: string | null) => queryLocal.filter.phone = v ? v : undefined, 400);

watch(queryLocal, applyReload);

function applyReload() {
    router.visit(UsersRoutes.index({
        query: queryLocal
    }));
}

const sortAdapter = useSpatieSortAdapter(() => queryLocal.sort, sort => queryLocal.sort = sort);

</script>
