<template>
    <section class="tab-card">
        <header
            class="tab-heading d-flex justify-space-between cursor-pointer"
            @click="showBlockMain = !showBlockMain"
        >
            <div>
                <span>Личные данные</span>
                <h2>Настройки профиля</h2>
            </div>
            <p>Обновите имя и адрес, которые используются при оформлении заказа.</p>
        </header>
        <div v-show="showBlockMain">
            <div class="form-grid d-grid">
                <VTextField
                    v-model="form.last_name"
                    :error-messages="form.errors.last_name"
                    autocomplete="family-name"
                    bg-color="#fff"
                    color="#df5f45"
                    label="Фамилия"
                    rounded="0"
                    variant="outlined"
                    class="mb-1"
                />
                <VTextField
                    v-model="form.first_name"
                    :error-messages="form.errors.first_name"
                    autocomplete="given-name"
                    bg-color="#fff"
                    color="#df5f45"
                    label="Имя"
                    rounded="0"
                    variant="outlined"
                    class="mb-1"
                />
                <VTextField
                    v-model="form.middle_name"
                    :error-messages="form.errors.middle_name"
                    autocomplete="additional-name"
                    bg-color="#fff"
                    color="#df5f45"
                    label="Отчество"
                    rounded="0"
                    variant="outlined"
                    class="mb-1"
                />
                <VTextarea
                    v-model="form.address"
                    :error-messages="form.errors.address"
                    class="address-field mb-1"
                    autocomplete="street-address"
                    auto-grow
                    bg-color="#fff"
                    color="#df5f45"
                    label="Адрес доставки"
                    max-rows="4"
                    rounded="0"
                    rows="2"
                    variant="outlined"
                />
            </div>

            <div class="account-info d-grid">
                <div class="d-flex flex-column">
                    <span>Электронная почта</span>
                    <strong>{{ client.email ?? 'Не указана' }}</strong>
                </div>
                <div class="d-flex flex-column">
                    <span>Подтверждённый телефон</span>
                    <strong>{{ client.phone?.phone ?? 'Не указан' }}</strong>
                </div>
                <p class="ma-0">Почта и телефон подтверждаются отдельно и не изменяются этой формой.</p>
            </div>
            <PhoneSettings :phones="client.pendingPhones"/>
            <footer class="form-actions d-flex justify-space-between">
                <span v-if="form.recentlySuccessful" class="save-message" role="status">
                    Изменения сохранены
                </span>
                <span v-else-if="form.isDirty" class="dirty-message">
                    Есть несохранённые изменения
                </span>

                <div class="d-flex align-center">
                    <VBtn
                        :disabled="!form.isDirty || form.processing"
                        class="reset-button rounded-0 text-none"
                        type="button"
                        variant="text"
                        @click="reset"
                    >
                        Отменить
                    </VBtn>
                    <VBtn
                        :disabled="!form.isDirty || form.processing"
                        :loading="form.processing"
                        class="save-button rounded-0 text-none"
                        min-width="176"
                        type="submit"
                        @click="submit"
                    >
                        Сохранить изменения
                    </VBtn>
                </div>
            </footer>
        </div>
    </section>
    <section class="tab-card mt-2">
        <header
            class="tab-heading d-flex justify-space-between cursor-pointer"
            @click="showBlockAvatar = !showBlockAvatar"
        >
            <div>
                <span>Аватар</span>
                <h2>Фото профиля</h2>
            </div>
            <p>Выберите фотографию, которая будет представлять ваш профиль.</p>
        </header>
        <div v-show="showBlockAvatar">
            <div class="avatar-content d-flex">
                <Avatar :user="client" :size="132" :font-size="68"/>
                <div class="avatar-details">
                    <p class="avatar-caption">{{
                            client.image ? 'Текущая фотография' : 'Фотография пока не добавлена'
                        }}</p>
                    <p class="avatar-description ma-0">Вы можете добавить или заменить изображение профиля.</p>
                    <VFileInput
                        v-model="avatarForm.image"
                        :error-messages="avatarForm.errors.image"
                        class="avatar-file-input"
                        accept="image/png,image/jpeg,image/bmp,image/webp"
                        bg-color="#fff"
                        color="#df5f45"
                        label="Файл аватара"
                        placeholder="Выберите изображение"
                        prepend-inner-icon="$mdiCamera"
                        rounded="0"
                        variant="outlined"
                        density="comfortable"
                        clearable
                        :disabled="avatarForm.processing"
                    />
                    <div class="avatar-actions d-flex flex-wrap">
                        <VBtn
                            class="avatar-upload-button rounded-0 text-none"
                            type="button"
                            :disabled="avatarForm.processing || isDeletingAvatar"
                            :loading="avatarForm.processing"
                            @click="sendAvatarForm"
                        >
                            {{ client.image ? 'Заменить фото' : 'Загрузить фото' }}
                        </VBtn>
                        <VBtn
                            v-if="client.image"
                            class="avatar-remove-button rounded-0 text-none"
                            type="button" variant="text"
                            :disabled="avatarForm.processing || isDeletingAvatar"
                            :loading="isDeletingAvatar"
                            @click="deleteAvatar"
                        >
                            Удалить фото
                        </VBtn>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="tab-card mt-2">
        <header
            class="tab-heading d-flex justify-space-between cursor-pointer"
            @click="showBlockChangePassword = !showBlockChangePassword"
        >
            <div>
                <span>Безопасность</span>
                <h2>Смена пароля</h2>
            </div>
            <p>Используйте надёжный пароль, который не применяется в других сервисах.</p>
        </header>
        <ChangePassword v-show="showBlockChangePassword"/>
    </section>
