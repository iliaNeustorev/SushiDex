<script setup lang="ts">
import {computed} from 'vue';

const {variant = 'primary'} = defineProps<{
    variant?: 'primary' | 'secondary' | 'success'
}>();

const classes = computed(() => ({
    [`text-${variant}`]: true
}));
</script>

<template>
    <div :class="classes" class="spinner-border" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</template>
