<template>
    <section class="phone-settings">
        <div v-if="phones.length > 0" class="pending-phone-info">
            <span class="phone-settings__label">Неподтверждённые телефоны</span>
            <div class="pending-phone-list">
                <div v-for="pendingPhone in phones" :key="pendingPhone.id" class="pending-phone">
                    <div class="pending-phone__main">
                        <strong>{{ pendingPhone.phone }}</strong>
                        <VBtn
                            :disabled="sendCodeForm.processing"
                            :loading="sendCodeForm.processing && sendCodeForm.id === pendingPhone.id"
                            density="compact"
                            icon="$phoneCheckOutline"
                            title="Подтвердить номер"
                            type="button"
                            variant="text"
                            @click="sendCode(pendingPhone.id)"
                        />
                        <VBtn
                            :disabled="deletingPhoneId !== null"
                            :loading="deletingPhoneId === pendingPhone.id"
                            density="compact"
                            icon="$deleteOutline"
                            title="Удалить номер"
                            type="button"
                            variant="text"
                            @click="deletePhone(pendingPhone.id)"
                        />
                    </div>
                    <VAlert
                        v-if="sendCodeForm.errors.id && sendCodeForm.id === pendingPhone.id"
                        class="pending-phone__error"
                        density="compact"
                        type="error"
                        variant="tonal"
                    >
                        {{ sendCodeForm.errors.id }}
                    </VAlert>
                </div>
            </div>
        </div>
        <VBtn
            class="add-phone-button"
            type="button"
            @click="addPhoneDialog = true"
        >
            Добавить телефон
        </VBtn>
    </section>
    <VDialog
        v-model="confirmPhoneDialog"
        max-width="520"
        @update:model-value="updateConfirmDialog"
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
                    @click="updateConfirmDialog(false)"
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
    <VDialog
        :model-value="addPhoneDialog"
        max-width="520"
        @update:model-value="updateAddPhoneDialog"
    >
        <VCard class="phone-confirm-dialog" elevation="0">
            <div class="phone-confirm-dialog__accent"></div>

            <VCardText class="phone-confirm-dialog__content">
                <span class="phone-confirm-dialog__eyebrow">Добавить новый телефон</span>
                <p>
                    После добавления новый номер нужно будет подтвердить
                </p>

                <VTextField
                    v-model="addPhoneForm.phone"
                    :error-messages="addPhoneForm.errors.phone"
                    hide-details="auto"
                    label="Введите номер"
                    placeholder="79991234567"
                    variant="outlined"
                    clearable
                ></VTextField>
            </VCardText>

            <VCardActions class="phone-confirm-dialog__actions">
                <VBtn
                    class="phone-dialog-close-button"
                    type="button"
                    variant="text"
                    @click="updateAddPhoneDialog(false)"
                >
                    Закрыть
                </VBtn>
                <VBtn
                    :disabled="addPhoneForm.processing || addPhoneForm.phone === ''"
                    :loading="addPhoneForm.processing"
                    class="phone-dialog-confirm-button"
                    type="button"
                    @click="sendNewPhone"
                >
                    Подтвердить
                </VBtn>
            </VCardActions>
        </VCard>
    </VDialog>
    <VDialog
        :model-value="deletePhoneDialog"
        max-width="520"
        @update:model-value="updatePhoneDeleteDialog"
    >
        <VCard class="phone-confirm-dialog" elevation="0">
            <div class="phone-confirm-dialog__accent"></div>

            <VCardText class="phone-confirm-dialog__content">
                <span class="phone-confirm-dialog__eyebrow">Вы действительно хотите удалить номер?</span>
            </VCardText>

            <VCardActions class="phone-confirm-dialog__actions">
                <VBtn
                    class="phone-dialog-close-button"
                    type="button"
                    variant="text"
                    @click="updatePhoneDeleteDialog(false)"
                >
                    Закрыть
                </VBtn>
                <VBtn
                    :disabled="deletingPhoneId === null"
                    class="phone-dialog-confirm-button"
                    type="button"
                    @click="confirmDeletePhone"
                >
                    Подтвердить
                </VBtn>
            </VCardActions>
        </VCard>
    </VDialog>
</template>
<script setup lang="ts">
import type {AddPhoneReqDTO, ConfirmCodeReqDTO, PendingPhoneProfileResource, SendCodeReqDTO} from "~types/generated.ts";
import PhoneRoutes from "~routes/Client/PhoneController.ts";
import {router, useForm} from "@inertiajs/vue3";
import {ref} from "vue";

