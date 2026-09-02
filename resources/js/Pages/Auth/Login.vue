<script setup lang="ts">
import {Link, useForm} from '@inertiajs/vue3'
import DefaultLayout from '~vue/Layouts/DefaultLayout.vue';
import SessionRoutes from '~routes/Auth/SessionController'
import RegisterRoutes from '~routes/Auth/RegisterController'
import ForgotPasswordRoutes from '~routes/Auth/PasswordResetController'

const form = useForm({
    email: '',
    password: '',
    remember: true
})

function send() {
    form.submit(SessionRoutes.store());
}

</script>

<template>
    <DefaultLayout>
        <VCard :loading="form.processing" class="mt-3">
            <VCardTitle tag="h1">Enter to site</VCardTitle>
            <VDivider/>
            <VCardText>
                <VTextField
                    v-model="form.email"
                    :error-messages="form.errors.email"
                    label="Email"
                    variant="outlined"
                    class="mb-2"
                />
                <VTextField
                    v-model="form.password"
                    :error-messages="form.errors.password"
                    type="password"
                    label="Password"
                    variant="outlined"
                    class="mb-2"
                />
                <VCheckbox
                    v-model="form.remember"
                    :error-messages="form.errors.remember"
                    label="Remeber auth"
                    color="red"
                />
                <VBtn @click="send" :disabled="form.processing" color="primary">Login</VBtn>
                <Link :href="RegisterRoutes.create().url" type="button" class="btn btn-primary">Register</Link>
                <Link :href="ForgotPasswordRoutes.create().url">Forgot password</Link>
            </VCardText>
        </VCard>
    </DefaultLayout>
</template>
