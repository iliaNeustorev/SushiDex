<template>
    <VSnackbar
        :key="notificationId"
        v-model="visible"
        :color="color"
        location="top end"
        :timeout="5000"
        variant="elevated"
        timer="top"
    >
        <div class="d-flex align-center">
            <VIcon
                :icon="icon"
                class="me-3"
            />

            <div>
                <div class="font-weight-bold">
                    {{ title }}
                </div>

                <div>
                    {{ text }}
                </div>
            </div>
        </div>
    </VSnackbar>
</template>

<script setup lang="ts">
import {onUnmounted, ref} from 'vue';
import {router} from '@inertiajs/vue3';
import type {NotificationType} from '~vue/types/inertia';

const notificationSettings: Record<
    NotificationType,
    { title: string; icon: string; color: string }
> = {
    success: {
        title: 'Успешно',
        icon: '$mdiCheckOutline',
        color: 'success',
    },
    warning: {
        title: 'Предупреждение',
        icon: '$mdiAlertOutline',
        color: 'warning',
    },
    info: {
        title: 'Информация',
        icon: '$mdiInformationOutline',
        color: 'info',
    },
    error: {
        title: 'Ошибка',
        icon: '$mdiCloseOutline',
        color: 'error',
    },
    danger: {
        title: 'Ошибка',
        icon: '$mdiCloseOutline',
        color: 'error',
    },
    primary: {
        title: 'Информация',
        icon: '$mdiInformationOutline',
        color: 'primary',
    },
};

const visible = ref(false);
const notificationId = ref(0);
const text = ref('');
const title = ref('');
const color = ref('info');
const icon = ref('');

onUnmounted(
    router.on('flash', (event) => {
        const flash = event.detail.flash;

        if (!flash.type || !flash.text) {
            return;
        }

        const settings = notificationSettings[flash.type];

        notificationId.value++;
        text.value = flash.text;
        title.value = settings.title;
        color.value = settings.color;
        icon.value = settings.icon;
        visible.value = true;
    }),
);
</script>
