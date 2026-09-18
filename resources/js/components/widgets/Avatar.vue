<template>
    <VAvatar
        class="avatar-preview"
        color="#df5f45"
        :size="size"
        :style="{fontSize: `${fontSize}px`}"
    >
        <VImg
            v-if="user.image"
            :src="user.image.url"
            :alt="`Аватар ${user.first_name}`"
            cover
        />
        <span v-else>{{ user.first_name.charAt(0).toUpperCase() || '?' }}</span>
    </VAvatar>
</template>

<script setup lang="ts">
import type {UserAuthResource, UserProfileResource} from "~types/generated.ts";

const {user, size, fontSize} = defineProps<{
    user: UserAuthResource | UserProfileResource,
    size: number,
    fontSize: number
}>()

</script>

<style scoped>
.avatar-preview {
    flex: none;
    color: #fff;
    font-family: 'Prata', serif;
}

.avatar-preview :deep(.v-img) {
    width: 100%;
    height: 100%;
}
</style>
