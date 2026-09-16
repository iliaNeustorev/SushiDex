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
            <p>Почта и телефон подтверждаются отдельно и не изменяются этой формой.</p>
        </div>
        <PhoneSettings :phones="client.pendingPhones"/>
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
    </section>
</template>

<script setup lang="ts">
import {useForm} from '@inertiajs/vue3';
import type {ProfileSaveReqDTO, UserProfileResource} from '~types/generated';
import ProfileRoutes from '~routes/Client/ProfileController';
import PhoneSettings from "~vue/Pages/Profile/Components/PhoneSettings.vue";

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


function reset(): void {
    form.reset();
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
    font-size: 15px;
    font-weight: 600;
}

.account-info p {
    grid-column: 1 / -1;
    margin: 0;
    padding: 14px 0;
    border-top: 1px solid var(--profile-border);
    color: var(--profile-caption);
    font-size: 13px;
    line-height: 1.6;
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
    border-radius: 0;
    font-size: 13px;
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
}
</style>
