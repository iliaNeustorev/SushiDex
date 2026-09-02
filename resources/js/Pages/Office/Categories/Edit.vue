<template>
    <DefaultLayout>
        <OfficeWrapper>
            <VCard :loading="form.processing" class="mt-3">
                <VCardTitle tag="h1">Редактировать категорию</VCardTitle>
                <VDivider/>
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

                <VCardActions>
                    <v-btn
                        @click="sendEdit"
                        :disabled="form.processing"
                        color="primary"
                    >
                        Сохранить
                    </v-btn>
                </VCardActions>

            </VCard>

            <VCard class="mb-4">
                <VRow>
                    <VCol v-for="img in images" cols="2">
                        <img :src="'/storage/' + img.path" alt="" class="w-100">
                        <VBtn @click="removeImage(img.id)" color="error">X</VBtn>
                    </VCol>
                </VRow>
            </VCard>
            <ImagesUploader item="category" :id="category.id"/>

        </OfficeWrapper>
    </DefaultLayout>
</template>

<script setup lang="ts">
import DefaultLayout from "~vue/Layouts/DefaultLayout.vue";
import {router, useForm} from "@inertiajs/vue3";
import type {
    CategoriesSaveReqDTO,
    CategoryCrudResource,
    ImageCrudResource,
} from "~types/generated";
import CategoriesRoutes from "~routes/Admin/CategoryController.ts";
import OfficeWrapper from "~vue/Layouts/OfficeWrapper.vue";
import ImagesUploader from "~vue/components/widgets/ImagesUploader.vue";
import AdminImages from "~routes/Admin/ImagesController.ts";

const props = defineProps<{
    category: CategoryCrudResource,
    images: ImageCrudResource[]
}>();

const form = useForm<CategoriesSaveReqDTO>({
    url: props.category.url,
    title: props.category.title,
    type: props.category.type,
    parent_id: props.category.parent_id
})

function sendEdit() {
    form.submit(CategoriesRoutes.update(props.category));
}

function removeImage(image: number) {
    router.visit(AdminImages.destroy({image}), {
        only: ['images']/* ,
		preserveScroll: true */
    })
}
</script>

