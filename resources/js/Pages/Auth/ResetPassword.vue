<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthLayout from '~vue/Layouts/AuthLayout.vue';
import ResetPasswordRoutes from '~routes/Auth/NewPasswordController';
import SessionRoutes from '~routes/Auth/SessionController';

const { token } = defineProps<{
    token: string;
}>();

const params = new URLSearchParams(window.location.search);
const email = params.get('email');

const form = useForm({
    token,
    email,
    password: '',
    password_confirmation: '',
});

function send(): void {
    form.submit(ResetPasswordRoutes.store());
}
</script>

<template>
    <Head title="Новый пароль — SushiDex" />
    <AuthLayout>
        <template #title>Придумайте новый пароль</template>
        <template #description>Остался последний шаг. Укажите новый пароль — после сохранения вы снова сможете войти в свой аккаунт.</template>

        <p class="auth-kicker">Новый пароль</p>
        <h2 class="auth-heading">Восстановление доступа</h2>
        <p class="auth-lead">Используйте надёжный пароль, который не применяете на других сайтах.</p>

        <form class="auth-form" @submit.prevent="send">
            <input v-model="form.token" type="hidden">
            <VTextField
                v-model="form.email"
                :error-messages="form.errors.email"
                autocomplete="email"
                label="Электронная почта"
                type="email"
                variant="outlined"
            />
            <VTextField
                v-model="form.password"
                :error-messages="form.errors.password"
                autocomplete="new-password"
                label="Новый пароль"
                type="password"
                variant="outlined"
            />
            <VTextField
                v-model="form.password_confirmation"
                autocomplete="new-password"
                label="Повторите новый пароль"
                type="password"
                variant="outlined"
            />
            <VBtn class="auth-submit" :disabled="form.processing" :loading="form.processing" type="submit">
                Сохранить пароль
            </VBtn>
        </form>

        <p class="auth-switch">Хотите вернуться?<Link :href="SessionRoutes.create().url">На страницу входа</Link></p>
    </AuthLayout>
</template>