</template>

<script setup lang="ts">
import {router, useForm} from '@inertiajs/vue3';
import type {ChangeAvatarReqDTO, ProfileSaveReqDTO, UserProfileResource} from '~types/generated';
import ProfileRoutes from '~routes/Client/ProfileController';
import PhoneSettings from "~vue/Pages/Profile/Components/PhoneSettings.vue";
import {ref} from "vue";
import Avatar from "~vue/components/widgets/Avatar.vue";
import ChangePassword from "~vue/Pages/Profile/Components/ChangePassword.vue";

const {client} = defineProps<{
    client: UserProfileResource;
}>();

const form = useForm<ProfileSaveReqDTO>({
    first_name: client.first_name,
    last_name: client.last_name,
    middle_name: client.middle_name,
    address: client.address,
});

const avatarForm = useForm<ChangeAvatarReqDTO>({
    item: 'user',
    image: null
})
const isDeletingAvatar = ref(false)
const showBlockMain = ref(false)
const showBlockAvatar = ref(false)
const showBlockChangePassword = ref(false)

function submit(): void {
    form.submit(ProfileRoutes.update(), {
        preserveScroll: true,
        preserveState: true,
        only: ['client']
    });
}

function sendAvatarForm(): void {
    avatarForm.submit(ProfileRoutes.changeAvatar(), {
        preserveScroll: true,
        only: ['client', 'user'],
        onSuccess: () => avatarForm.reset()
    });
}

function deleteAvatar(): void {
    isDeletingAvatar.value = true
    router.delete(ProfileRoutes.destroyAvatar(), {
        preserveScroll: true,
        only: ['client', 'user'],
        onFinish: () => isDeletingAvatar.value = false
    })
}

function reset(): void {
    form.resetAndClearErrors();
}
</script>

<style scoped>
.tab-card {
    --profile-accent: #df5f45;
    --profile-action: #766b62;
    --profile-border: #e0d6cb;
    --profile-button-border: #d7c9bc;
    --profile-caption: #92867c;
    --profile-field: #fff;
    --profile-muted: #9c8f84;
    --profile-secondary: #887d73;
    --profile-success: #687a50;
    --profile-surface: #fffaf4;
    --profile-text: #4f4943;
    padding: 31px;
    border: 1px solid var(--profile-border);
    background: var(--profile-surface);
}

/* Heading */

.tab-heading {
    align-items: end;
    gap: 30px;
    padding-bottom: 22px;
    border-bottom: 1px solid var(--profile-border);
}

