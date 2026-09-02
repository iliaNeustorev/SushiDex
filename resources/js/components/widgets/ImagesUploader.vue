<script setup lang="ts">
import {useForm} from '@inertiajs/vue3';
import type {ImagesUploadReqDTO} from '~gen/types/generated';
import AdminImages from '~gen/wayfinder/actions/App/Http/Controllers/Admin/ImagesController.ts';

const {item, id} = defineProps<{ item: string, id: number }>()

const form = useForm<ImagesUploadReqDTO>({
    item,
    id,
    images: []
})

function send() {
    form.submit(
        AdminImages.store(),
        {
            onSuccess() {
                form.images = []
            },
            only: ['images']/* ,
			preserveScroll: true */
        }
    )
}

</script>

<template>
    <VCard>
        <VCardTitle>Images upload</VCardTitle>
        <VCardText>
            <VFileInput v-model="form.images" multiple density="compact"/>
            <VBtn @click="send" :disabled="form.images.length < 1" color="primary">Upload</VBtn>
            <VAlert v-if="Object.keys(form.errors).length > 0" type="error" class="mt-3">
                <p v-for="err in form.errors">{{ err }}</p>
            </VAlert>
        </VCardText>
    </VCard>
</template>
