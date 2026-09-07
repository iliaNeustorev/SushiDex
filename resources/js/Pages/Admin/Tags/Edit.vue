<template>
    <AdminLayout>
        <AdminWrapper>
            <VCard class="mb-4">
                <VCardTitle tag="h1">Редактирование тега {{ tag.url }}</VCardTitle>
                <VDivider class="mb-2"/>
                <VCardText>
                    <VTextField
                        v-model.trim.lazy="form.url"
                        :error-messages="form.errors.url"
                        variant="outlined"
                        label="Url"
                        class="mb-1"
                    />
                    <VTextField
                        v-model.trim.lazy="form.title"
                        :error-messages="form.errors.title"
                        variant="outlined"
                        label="Title"
                        class="mb-1"
                    />
                    <VTextarea
                        v-model.trim.lazy="form.description"
                        :error-messages="form.errors.description"
                        variant="outlined"
                        label="Описание"
                        class="mb-1"
                    />
                </VCardText>
                <VCardActions>
                    <VBtn @click="sendEdit" :disabled="form.processing" color="primary">Сохранить</VBtn>
                </VCardActions>
            </VCard>
        </AdminWrapper>
    </AdminLayout>
</template>

<script setup lang="ts">

import AdminLayout from "~vue/Layouts/AdminLayout.vue";
import AdminWrapper from "~vue/Layouts/AdminWrapper.vue";
import type {TagsSaveReqDTO, TagCrudResource} from "~types/generated";
import {useForm} from "@inertiajs/vue3";
import TagsRoutes from "~routes/Admin/TagController.ts";

const props = defineProps<{
    tag: TagCrudResource,
}>();

const form = useForm<TagsSaveReqDTO>({
    url: props.tag.url,
    title: props.tag.title,
    description: props.tag.description,
})

function sendEdit() {
    form.submit(TagsRoutes.update(props.tag));
}
</script>
