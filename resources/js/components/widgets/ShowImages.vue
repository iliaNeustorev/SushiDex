<template>
    <VCard class="mb-4">
        <VRow>
            <VCol v-for="img in images" cols="2" :key="img.id">
                <img :src="'/storage/' + img.path" alt="" class="w-100">
                <VBtn @click="removeImage(img.id)" color="error">Удалить</VBtn>
            </VCol>
        </VRow>
    </VCard>
</template>

<script setup lang="ts">
import type {ImageCrudResource} from "~types/generated";
import {router} from "@inertiajs/vue3";
import ImagesRoutes from "~routes/Admin/ImagesController.ts";

const {images} = defineProps<{
    images: ImageCrudResource[]
}>()

function removeImage(image: number) {
    router.visit(ImagesRoutes.destroy({image}), {
        only: ['images'],
        preserveScroll: true,
    })
}
</script>
