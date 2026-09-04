<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthLayout from '~vue/Layouts/AuthLayout.vue';
import RegisterRoutes from '~routes/Auth/RegisterController';
import SessionRoutes from '~routes/Auth/SessionController';

const form = useForm({
    first_name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

function send(): void {
    form.post(RegisterRoutes.store().url);
}
</script>

<template>
    <Head title="Регистрация — SushiDex" />
    <AuthLayout>
        <template #title>Станьте частью SushiDex</template>
        <template #description>Создайте аккаунт — и любимые блюда, адреса доставки и история заказов всегда будут под рукой.</template>

        <p class="auth-kicker">Новый аккаунт</p>
        <h2 class="auth-heading">Регистрация</h2>
        <p class="auth-lead">Заполните четыре поля — это займёт меньше минуты.</p>

        <form class="auth-form" @submit.prevent="send">
            <VTextField
                v-model="form.first_name"
                :error-messages="form.errors.first_name"
                autocomplete="given-name"
                label="Ваше имя"
                variant="outlined"
            />
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
                label="Пароль"
                type="password"
                variant="outlined"
            />
            <VTextField
                v-model="form.password_confirmation"
                autocomplete="new-password"
                label="Повторите пароль"
                type="password"
                variant="outlined"
            />
            <VBtn class="auth-submit" :disabled="form.processing" :loading="form.processing" type="submit">
                Создать аккаунт
            </VBtn>
        </form>

        <p class="auth-switch">Уже зарегистрированы?<Link :href="SessionRoutes.create().url">Войти</Link></p>
    </AuthLayout>
</template>
