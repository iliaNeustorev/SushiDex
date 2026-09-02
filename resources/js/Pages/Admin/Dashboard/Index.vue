<script setup lang="ts">
import {useForm} from '@inertiajs/vue3'
import {ref} from 'vue';
import SessionRoutes from "~routes/Auth/SessionController.ts";
import DefaultLayout from "~vue/Layouts/DefaultLayout.vue";
import AdminWrapper from "~vue/Layouts/AdminWrapper.vue";
import Modal from "~vue/components/UI/Modal.vue";

defineProps({
    user: {type: Object, required: true}
})

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

<template>
    <DefaultLayout>
        <AdminWrapper>
            <h1>Админ панель</h1>
            {{ user.first_name + ' ' + user.email }}
            <span v-for="(name, description) in user.role" :key="name">({{ description }})</span>
            <hr>
            <button @click="closeModalLogout = true" class="btn btn-danger">
                Logout
            </button>
            <Modal
                :open="closeModalLogout"
                @close="closeModalLogout = false"
            >
                <template #default>
                    <h2>Выйти</h2>
                </template>
                <template #footer>
                    <button :disabled="logoutForm.processing" @click="logout" type="button" class="btn btn-success">Ok
                    </button>
                    <button :disabled="logoutForm.processing" @click="closeModalLogout = false" type="button"
                            class="btn btn-danger">Cancel
                    </button>
                </template>
            </Modal>
        </AdminWrapper>
    </DefaultLayout>
</template>