const {} = defineProps<{
    phones: PendingPhoneProfileResource[]
}>();

const sendCodeForm = useForm<SendCodeReqDTO>({
    id: 0
})
const confirmCodeForm = useForm<ConfirmCodeReqDTO>({
    id: 0,
    code: ''
})
const addPhoneForm = useForm<AddPhoneReqDTO>({
    phone: ''
})

const confirmPhoneDialog = ref<boolean>(false);
const addPhoneDialog = ref<boolean>(false);
const deletePhoneDialog = ref<boolean>(false);

const deletingPhoneId = ref<number | null>(null)

function sendCode(id: number) {
    sendCodeForm.clearErrors();
    confirmCodeForm.clearErrors();
    sendCodeForm.id = id
    confirmCodeForm.id = id;
    sendCodeForm.submit(PhoneRoutes.sendCode(), {
        preserveScroll: true,
        preserveState: true,
        only: ['client'],
        onSuccess: () => {
            confirmCodeForm.code = '';
            confirmPhoneDialog.value = true;
        },
    });
}

function confirmCode() {
    confirmCodeForm.submit(PhoneRoutes.confirmCode(), {
        preserveScroll: true,
        preserveState: true,
        only: ['client'],
        onSuccess: (() => updateConfirmDialog(false)),
    })
}

function sendNewPhone() {
    addPhoneForm.clearErrors();
    addPhoneForm.phone = addPhoneForm.phone.replace(/\D/g, '').replace(/^8/, '7')
    addPhoneForm.submit(PhoneRoutes.store(), {
            preserveScroll: true,
            preserveState: true,
            only: ['client'],
            onSuccess: (() => updateAddPhoneDialog(false)),
        }
    )
}

function updateConfirmDialog(open: boolean): void {
    confirmPhoneDialog.value = open;
    if (!open) {
        confirmCodeForm.resetAndClearErrors();
        sendCodeForm.resetAndClearErrors();
    }
}

function updateAddPhoneDialog(open: boolean): void {
    addPhoneDialog.value = open;

    if (!open) {
        addPhoneForm.resetAndClearErrors();
    }
}

function updatePhoneDeleteDialog(open: boolean): void {
    deletePhoneDialog.value = open;

    if (!open) {
        deletingPhoneId.value = null;
    }
}

function deletePhone(id: number) {
    deletingPhoneId.value = id
    deletePhoneDialog.value = true
}

function confirmDeletePhone() {
    if (deletingPhoneId.value != null) {
        router.delete(PhoneRoutes.destroy.url(deletingPhoneId.value), {
            preserveScroll: true,
            onSuccess: () => updatePhoneDeleteDialog(false),
        });
    }
}
</script>

<style scoped>
.phone-settings {
    display: flex;
    align-items: end;
    justify-content: space-between;
    gap: 24px;
    padding: 20px 0;
    border-bottom: 1px solid var(--profile-border);
}

.pending-phone-info {
    min-width: 0;
    flex: 1;
}

.phone-settings__label {
    color: var(--profile-muted);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .14em;
    text-transform: uppercase;
}

.pending-phone-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-top: 7px;
}

.pending-phone {
    display: flex;
    align-items: center;
    gap: 16px;
    color: var(--profile-text);
    font-size: 15px;
}

.pending-phone__main {
    display: flex;
    flex: 0 0 auto;
    align-items: center;
    gap: 4px;
}

.pending-phone strong {
    overflow-wrap: anywhere;
    font-weight: 600;
}

.pending-phone__error {
    min-width: 0;
    font-size: 13px;
}

.add-phone-button {
    flex: 0 0 auto;
    min-width: 176px;
    border-radius: 0;
    background: var(--profile-accent);
    color: #fff;
    font-size: 13px;
    letter-spacing: .04em;
    text-transform: none;
}

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

.phone-confirm-dialog__error {
    margin-bottom: 18px;
    border-radius: 0;
    font-size: 13px;
}

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
    font-size: 11px;
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
    font-size: 14px;
    line-height: 1.65;
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
    font-size: 12px;
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
    font-size: 12px;
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

@media (max-width: 620px) {
    .phone-settings {
        align-items: stretch;
        flex-direction: column;
        gap: 16px;
    }

    .add-phone-button {
        width: 100%;
    }

    .pending-phone {
        align-items: flex-start;
        flex-direction: column;
        gap: 4px;
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