.tab-heading span,
.account-info span {
    color: var(--profile-muted);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .14em;
    text-transform: uppercase;
}

.tab-heading h2 {
    margin: 6px 0 0;
    font-family: 'Prata', serif;
    font-size: 28px;
    font-weight: 400;
}

.tab-heading p {
    max-width: 340px;
    margin: 0 0 3px;
    color: var(--profile-secondary);
    font-size: 14px;
    line-height: 1.6;
}

/* Profile form */

.form-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 5px 18px;
    padding-top: 27px;
}

.address-field {
    grid-column: 1 / -1;
}

/* Account information */

.account-info {
    grid-template-columns: repeat(2, minmax(0, 1fr));
    margin-top: 11px;
    border-top: 1px solid var(--profile-border);
    border-bottom: 1px solid var(--profile-border);
}

.account-info > div {
    min-width: 0;
    gap: 7px;
    padding: 21px 0;
}

.account-info > div:nth-child(2) {
    padding-left: 24px;
    border-left: 1px solid var(--profile-border);
}

.account-info > .pending-phone-info {
    grid-column: 1 / -1;
    border-top: 1px solid var(--profile-border);
}

.account-info strong {
    overflow-wrap: anywhere;
    color: var(--profile-text);
    font-size: 15px;
    font-weight: 600;
}

.account-info p {
    grid-column: 1 / -1;
    padding: 14px 0;
    border-top: 1px solid var(--profile-border);
    color: var(--profile-caption);
    font-size: 13px;
    line-height: 1.6;
}

/* Form actions */

.form-actions {
    min-height: 44px;
    align-items: center;
    gap: 20px;
    padding-top: 24px;
}

.form-actions > div {
    gap: 10px;
    margin-left: auto;
}

.save-message,
.dirty-message {
    font-size: 13px;
}

.save-message {
    color: var(--profile-success);
}

.dirty-message {
    color: var(--profile-muted);
}

.reset-button,
.save-button {
    font-size: 13px;
    letter-spacing: .04em;
}

.reset-button {
    color: var(--profile-action);
}

.save-button {
    background: var(--profile-accent);
    color: #fff;
}

/* Avatar */

.avatar-content {
    align-items: center;
    gap: 30px;
    padding-top: 28px;
}

.avatar-details {
    flex: 1;
    min-width: 0;
}

.avatar-caption {
    margin: 0 0 6px;
    color: var(--profile-text);
    font-size: 17px;
    font-weight: 700;
}

.avatar-description {
    color: var(--profile-secondary);
    font-size: 13px;
    line-height: 1.6;
}

.avatar-file-input {
    max-width: 440px;
    margin-top: 19px;
}

.avatar-file-input :deep(.v-field__prepend-inner) {
    color: var(--profile-accent);
}

.avatar-actions {
    align-items: center;
    gap: 10px;
    margin-top: 2px;
}

.avatar-upload-button,
.avatar-remove-button {
    font-size: 13px;
}

.avatar-upload-button {
    background: var(--profile-accent);
    color: #fff;
}

.avatar-remove-button {
    color: var(--profile-action);
}


/* Responsive */

@media (max-width: 620px) {
    .tab-card {
        padding: 24px 19px;
    }

    .tab-heading {
        align-items: start;
        flex-direction: column;
        gap: 12px;
    }

    .form-grid,
    .account-info {
        grid-template-columns: 1fr;
    }

    .account-info > div:nth-child(2) {
        padding-left: 0;
        border-top: 1px solid var(--profile-border);
        border-left: 0;
    }

    .form-actions {
        align-items: stretch;
        flex-direction: column;
    }

    .form-actions > div {
        width: 100%;
        margin-left: 0;
    }

    .form-actions .save-button {
        flex: 1;
    }

    .avatar-content {
        align-items: flex-start;
        flex-direction: column;
        gap: 20px;
    }

    .avatar-details,
    .avatar-file-input {
        width: 100%;
    }

    .avatar-actions {
        align-items: stretch;
        flex-direction: column;
    }

}
</style>
