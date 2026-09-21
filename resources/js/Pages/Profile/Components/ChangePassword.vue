<template>
    <div class="password-content">
        <div v-if="successChange" class="text-yellow-darken-4 mb-2">Ваш пароль успешно изменен!</div>
        <div class="password-grid d-grid">
            <VTextField
                class="mb-1"
                autocomplete="current-password"
                :append-icon="showCurrentPassword ? '$mdiEye' : '$mdiEyeOff'"
                bg-color="#fff"
                color="#df5f45"
                label="Текущий пароль"
                rounded="0"
                :type="showCurrentPassword ? 'text' : 'password'"
                variant="outlined"
                v-model="form.current_password"
                :error-messages="form.errors.current_password"
                @click:append="showCurrentPassword = !showCurrentPassword"
                :disabled="form.processing"
            />
            <VTextField
                class="mb-1"
                :append-icon="showNewPassword ? '$mdiEye' : '$mdiEyeOff'"
                bg-color="#fff"
                color="#df5f45"
                label="Новый пароль"
                rounded="0"
                :type="showNewPassword ? 'text' : 'password'"
                variant="outlined" ,
                v-model="form.password"
                :error-messages="form.errors.password"
                @click:append="showNewPassword = !showNewPassword"
                :disabled="form.processing"
            />
            <VTextField
                class="mb-1"
                :append-icon="showNewPassword ? '$mdiEye' : '$mdiEyeOff'"
                bg-color="#fff"
                color="#df5f45"
                label="Подтвердите новый пароль"
                rounded="0"
                :type="showNewPassword ? 'text' : 'password'"
                variant="outlined"
                v-model="form.password_confirmation"
                @click:append="showNewPassword = !showNewPassword"
                :disabled="form.processing"
            />
        </div>
        <footer class="password-actions d-flex justify-space-between">
            <p class="ma-0">После смены пароля используйте новый пароль при следующем входе.</p>
            <VBtn
                class="save-button rounded-0 text-none"
                min-width="176"
                type="button"
                @click="sendForm"
                :loading="form.processing"
            >
                Изменить пароль
            </VBtn>
        </footer>
    </div>
</template>
<script setup lang="ts">
import {useForm} from "@inertiajs/vue3";
import type {ChangePasswordReqDTO} from "~types/generated.ts";
import ProfileRoutes from '~routes/Client/ProfileController';
import {ref} from "vue";

const form = useForm<ChangePasswordReqDTO>({
    current_password: '',
    password: '',
    password_confirmation: ''
})

const successChange = ref(false)
const showCurrentPassword = ref(false)
const showNewPassword = ref(false)

function sendForm() {
    form.submit(ProfileRoutes.changePassword(), {
        preserveState: true,
        preserveScroll: true,
        only: ['client'],
        onSuccess: () => successChange.value = true
    })
}
</script>
<style scoped>
.password-content {
    padding-top: 27px;
}

.password-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 5px 18px;
}

.password-actions {
    align-items: center;
    gap: 20px;
    padding-top: 8px;
}

.password-actions p {
    max-width: 460px;
    color: var(--profile-caption);
    font-size: 13px;
    line-height: 1.6;
}

.save-button {
    background: var(--profile-accent);
    color: #fff;
    font-size: 13px;
    letter-spacing: .04em;
}

@media (max-width: 620px) {
    .password-actions {
        align-items: stretch;
        flex-direction: column;
    }

    .password-actions .save-button {
        width: 100%;
    }

    .password-grid {
        grid-template-columns: 1fr;
    }
}
</style>
