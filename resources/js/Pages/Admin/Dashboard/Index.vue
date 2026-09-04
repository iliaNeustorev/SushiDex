<template>
    <AdminLayout>
        <AdminWrapper>
            <div>
                <h1>Админ панель</h1>
                {{ user.first_name + ' ' + user.email }}
                <span v-for="(name, description) in user.role" :key="name">({{ description }})</span>
            </div>
        </AdminWrapper>
    </AdminLayout>
</template>

<script setup lang="ts">
import AdminLayout from "~vue/Layouts/AdminLayout.vue";
import AdminWrapper from "~vue/Layouts/AdminWrapper.vue";

defineProps({
    user: {type: Object, required: true}
})
</script>
