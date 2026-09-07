<template>
    <AdminLayout>
        <AdminWrapper>
            <VCard :loading="form.processing" class="mt-3">
                <VCardTitle tag="h1">Создать тэг</VCardTitle>
                <VDivider class="mb-2"/>
                <form @submit.prevent="send" class="mb-3 ml-2">
                    <VTextField
                        v-model="form.url"
                        :counter="10"
                        :error-messages="form.errors.url"
                        label="Url"
                        :maxlength="64"
                    ></VTextField>

                    <VTextField
                        v-model="form.title"
                        :counter="7"
                        :error-messages="form.errors.title"
                        label="Имя"
                        :maxlength="64"
                    ></VTextField>

                    <VTextarea
                        v-model="form.description"
                        clearable
                        :error-messages="form.errors.description"
                        label="Описание">
                    </VTextarea>
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
import {useForm} from "@inertiajs/vue3";
import AdminLayout from '~vue/Layouts/AdminLayout.vue';
import TagsRoutes from "~routes/Admin/TagController";
import AdminWrapper from "~vue/Layouts/AdminWrapper.vue";

const form = useForm({
    url: '',
    title: '',
    description: null,
})

function send() {
    form.submit(TagsRoutes.store());
}

function resetForm() {
    form.reset();
}
</script>

