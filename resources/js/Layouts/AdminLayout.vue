<template>
    <VApp>
        <VAppBar app height="54">
            <VContainer fluid>
                <VToolbarTitle>
                    <VIcon icon="$castEducation" class="me-2"></VIcon>
                    SushiDex
                </VToolbarTitle>
            </VContainer>
        </VAppBar>
        <VNavigationDrawer permanent>
            <div class="d-flex flex-column fill-height">
                <VList>
                    <VListSubheader>Админ панель</VListSubheader>
                    <VListItem v-for="(item,index) in mainMenu" :key="item.to.url" color="primary" rounded="xl">
                        <template #prepend>
                            <VIcon :icon="item.icon"/>
                        </template>
                        <VListItemTitle>
                            <Link :href="item.to.url" class="text-decoration-none text-h6"
                                  :class="[page.url.startsWith(item.to.url) && index != 0 ? 'text-yellow-darken-3 font-weight-bold' : 'text-grey-darken-4']">
                                {{
                                    item.title
                                }}
                            </Link>
                        </VListItemTitle>
                    </VListItem>
                </VList>
                <VDialog
                    v-model="closeModalLogout"
                    max-width="600"
                    persistent
                >
                    <template v-slot:activator="{ props: closeModalLogout }">
                        <VBtn class="mt-auto text-h6 mb-6" v-bind="closeModalLogout">
                            Выйти из аккаунта
                        </VBtn>
                    </template>

                    <VCard
                        prepend-icon="mdi-map-marker"
                        title="Вы действительно хотите выйти?"
                        class="text-center"
                    >
                        <template v-slot:actions>
                            <VSpacer></VSpacer>
                            <VBtn @click="closeModalLogout = false">
                                Отмена
                            </VBtn>

                            <VBtn @click="logout">
                                ОК
                            </VBtn>
                        </template>
                    </VCard>
                </VDialog>
            </div>
        </VNavigationDrawer>
        <VMain class="appMain">
            <div class="ps-4 pe-4">
                <slot/>
            </div>
        </VMain>
        <VFooter app class="flex-grow-0">
            <VContainer fluid class="py-1">
                Админ панель &copy;
            </VContainer>
        </VFooter>
    </VApp>
</template>

<script setup lang="ts">
import {Link, usePage, useForm} from '@inertiajs/vue3';
import {computed, ref} from 'vue';
import type {UserAuthResource} from "~types/generated";
import PostsRoutes from "~routes/Admin/PostController.ts";
import GeneralController from "~routes/GeneralController.ts";
import ProductRoutes from "~routes/Admin/ProductController.ts";
import SessionRoutes from "~routes/Auth/SessionController.ts";
import CategoryRoutes from "~routes/Admin/CategoryController.ts";

const {props} = usePage<{ user: UserAuthResource | null }>();
const page = usePage();
const mainMenuBase = [
    {to: GeneralController.index(), title: 'Главная', icon: '', guard: 'admin'},
    {to: PostsRoutes.index(), title: 'Посты', icon: '', guard: 'admin'},
    {to: ProductRoutes.index(), title: 'Продукты', icon: '', guard: 'admin'},
    {to: CategoryRoutes.index(), title: 'Категории', icon: '', guard: 'admin'}
] as const;

const mainMenu = computed(() => mainMenuBase.filter(item =>
    (item.guard === 'admin' && props.user)
))

let closeModalLogout = ref<boolean>(false);

const logoutForm = useForm({});

function logout() {
    logoutForm.submit(SessionRoutes.logout(), {
        onFinish() {
            closeModalLogout.value = false
        }
    })
}
</script>
