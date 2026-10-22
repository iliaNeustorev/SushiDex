<template>
    <MainLayout>
        <VContainer>
            <VCard>
                <VCardTitle class="d-flex align-center">
                    <span>Уведомления</span>
                    <VBtn
                        color="success"
                        icon="$read"
                        :disabled="!checkUnreadNotify"
                        class="ml-2"
                        variant="tonal"
                        title="Прочитать все уведомления"
                        :loading="loadingRead"
                        @click="readAll"
                    />
                    <VSpacer />
                    <VCheckbox
                        v-model="showAllNotifications"
                        label="Показать все"
                        color="success"
                        @update:model-value="showAll"
                    />
                </VCardTitle>

                <VDivider />

                <VList
                    v-if="notifications.length > 0"
                    lines="three"
                >
                    <template
                        v-for="notification in notifications"
                        :key="notification.id"
                    >
                        <div class="d-flex align-center justify-space-between">
                            <VListItem
                                :title="notification.data.message"
                                :subtitle="formatDate(notification.created_at)"
                                :disabled="notification.read_at !== null"
                            >
                                <template #prepend>
                                    <Link
                                        :href="NotificationRoutes.update(notification.id)"
                                        method="patch"
                                        as="button"
                                        class="mr-4"
                                        :disabled="notification.read_at !== null"
                                        title="Отметить как прочитанное"
                                    >
                                        <VIcon
                                            :color="notification.read_at ? 'success' : 'primary'"
                                            :icon="notification.read_at === null ? '$bellRing' : '$bellOutline'"
                                        />
                                    </Link>
                                </template>
                            </VListItem>
                            <VSpacer />
                            <VListItem v-if="notification.data.url && notification.read_at === null">
                                <Link
                                    :href="notification.data.url"
                                    class="text-decoration-none text-green-darken-3"
                                >
                                    Перейти
                                </Link>
                            </VListItem>
                        </div>
                        <VDivider />
                    </template>
                </VList>

                <VEmptyState v-else>
                    Новых уведомлений нет
                </VEmptyState>

                <VContainer v-if="page < lastPage">
                    <VRow class="align-center justify-center">
                        <VCol cols="auto">
                            <VBtn
                                size="large"
                                color="success"
                                rounded="xl"
                                @click="nextPage"
                            >
                                Показать ещё +
                            </VBtn>
                        </VCol>
                    </VRow>
                </VContainer>
            </VCard>
        </VContainer>
    </MainLayout>
</template>

<script setup lang="ts">
import type {DatabaseNotificationResource, NotificationsQuery} from '~types/generated.ts';
import MainLayout from '~vue/Layouts/MainLayout.vue';
import {formatDate} from '~vue/shared/formatters.ts';
import NotificationRoutes from '~routes/Client/NotificationController.ts';
import {Link, router} from '@inertiajs/vue3';
import {ref, shallowRef, watch} from 'vue';

const {notifications, query = {all: false}, lastPage, page = 1, checkUnreadNotify} = defineProps<{
    query: NotificationsQuery;
    notifications: DatabaseNotificationResource[];
    page: number;
    lastPage: number;
    checkUnreadNotify: boolean;
}>();

const loadingRead = shallowRef(false);
const showAllNotifications = ref(query.all ?? false);

watch(() => query.all, (all) => {
    showAllNotifications.value = all ?? false;
});

function readAll(): void {
    loadingRead.value = true;
    router.visit(NotificationRoutes.readAll(), {
        preserveScroll: true,
        preserveState: true,
        onFinish: () => {
            loadingRead.value = false;
        },
    });
}

function nextPage(): void {
    if (page >= lastPage) {
        return;
    }
    router.visit(NotificationRoutes.index({
        query: {
            page: page + 1,
            all: showAllNotifications.value,
        },
    }), {
        preserveScroll: true,
        only: ['notifications', 'page', 'lastPage', 'checkUnreadNotify'],
    });
}

function showAll(): void {
    router.visit(NotificationRoutes.index({query: {all: showAllNotifications.value}}), {
        reset: ['notifications'],
    });
}
</script>
