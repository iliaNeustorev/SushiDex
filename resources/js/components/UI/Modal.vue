<script setup lang="ts">
import {computed, useCssModule} from 'vue';

const {open, title = ''} = defineProps<{
    open: boolean,
    title?: string
}>();

const emit = defineEmits<{
    (name: 'ok'): void,
    (name: 'close'): void
}>();

const style = useCssModule();

const modalClasses = computed(() => ({
    [style.modal]: open
}))

</script>

<template>
    <Teleport to="body">
        <Transition name="fade">
            <div :class="modalClasses" class="modal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5">{{ title }}</h1>
                            <button @click="emit('close')" type="button" class="btn-close" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <slot></slot>
                        </div>
                        <div class="modal-footer">
                            <slot name="footer">
                                <button @click="emit('ok')" type="button" class="btn btn-success">Ok</button>
                                <button @click="emit('close')" type="button" class="btn btn-danger">Cancel</button>
                            </slot>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style module>
.modal {
    background: rgba(0, 0, 0, 0.3);
    display: block !important;
}
</style>
