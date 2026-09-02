<script setup lang="ts">
import {useForm} from '@inertiajs/vue3'
import ForgotPasswordRoutes from '~gen/wayfinder/actions/App/Http/Controllers/Auth/PasswordResetController'
import DefaultLayout from '~vue/Layouts/DefaultLayout.vue';

const form = useForm({
    email: '',
})

function send() {
    form.submit(ForgotPasswordRoutes.store());
}

</script>

<template>
    <DefaultLayout>
        <h1>Send Email</h1>
        <hr>
        <form>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input v-model="form.email" type="email" class="form-control">
                <div class="text-danger">{{ form.errors.email }}&nbsp;</div>
            </div>
            <button @click="send" :disabled="form.processing" type="button" class="btn btn-primary">Send</button>
        </form>
    </DefaultLayout>
</template>
