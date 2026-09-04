<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthLayout from '~vue/Layouts/AuthLayout.vue';
import SessionRoutes from '~routes/Auth/SessionController';
import RegisterRoutes from '~routes/Auth/RegisterController';
import ForgotPasswordRoutes from '~routes/Auth/PasswordResetController';

const form = useForm({
    email: '',
    password: '',
    remember: true,
});

function send(): void {
    form.submit(SessionRoutes.store());
}
</script>

<template>
    <Head title="Вход — SushiDex" />
    <AuthLayout>
        <template #title>С возвращением</template>
        <template #description>Войдите, чтобы увидеть историю заказов, сохранить любимые блюда и пользоваться бонусами.</template>

        <p class="auth-kicker">Личный кабинет</p>
        <h2 class="auth-heading">Войти в аккаунт</h2>
        <p class="auth-lead">Введите почту и пароль, указанные при регистрации.</p>

        <form class="auth-form" @submit.prevent="send">
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
                autocomplete="current-password"
                label="Пароль"
                type="password"
                variant="outlined"
            />
            <div class="auth-row">
                <VCheckbox
                    v-model="form.remember"
                    :error-messages="form.errors.remember"
                    color="#df5f45"
                    density="compact"
                    hide-details
                    label="Запомнить меня"
                />
                <Link :href="ForgotPasswordRoutes.create().url" class="auth-link">Забыли пароль?</Link>
            </div>
            <VBtn class="auth-submit" :disabled="form.processing" :loading="form.processing" type="submit">
                Войти
            </VBtn>
        </form>

        <p class="auth-switch">Впервые в SushiDex?<Link :href="RegisterRoutes.create().url">Создать аккаунт</Link></p>
    </AuthLayout>
</template>
