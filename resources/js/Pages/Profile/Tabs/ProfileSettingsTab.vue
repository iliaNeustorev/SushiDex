<template>
    <section class="tab-card">
        <header class="tab-heading">
            <div>
                <span>Личные данные</span>
                <h2>Настройки профиля</h2>
            </div>
            <p>Обновите имя и адрес, которые используются при оформлении заказа.</p>
        </header>
        <div class="form-grid">
            <VTextField
                v-model="form.last_name"
                :error-messages="form.errors.last_name"
                autocomplete="family-name"
                label="Фамилия"
                variant="outlined"
            />
            <VTextField
                v-model="form.first_name"
                :error-messages="form.errors.first_name"
                autocomplete="given-name"
                label="Имя"
                variant="outlined"
            />
            <VTextField
                v-model="form.middle_name"
                :error-messages="form.errors.middle_name"
                autocomplete="additional-name"
                label="Отчество"
                variant="outlined"
            />
            <VTextarea
                v-model="form.address"
                :error-messages="form.errors.address"
                class="address-field"
                autocomplete="street-address"
                auto-grow
                label="Адрес доставки"
                max-rows="4"
                rows="2"
                variant="outlined"
            />
        </div>

        <div class="account-info">
            <div>
                <span>Электронная почта</span>
                <strong>{{ client.email ?? 'Не указана' }}</strong>
            </div>
            <div>
                <span>Подтверждённый телефон</span>
                <strong>{{ client.phone?.phone ?? 'Не указан' }}</strong>
            </div>
            <div v-if="client.pendingPhones.length > 0" class="pending-phone-info">
                <span>Неподтверждённый телефон</span>
                <strong v-for="pendingPhone in client.pendingPhones" :key="pendingPhone.id">{{
                        pendingPhone.phone
                    }}
                    <VBtn
                        class="ma-1"
                        :disabled="sendCodeForm.processing"
                        :loading="sendCodeForm.processing &&sendCodeForm.id === pendingPhone.id"
                        icon="$phoneCheckOutline"
                        type="button"
                        density="compact"
                        title="Подтвердить номер"
                        @click="sendCode(pendingPhone.id)"
                    />
                </strong>
            </div>
            <p>Почта и телефон подтверждаются отдельно и не изменяются этой формой.</p>
        </div>

        <footer class="form-actions">
                <span v-if="form.recentlySuccessful" class="save-message" role="status">
                    Изменения сохранены
                </span>
            <span v-else-if="form.isDirty" class="dirty-message">
                    Есть несохранённые изменения
                </span>

            <div>
                <VBtn
                    :disabled="!form.isDirty || form.processing"
                    class="reset-button"
                    type="button"
                    variant="text"
                    @click="reset"
                >
                    Отменить
                </VBtn>
                <VBtn
                    :disabled="!form.isDirty || form.processing"
                    :loading="form.processing"
                    class="save-button"
                    type="submit"
                    @click="submit"
                >
                    Сохранить изменения
                </VBtn>
            </div>
        </footer>

        <VDialog
            v-model="confirmPhoneDialog"
            max-width="520"
        >
            <VCard class="phone-confirm-dialog" elevation="0">
                <div class="phone-confirm-dialog__accent"></div>

                <VCardText class="phone-confirm-dialog__content">
                    <span class="phone-confirm-dialog__eyebrow">Подтверждение телефона</span>
                    <h2>Введите код из SMS</h2>
                    <p>
                        Мы отправили шестизначный код на указанный номер. Введите его,
                        чтобы завершить подтверждение.
                    </p>

                    <VAlert
                        v-if="sendCodeForm.errors.id"
                        class="phone-confirm-dialog__error"
                        density="compact"
                        type="error"
                        variant="tonal"
                    >
                        {{ sendCodeForm.errors.id }}
                    </VAlert>

                    <VTextField
                        v-model="confirmCodeForm.code"
                        :error-messages="confirmCodeForm.errors.code"
                        class="phone-confirm-dialog__code"
                        autocomplete="one-time-code"
                        inputmode="numeric"
                        label="Код подтверждения"
                        maxlength="6"
                        placeholder="000000"
                        variant="outlined"
                    />

                    <div class="phone-confirm-dialog__hint">
                        <span>Не получили сообщение?</span>
                        <span>Новый код можно запросить повторно</span>
                    </div>
                </VCardText>

                <VCardActions class="phone-confirm-dialog__actions">
                    <VBtn
                        class="phone-dialog-close-button"
                        type="button"
                        variant="text"
                        @click="closeConfirmDialog"
                    >
                        Закрыть
                    </VBtn>
                    <VBtn
                        :loading="sendCodeForm.processing"
                        :disabled="sendCodeForm.processing"
                        class="phone-dialog-resend-button"
                        type="button"
                        variant="outlined"
                        @click="sendCode(sendCodeForm.id)"
                    >
                        Отправить код повторно
                    </VBtn>
                    <VBtn
                        :loading="confirmCodeForm.processing"
                        :disabled="confirmCodeForm.processing || confirmCodeForm.code.length !== 6"
                        class="phone-dialog-confirm-button"
                        type="button"
                        @click="confirmCode"
                    >
                        Подтвердить
                    </VBtn>
                </VCardActions>
            </VCard>
        </VDialog>
    </section>
