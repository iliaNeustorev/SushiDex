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
							{ key: 'phone', title: 'Телефон' },
							{ key:'verified_at', title:'Верификация номера' },
							{ key: 'block', title: 'Бан', align: 'center' },
							{ key: 'roles', title: 'Роли', sortable: false, align: 'center', maxWidth:300 },
							{ key: 'created_at', title: 'Дата регистрации' },
						]"
                        :sort-by="sortAdapter.sortBy.value"
                        @update:page="queryLocal.page = $event"
                        @update:items-per-page="queryLocal.batch = $event"
                        @update:sort-by="sortAdapter.onSort"
                    >
                        <template #item.created_at="{ item }">
                            {{ (new Date(item.created_at)).toLocaleString() }}
                        </template>
                        <template #item.phone="{ item }">
                            {{ item.phone !== null ? item.phone.phone : 'не указан' }}
                        </template>
                        <template #item.verified_at="{ item }">
                            {{ item.phone !== null ? new Date(item.phone.verified_at).toLocaleString() : '-' }}
                        </template>
                        <template #item.name="{ item }">
                            {{
                                String(item.last_name ?? '') + ' ' + String(item.first_name ?? '') + ' ' + String(item.middle_name ?? '')
                            }}
                        </template>
                        <template #item.roles="{ item }">
                            <VAutocomplete
                                :model-value="item.roles.map(role => role.id)"
                                :items="rolesForSelect"
                                :error-messages="changeRolesUserId === item.id ? changeRoles.errors.roleIds : undefined"
                                item-title="description"
                                item-value="id"
                                label="Выбрать роли"
                                chips
                                closable-chips
                                multiple
                                @update:model-value="sendChangeRoles(item.id, $event)"
                                variant="underlined"
                            />
                        </template>
                        <template #item.block="{ item }">
                            <div class="d-flex justify-center">
                                <VCheckbox
                                    class="flex-grow-0"
                                    v-model="item.block"
                                    color="red-darken-3"
                                    hide-details
                                    @update:model-value="sendBlock(item.id, item.block)"
                                ></VCheckbox>
                            </div>
                        </template>
                    </VDataTableServer>
                </VCardText>
            </VCard>
        </AdminWrapper>
    </AdminLayout>
</template>

<script setup lang="ts">

import {router, useForm} from '@inertiajs/vue3';
import {reactive, ref, watch} from 'vue';
import AdminLayout from "~vue/Layouts/AdminLayout.vue";
import UsersRoutes from "~routes/Admin/UserController"
import type {
    RoleCrudResource,
    UserChangeBlockDTO,
    UserChangeRolesRequestDTO,
    UserCrudResource,
    UsersQuery
} from "~types/generated";
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
const onNameUpd = debounce((v: string | null) => queryLocal.filter.name = v ? v : undefined, 900);
const onAddressUpd = debounce((v: string | null) => queryLocal.filter.address = v ? v : undefined, 900);
const onPhoneUpd = debounce((v: string | null) => queryLocal.filter.phone = v ? v : undefined, 900);

watch(queryLocal, applyReload);

function applyReload() {
    router.visit(UsersRoutes.index({
        query: queryLocal,
    }), {
        only: ['users', 'query'],
        preserveScroll: true,
        replace: true,
    });
}

const form = useForm<UserChangeBlockDTO>({
    block: false
});
const changeRoles = useForm<UserChangeRolesRequestDTO>({
    roleIds: []
})
const changeRolesUserId = ref<number | null>(null);

function sendBlock(id: number, block: boolean) {
    form.block = Boolean(block);
    form.submit(UsersRoutes.changeBlock(id), {
        preserveScroll: true,
        preserveState: true,
    });
}

function sendChangeRoles(userId: number, roleIds: number[]) {
    changeRolesUserId.value = userId;
    changeRoles.clearErrors();
    changeRoles.roleIds = roleIds;
    if (!roleIds.length) {
        changeRoles.setError(
            'roleIds',
            'У пользователя должна быть хотя бы одна роль'
        );
        return;
    }
    changeRoles.submit(UsersRoutes.update(userId), {
        preserveScroll: true,
        preserveState: true,
    });
}

const sortAdapter = useSpatieSortAdapter(() => queryLocal.sort, sort => queryLocal.sort = sort);

</script>
