<template>
    <DefaultLayout>
        <AdminWrapper>
            <VCard :loading="form.processing" class="mt-3">
                <VCardTitle tag="h1">Create category</VCardTitle>
                <VDivider/>
                <form @submit.prevent="send" class="mb-3 ml-2">
                    <v-text-field
                        v-model="form.url"
                        :counter="10"
                        :error-messages="form.errors.url"
                        label="Url"
                    ></v-text-field>

                    <v-text-field
                        v-model="form.title"
                        :counter="7"
                        :error-messages="form.errors.title"
                        label="Title"
                    ></v-text-field>

                    <v-btn
                        class="me-4"
                        type="submit"
                    >
                        Create
                    </v-btn>

                    <v-btn @click="resetForm">
                        clear
                    </v-btn>
                </form>
            </VCard>
        </AdminWrapper>
    </DefaultLayout>
</template>

<script setup lang="ts">

import DefaultLayout from "~vue/Layouts/DefaultLayout.vue";
import {useForm} from "@inertiajs/vue3";
import type {CategoriesSaveReqDTO} from "~types/generated";
import CategoriesRoutes from "~routes/Admin/CategoryController.ts";
import AdminWrapper from "~vue/Layouts/AdminWrapper.vue";

const form = useForm<CategoriesSaveReqDTO>({
    url: '',
    title: '',
    type: null,
    parent_id: null
})

function send() {
    form.submit(CategoriesRoutes.store());
}

function resetForm() {
    form.reset();
}
</script>
