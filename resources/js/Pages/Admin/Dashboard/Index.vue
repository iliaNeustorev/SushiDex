<template>
    <AdminLayout>
        <AdminWrapper>
            <div>
                <h1>Админ панель</h1>
                {{ user.first_name + ' ' + user.email }}
                <span
                    v-for="role in user.roles"
                    :key="role.name"
                >
                    ({{ role.description }})
                </span>
            </div>
            <VBadge
                floating
                class="mt-3"
                location="top right"
                color="success"
                :content="countActualOrder"
            >
                <VBtn size="x-large" @click="router.visit(OrderController.actual())">Монитор заказов</VBtn>
            </VBadge>
        </AdminWrapper>
    </AdminLayout>
</template>

<script setup lang="ts">
import AdminLayout from "~vue/Layouts/AdminLayout.vue";
import AdminWrapper from "~vue/Layouts/AdminWrapper.vue";
import OrderController from "~routes/Admin/OrderController.ts";
import {router} from "@inertiajs/vue3";
import type {UserAuthResource} from "~types/generated.ts";

defineProps<{
    user: UserAuthResource;
    countActualOrder: number;
}>();
</script>
