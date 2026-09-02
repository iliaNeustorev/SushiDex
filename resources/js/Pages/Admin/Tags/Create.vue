<script setup lang="ts">
import {useForm} from "@inertiajs/vue3";
import DefaultLayout from '~vue/Layouts/DefaultLayout.vue';
import TagsRoutes from "~routes/Admin/TagController";

const form = useForm({
    url: '',
    title: '',
    description: null,
})

function send() {
    form.submit(TagsRoutes.store());
}
</script>

<template>
    <DefaultLayout>
        <h1>Create tag</h1>
        <hr>
        <form>
            <div class="mb-3">
                <label class="form-label">Url</label>
                <input v-model="form.url" type="text" class="form-control">
                <div class="text-danger">{{ form.errors.url }}&nbsp;</div>
            </div>
            <div class="mb-3">
                <label class="form-label">Title</label>
                <input v-model="form.title" type="text" class="form-control">
                <div class="text-danger">{{ form.errors.title }}&nbsp;</div>
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea v-model="form.description" class="form-control"></textarea>
                <div class="text-danger">{{ form.errors.description }}&nbsp;</div>
            </div>
            <button @click="send" :disabled="form.processing" type="button" class="btn btn-primary">Send</button>
        </form>
    </DefaultLayout>
</template>

