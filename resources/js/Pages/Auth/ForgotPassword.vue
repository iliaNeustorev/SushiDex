<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthLayout from '~vue/Layouts/AuthLayout.vue';
import ForgotPasswordRoutes from '~routes/Auth/PasswordResetController';
import SessionRoutes from '~routes/Auth/SessionController';

const form = useForm({
    email: '',
});

function send(): void {
    form.submit(ForgotPasswordRoutes.store());
}
</script>

<template>
    <Head title="Восстановление пароля — SushiDex" />
    <AuthLayout>
        <template #title>Вернём доступ к аккаунту</template>
        <template #description>Укажите почту, с которой регистрировались. Мы отправим безопасную ссылку для создания нового пароля.</template>

        <p class="auth-kicker">Восстановление доступа</p>
        <h2 class="auth-heading">Забыли пароль?</h2>
        <p class="auth-lead">Введите электронную почту — письмо со ссылкой придёт в течение нескольких минут.</p>

        <form class="auth-form" @submit.prevent="send">
            <VTextField
                v-model="form.email"
                :error-messages="form.errors.email"
                autocomplete="email"
                label="Электронная почта"
                type="email"
                variant="outlined"
            />
            <VBtn class="auth-submit" :disabled="form.processing" :loading="form.processing" type="submit">
                Отправить ссылку
            </VBtn>
        </form>

        <p class="auth-switch">Вспомнили пароль?<Link :href="SessionRoutes.create().url">Вернуться ко входу</Link></p>
    </AuthLayout>
</template>
