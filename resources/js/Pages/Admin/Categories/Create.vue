<template>
    <AdminLayout>
        <AdminWrapper>
            <VCard :loading="form.processing" class="mt-3">
                <VCardTitle tag="h1">Создать категорию</VCardTitle>
                <VDivider class="mb-2"/>
                <form @submit.prevent="send" class="mb-3 ml-2">
                    <VTextField
                        v-model="form.url"
                        :counter="10"
                        :error-messages="form.errors.url"
                        label="Url"
                    ></VTextField>

                    <VTextField
                        v-model="form.title"
                        :counter="7"
                        :error-messages="form.errors.title"
                        label="Заголовок"
                    ></VTextField>

                    <VSelect
                        v-model="form.type"
                        :error-messages="form.errors.type"
                        :items="Object.keys(props.types).map(Number)"
                        :item-title="id => props.types[id]"
                        :item-value="id => id"
                        label="Тип" ,
                    ></VSelect>

                    <VSelect
                        v-model="form.parent_id"
                        :error-messages="form.errors.parent_id"
                        :items="categories"
                        item-title="title"
                        item-value="id"
                        label="Категория" ,
                        clearable
                    ></VSelect>

                    <VBtn
                        class="me-4"
                        type="submit"
                    >
                        Создать
                    </VBtn>

                    <VBtn @click="resetForm">
                        Очистить
                    </VBtn>
                </form>
            </VCard>
        </AdminWrapper>
    </AdminLayout>
</template>

<script setup lang="ts">

import AdminLayout from "~vue/Layouts/AdminLayout.vue";
import {useForm} from "@inertiajs/vue3";
import type {CategoriesSaveReqDTO, CategoryCrudResource} from "~types/generated";
import CategoriesRoutes from "~routes/Admin/CategoryController.ts";
import AdminWrapper from "~vue/Layouts/AdminWrapper.vue";

const props = defineProps<{
    types: Record<number, string>,
    categories: CategoryCrudResource[]
}>();

const form = useForm<CategoriesSaveReqDTO>({
    url: '',
    title: '',
    type: 1,
    parent_id: null
})

function send() {
    form.submit(CategoriesRoutes.store());
}

function resetForm() {
    form.reset();
}
</script>
