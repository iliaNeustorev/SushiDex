<script setup lang="ts">
import {useForm} from '@inertiajs/vue3'
import ResetPasswordRoutes from '~routes/Auth/NewPasswordController'
import DefaultLayout from '~vue/Layouts/DefaultLayout.vue';

const {token} = defineProps({
    token: {type: String, required: true},
})

const params = new URLSearchParams(window.location.search);
const email = params.get('email');

const form = useForm({
    token,
    email: email,
    password: '',
    password_confirmation: ''
})

function send() {
    form.submit(ResetPasswordRoutes.store());
}

</script>

<template>
    <DefaultLayout>
        <h1>Send Email</h1>
        <hr>
        <form>
            <input v-model="form.token" type="hidden">
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input v-model="form.email" type="email" class="form-control">
                <div class="text-danger">{{ form.errors.email }}&nbsp;</div>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input v-model="form.password" type="password" class="form-control">
                <div class="text-danger">{{ form.errors.password }}&nbsp;</div>
            </div>
            <div class="mb-3">
                <label class="form-label">Password repeat</label>
                <input v-model="form.password_confirmation" type="password" class="form-control">
                <div class="text-danger">{{ form.errors.password }}&nbsp;</div>
            </div>
            <button @click="send" :disabled="form.processing" type="button" class="btn btn-primary">Send</button>
        </form>
    </DefaultLayout>
</template>