</template>

<script setup lang="ts">
import {useForm} from '@inertiajs/vue3';
import {ref} from 'vue';
import type {ConfirmCodeReqDTO, ProfileSaveReqDTO, SendCodeReqDTO, UserProfileResource} from '~types/generated';
import ProfileRoutes from '~routes/Client/ProfileController';
import PhoneRoutes from "~routes/Client/PhoneController.ts";

const {client} = defineProps<{
    client: UserProfileResource;
}>();

const form = useForm<ProfileSaveReqDTO>({
    first_name: client.first_name,
    last_name: client.last_name,
    middle_name: client.middle_name,
    address: client.address,
});

function submit(): void {
    form.submit(ProfileRoutes.update(), {
        preserveScroll: true,
        preserveState: true,
        only: ['client']
    });
}

const sendCodeForm = useForm<SendCodeReqDTO>({
    id: 0
})
const confirmCodeForm = useForm<ConfirmCodeReqDTO>({
    id: 0,
    code: ''
})
const confirmPhoneDialog = ref(false);

function sendCode(id: number) {
    sendCodeForm.clearErrors();
    confirmCodeForm.clearErrors();
    confirmPhoneDialog.value = true;
    sendCodeForm.id = id
    confirmCodeForm.id = id;
    sendCodeForm.submit(PhoneRoutes.sendCode(), {
        preserveScroll: true,
        preserveState: true,
        only: ['client'],
        onSuccess: () => {
            confirmCodeForm.code = '';
        },
    });
}

function confirmCode() {
    confirmCodeForm.submit(PhoneRoutes.confirmCode(), {
        preserveScroll: true,
        preserveState: true,
        only: ['client'],
        onSuccess: closeConfirmDialog,
    })
}

function reset(): void {
    form.reset();
}

function closeConfirmDialog(): void {
    confirmPhoneDialog.value = false;
    confirmCodeForm.resetAndClearErrors();
    sendCodeForm.clearErrors();
}
</script>

<style scoped>
.tab-card,
.phone-confirm-dialog {
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
}

.tab-card {
    padding: 31px;
    border: 1px solid var(--profile-border);
    background: var(--profile-surface);
}

/* Heading */

.tab-heading {
    display: flex;
    align-items: end;
    justify-content: space-between;
    gap: 30px;
    padding-bottom: 22px;
    border-bottom: 1px solid var(--profile-border);
}

.tab-heading span,
.account-info span {
    color: var(--profile-muted);
    font-size: 9px;
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
    font-size: 11px;
    line-height: 1.6;
}

/* Profile form */

.form-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 5px 18px;
    padding-top: 27px;
}

.address-field {
    grid-column: 1 / -1;
}

.form-grid :deep(.v-field) {
    border-radius: 0;
    background: var(--profile-field);
}

.form-grid :deep(.v-field--focused .v-field__outline) {
    color: var(--profile-accent);
}

/* Account information */

.account-info {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    margin-top: 11px;
    border-top: 1px solid var(--profile-border);
    border-bottom: 1px solid var(--profile-border);
}

