<template>
    <VCombobox
        v-model="address"
        :items="addressSuggestions"
        :loading="addressLoading"
        :error-messages="fieldErrors"
        :return-object="false"
        autocomplete="street-address"
        no-filter
        hide-no-data
        label="Адрес доставки"
        variant="outlined"
        @update:search="searchAddress"
    />
</template>

<script setup lang="ts">
import {ref, computed, onBeforeUnmount} from 'vue';
import axios from 'axios';
import ProfileRoutes from '~routes/Client/ProfileController';

const address = defineModel<string | null>({required: true});

const props = withDefaults(defineProps<{
    errorMessages?: string | string[];
}>(), {
    errorMessages: '',
});

const addressSuggestions = ref<string[]>([]);
const addressLoading = ref(false);
const addressError = ref('');

const fieldErrors = computed(() => {
    return props.errorMessages.length ? props.errorMessages : addressError.value;
});

let searchTimer: ReturnType<typeof setTimeout> | undefined;
let searchVersion = 0;

function searchAddress(value: string): void {
    clearTimeout(searchTimer);

    const version = ++searchVersion;
    const query = value.trim();

    addressSuggestions.value = [];
    addressError.value = '';
    addressLoading.value = false;

    if (query.length < 3) {
        return;
    }

    searchTimer = setTimeout(async () => {
        addressLoading.value = true;

        try {
            const response = await axios.post<{data: string[]}>(
                ProfileRoutes.getAutoCompleteAddress.url(),
                {partAddress: query},
            );

            if (version === searchVersion) {
                addressSuggestions.value = response.data.data;
            }
        } catch {
            if (version === searchVersion) {
                addressError.value = 'Не удалось загрузить подсказки.';
            }
        } finally {
            if (version === searchVersion) {
                addressLoading.value = false;
            }
        }
    }, 350);
}

onBeforeUnmount(() => {
    clearTimeout(searchTimer);
    searchVersion++;
});
</script>
