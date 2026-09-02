<script setup lang="ts">
import {computed} from 'vue';

const {value, max, min = 0, striped = false, animated = false, label = false} = defineProps<{
    value: number,
    max: number,
    min: number,
    label?: boolean,
    striped?: boolean,
    animated?: boolean
}>();

const rel = computed(() => (value - min) / (max - min));

const styles = computed(() => ({
    width: rel.value * 100 + '%'
}));

const clasess = computed(() => ({
    'progress-bar-striped': striped || animated,
    'progress-bar-animated': animated
}));
</script>

<template>
    <div class="progress">
        <div
            :style="styles"
            :class="clasess"
            class="progress-bar"
            role="progressbar"
            :aria-valuenow="value"
            :aria-valuemin="min"
            :aria-valuemax="max"
        >{{ label ? rel * 100 + '%' : '' }}
        </div>
    </div>
</template>