.account-info > div {
    display: flex;
    min-width: 0;
    flex-direction: column;
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
    font-size: 13px;
    font-weight: 600;
}

.account-info p {
    grid-column: 1 / -1;
    margin: 0;
    padding: 14px 0;
    border-top: 1px solid var(--profile-border);
    color: var(--profile-caption);
    font-size: 10px;
    line-height: 1.5;
}

/* Form actions */

.form-actions {
    display: flex;
    min-height: 44px;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    padding-top: 24px;
}

.form-actions > div {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-left: auto;
}

.save-message,
.dirty-message {
    font-size: 11px;
}

.save-message {
    color: var(--profile-success);
}

.dirty-message {
    color: var(--profile-muted);
}

.reset-button,
.save-button {
    border-radius: 0;
    font-size: 11px;
    letter-spacing: .04em;
    text-transform: none;
}

.reset-button {
    color: var(--profile-action);
}

.save-button {
    min-width: 176px;
    background: var(--profile-accent);
    color: #fff;
}

/* Phone confirmation dialog */

.phone-confirm-dialog {
    position: relative;
    overflow: hidden;
    border: 1px solid var(--profile-border);
    border-radius: 0;
    background: var(--profile-surface);
    color: var(--profile-text);
}

.phone-confirm-dialog__accent {
    width: 100%;
    height: 4px;
    background: var(--profile-accent);
}

.phone-confirm-dialog__content {
    padding: 36px 38px 28px !important;
}

.phone-confirm-dialog__eyebrow {
    color: var(--profile-accent);
    font-size: 9px;
    font-weight: 700;
    letter-spacing: .16em;
    text-transform: uppercase;
}

.phone-confirm-dialog h2 {
    margin: 8px 0 12px;
    font-family: 'Prata', serif;
    font-size: 30px;
    font-weight: 400;
    line-height: 1.2;
}

.phone-confirm-dialog__content > p {
    max-width: 410px;
    margin: 0 0 25px;
    color: var(--profile-secondary);
    font-size: 12px;
    line-height: 1.65;
}

.phone-confirm-dialog__error {
    margin-bottom: 18px;
    border-radius: 0;
    font-size: 11px;
}

.phone-confirm-dialog__code :deep(.v-field) {
    border-radius: 0;
    background: var(--profile-field);
}

.phone-confirm-dialog__code :deep(input) {
    color: var(--profile-text);
    font-size: 24px;
    font-weight: 600;
    letter-spacing: .28em;
    text-align: center;
}

.phone-confirm-dialog__code :deep(.v-field--focused .v-field__outline) {
    color: var(--profile-accent);
}

.phone-confirm-dialog__hint {
    display: flex;
    justify-content: space-between;
    gap: 20px;
    margin-top: -4px;
    color: var(--profile-muted);
    font-size: 9px;
    line-height: 1.5;
}

.phone-confirm-dialog__actions {
    display: grid;
    grid-template-columns: auto 1fr auto;
    gap: 9px;
    padding: 20px 38px 30px !important;
    border-top: 1px solid var(--profile-border);
}

.phone-dialog-close-button,
.phone-dialog-resend-button,
.phone-dialog-confirm-button {
    border-radius: 0;
    font-size: 10px;
    letter-spacing: .03em;
    text-transform: none;
}

.phone-dialog-close-button {
    color: var(--profile-action);
}

.phone-dialog-resend-button {
    border-color: var(--profile-button-border);
    color: var(--profile-action);
}

.phone-dialog-confirm-button {
    min-width: 122px;
    background: var(--profile-accent);
    color: #fff;
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

    .phone-confirm-dialog__content {
        padding: 30px 22px 23px !important;
    }

    .phone-confirm-dialog h2 {
        font-size: 25px;
    }

    .phone-confirm-dialog__hint {
        flex-direction: column;
        gap: 2px;
    }

    .phone-confirm-dialog__actions {
        grid-template-columns: 1fr;
        padding: 18px 22px 24px !important;
    }

    .phone-dialog-close-button,
    .phone-dialog-resend-button,
    .phone-dialog-confirm-button {
        width: 100%;
    }
}
</style>
