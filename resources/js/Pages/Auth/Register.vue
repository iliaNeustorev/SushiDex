<script setup lang="ts">
import {useForm} from '@inertiajs/vue3'
import DefaultLayout from '~vue/Layouts/DefaultLayout.vue';
import RegisterRoutes from '~routes/Auth/RegisterController'

const form = useForm({
    first_name: '',
    email: '',
    password: '',
    password_confirmation: '',
})

function send() {
    form.post(RegisterRoutes.store().url);
}

</script>

<template>
    <DefaultLayout>
        <h1>Register</h1>
        <hr>
        <form>
            <div class="mb-3">
                <label class="form-label">Name</label>
                <input v-model="form.first_name" type="text" class="form-control">
                <div class="text-danger">{{ form.errors.email }}&nbsp;</div>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input v-model="form.email" type="text" class="form-control">
                <div class="text-danger">{{ form.errors.email }}&nbsp;</div>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input v-model="form.password" type="password" class="form-control">
                <div class="text-danger">{{ form.errors.password }}&nbsp;</div>
            </div>
            <div class="mb-3">
                <label class="form-label">Repeat password</label>
                <input v-model="form.password_confirmation" type="password" class="form-control">
                <div class="text-danger">{{ form.errors.password }}&nbsp;</div>
            </div>
            <button @click="send" :disabled="form.processing" type="button" class="btn btn-primary">Register</button>
        </form>
    </DefaultLayout>
</template>
