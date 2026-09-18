<template>
    <Head title="Личный кабинет — SushiDex"/>
    <MainLayout>
        <main class="inner-page profile-page">
            <div class="shell">
                <header class="profile-header">
                    <Avatar :user="client" :size="132" :font-size="52"/>
                    <div>
                        <p class="eyebrow dark"><span></span> Личный кабинет</p>
                        <h1>{{ fullName }}</h1>
                        <p>Добрый вечер! Здесь собрана информация о ваших заказах и бонусах.</p>
                    </div>
                </header>

                <div class="profile-grid">
                    <VTabs
                        v-model="activeTab"
                        class="profile-nav"
                        :direction="mdAndUp ? 'vertical' : 'horizontal'"
                        :show-arrows="!mdAndUp"
                        color="#df5f45"
                        mandatory="force"
                    >
                        <VTab
                            v-for="tab in tabs"
                            :key="tab.key"
                            :value="tab.key"
                            :ripple="false"
                        >
                            <span class="profile-tab-label">
                                <i>{{ tab.number }}</i>
                                {{ tab.title }}
                            </span>
                        </VTab>
                    </VTabs>

                    <VTabsWindow v-model="activeTab" class="profile-tab-content">
                        <VTabsWindowItem value="overview">
                            <ProfileOverviewTab :client="client" :orders="orders"/>
                        </VTabsWindowItem>
                        <VTabsWindowItem value="orders">
                            <ProfileOrdersTab :orders="orders"/>
                        </VTabsWindowItem>
                        <VTabsWindowItem value="settings">
                            <ProfileSettingsTab :client="client"/>
                        </VTabsWindowItem>
                    </VTabsWindow>
                </div>
            </div>
        </main>
    </MainLayout>
</template>

<script setup lang="ts">
import {Head} from '@inertiajs/vue3';
import {computed, ref} from 'vue';
import {useDisplay} from 'vuetify';
import type {OrderPublicResource, UserProfileResource} from '~types/generated';
import MainLayout from '~vue/Layouts/MainLayout.vue';
import ProfileOrdersTab from './Tabs/ProfileOrdersTab.vue';
import ProfileOverviewTab from './Tabs/ProfileOverviewTab.vue';
import ProfileSettingsTab from './Tabs/ProfileSettingsTab.vue';
import Avatar from "~vue/components/widgets/Avatar.vue";

type ProfileTab = 'overview' | 'orders' | 'settings';

const {client, orders} = defineProps<{
    client: UserProfileResource,
    orders: OrderPublicResource[]
}>();

const {mdAndUp} = useDisplay();

const tabs: Array<{ key: ProfileTab; number: string; title: string }> = [
    {key: 'overview', number: '01', title: 'Обзор'},
    {key: 'orders', number: '02', title: 'Мои заказы'},
    {key: 'settings', number: '03', title: 'Настройки'},
];

const activeTab = ref<ProfileTab>('overview');
const fullName = computed(() =>
    [client.last_name, client.first_name, client.middle_name]
        .filter(Boolean)
        .join(' '),
);
</script>

<style scoped>
.profile-header {
    display: flex;
    align-items: center;
    gap: 28px;
    margin-bottom: 58px;
}

.profile-avatar {
    flex: 0 0 92px;
    width: 92px;
    height: 92px;
    display: grid;
    place-items: center;
    border-radius: 50%;
    background: #df5f45;
    color: #fff;
    font-family: 'Prata', serif;
    font-size: 38px;
}

.profile-header .eyebrow {
    margin-bottom: 12px;
}

.profile-header h1 {
    margin: 0;
    font-family: 'Prata', serif;
    font-size: clamp(38px, 5vw, 58px);
    font-weight: 400;
    line-height: 1.1;
}

.profile-header > div > p:last-child {
    margin: 13px 0 0;
    color: #81766c;
    font-size: 14px;
}

.profile-grid {
    display: grid;
    grid-template-columns: 240px minmax(0, 1fr);
    gap: 62px;
}

.profile-nav {
    align-self: start;
    width: 100%;
    height: auto;
    border-top: 1px solid #d9cec2;
}

.profile-nav :deep(.v-slide-group__content) {
    width: 100%;
}

.profile-nav :deep(.v-tab) {
    justify-content: flex-start;
    min-width: 0;
    height: auto;
    min-height: 49px;
    display: flex;
    width: 100%;
    padding: 18px 4px;
    border-bottom: 1px solid #d9cec2;
    color: #7d736a;
    font-family: inherit;
    font-size: 13px;
    font-weight: 600;
    letter-spacing: normal;
    text-transform: none;
    transition: color .2s ease;
}

.profile-nav :deep(.v-tab:hover),
.profile-nav :deep(.v-tab:focus-visible),
.profile-nav :deep(.v-tab--selected) {
    color: #df5f45;
}

.profile-nav :deep(.v-tab__slider) {
    display: none;
}

.profile-tab-label {
    display: flex;
    align-items: center;
    gap: 17px;
}

.profile-tab-label i {
    color: #ada096;
    font-size: 9px;
    font-style: normal;
}

.profile-tab-content {
    min-width: 0;
}

@media (max-width: 900px) {
    .profile-grid {
        grid-template-columns: 1fr;
        gap: 35px;
    }

    .profile-nav {
        border-top: 0;
        border-left: 1px solid #d9cec2;
    }

    .profile-nav :deep(.v-slide-group__content) {
        width: auto;
    }

    .profile-nav :deep(.v-tab) {
        min-width: max-content;
        padding: 14px 20px;
        border-top: 1px solid #d9cec2;
        border-right: 1px solid #d9cec2;
    }
}

@media (max-width: 560px) {
    .profile-header {
        align-items: flex-start;
        flex-direction: column;
    }
}
</style>
