<script setup lang="ts">
import {Link, usePage} from '@inertiajs/vue3';
import {computed} from 'vue';
import AuthSession from '~gen/wayfinder/actions/App/Http/Controllers/Auth/SessionController';
import AdminDashboard from '~gen/wayfinder/actions/App/Http/Controllers/Admin/DashboardController';
import Posts from '~gen/wayfinder/actions/App/Http/Controllers/PostController';
import type {UserAuthResource} from "~types/generated";

const {props} = usePage<{ user: UserAuthResource | null }>();

const mainMenuBase = [
    // {to: General.home(), title: 'Home', icon: '', guard: null},
    {to: Posts.index(), title: 'Blog', icon: '$newspaper', guard: null},
    {to: AuthSession.create(), title: 'Login', icon: '', guard: 'guest'},
    {to: AdminDashboard.index(), title: 'Admin', icon: '', guard: 'admin'}
] as const;

const mainMenu = computed(() => mainMenuBase.filter(item =>
    item.guard === null ||
    (item.guard === 'guest' && !props.user) ||
    (item.guard === 'admin' && props.user)
))
</script>

<template>
    <VApp>
        <VAppBar app height="54">
            <VContainer fluid>
                <VToolbarTitle>
                    <VIcon icon="$castEducation" class="me-2"></VIcon>
                    СушиДекс
                </VToolbarTitle>
            </VContainer>
        </VAppBar>
        <VNavigationDrawer permanent>
            <VList>
                <VListSubheader>Главное меню</VListSubheader>
                <VListItem v-for="item in mainMenu" :key="item.to.url" color="primary" rounded="xl">
                    <template #prepend>
                        <VIcon :icon="item.icon"/>
                    </template>
                    <VListItemTitle>
                        <Link :href="item.to.url" class="text-decoration-none text-grey-darken-4">{{
                                item.title
                            }}
                        </Link>
                    </VListItemTitle>
                </VListItem>
            </VList>
        </VNavigationDrawer>
        <VMain class="appMain">
            <div class="ps-4 pe-4">
                <slot/>
            </div>
        </VMain>
        <VFooter app class="flex-grow-0">
            <VContainer fluid class="py-1">
                Some site &copy;
            </VContainer>
        </VFooter>
    </VApp>
</template>
