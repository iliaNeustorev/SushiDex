<template>
    <Head title="Меню — SushiDex"/>
    <MainLayout>
        <main class="inner-page menu-page">
            <div class="shell">
                <header class="page-intro">
                    <p class="eyebrow dark"><span></span> Меню SushiDex</p>
                    <h1>Выберите свой вкус</h1>
                    <p>Роллы, суши и тёплые блюда готовим только после заказа. Выберите то, что хочется попробовать
                        сегодня.</p>
                </header>
                <nav class="category-list">
                    <button
                        v-for="category in categories"
                        :key="category.url"
                        type="button"
                        class="category-tab"
                        :class="{ active: category.url === selectedCategoryUrl }"
                        :aria-current="category.url === selectedCategoryUrl ? 'page' : undefined"
                        @click="selectCategory(category.url)"
                    >
                        {{ category.title }}
                    </button>
                </nav>
                <section class="catalog-section">
                    <div class="catalog-heading">
                        <div>
                            <p class="catalog-kicker">Каталог</p>
                            <h2>{{ activeCategory?.title ?? 'Меню' }}</h2>
                        </div>
                        <span class="catalog-count">{{ formatItemsCount(countProducts) }}</span>
                    </div>
                    <div class="menu-search">
                        <VTextField
                            :model-value="queryLocal.filter.title"
                            @update:model-value="onTitleUpd"
                            label="Поиск продукта"
                            variant="outlined"
                            density="comfortable"
                            color="#df5f45"
                            base-color="#bcae9f"
                            hide-details
                            clearable
                        />
                    </div>
                    <div v-if="products.data.length > 0" class="product-grid">
                        <article v-for="product in products.data" :key="product.id" class="product-card">
                            <CardProduct :product="product"/>
                        </article>
                    </div>
                    <div v-else class="empty-state">
                        <span class="empty-state-mark" aria-hidden="true">よ</span>
                        <h3>Ничего не найдено</h3>
                        <p>Попробуйте другое название или выберите категорию.</p>
                    </div>
                </section>
                <div v-if="products.lastPage > 1" class="menu-pagination">
                    <VPagination
                        v-model="queryLocal.page"
                        color="#171716"
                        active-color="#df5f45"
                        :length="products.lastPage"
                        next-icon="$menuRight"
                        prev-icon="$menuLeft"
                    />
                </div>
            </div>
        </main>
    </MainLayout>
</template>
<script setup lang="ts">
import {Head, router} from '@inertiajs/vue3';
import MainLayout from '~vue/Layouts/MainLayout.vue';
import type {CategoryPublicResource, ProductPublicResource, ProductsClientQuery} from "~types/generated.ts";
import type {TypedPagination} from "~vue/shared/pagination.ts";
import {computed, reactive, watch} from "vue";
import {formatItemsCount} from "~vue/shared/formatters.ts";
import type {RequiredKeys} from "~vue/shared/objects.ts";
import {debounce, merge} from "lodash-es";
import MenuRoutes from "~routes/GeneralController.ts";
import CardProduct from "~vue/components/products/CardProduct.vue";

const {query = {}, categories, products, selectedCategoryUrl} = defineProps<{
    categories: CategoryPublicResource[],
    products: TypedPagination<ProductPublicResource>,
    selectedCategoryUrl: string | null,
    query: ProductsClientQuery
}>()
const activeCategory = computed(() => categories.find(v => v.url === selectedCategoryUrl))
const countProducts = computed(() => products.total)

const queryDefaults: RequiredKeys<ProductsClientQuery, 'filter'> = {
    filter: {}
}
const queryLocal = reactive(merge({}, queryDefaults, {page: products.page}, query));

const onTitleUpd = debounce((v: string | null) => {
    queryLocal.page = 1;
    queryLocal.filter.title = v || undefined;
}, 900);

watch(queryLocal, applyReload);

function applyReload() {
    router.visit(MenuRoutes.menu({
        query: queryLocal
    }), {
        preserveScroll: true
    });
}

function selectCategory(categoryUrl: string) {
    if (queryLocal.url === categoryUrl || (!queryLocal.url && selectedCategoryUrl === categoryUrl)) {
        return;
    }

    queryLocal.page = 1;
    queryLocal.url = categoryUrl;
}
</script>
<style scoped>
.category-list {
    display: flex;
    gap: 12px;
    margin-bottom: 64px;
    overflow-x: auto;
    padding-bottom: 12px;
    scrollbar-width: thin;
}

.category-tab {
    min-width: max-content;
    padding: 13px 21px;
    border: 1px solid #d9cec2;
    background: transparent;
    color: #756b62;
    font-family: inherit;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    transition: border-color .2s, background-color .2s, color .2s;
}

.category-tab:hover {
    border-color: #171716;
    color: #171716;
}

.category-tab:focus-visible {
    outline: 2px solid #df5f45;
    outline-offset: 3px;
}

.category-tab.active {
    border-color: #171716;
    background: #171716;
    color: #fff;
}

.catalog-heading {
    display: flex;
    align-items: end;
    justify-content: space-between;
    gap: 20px;
    padding-bottom: 22px;
    border-bottom: 1px solid #d9cec2;
}

.catalog-kicker {
    margin: 0 0 8px;
    color: #b16a56;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .16em;
    text-transform: uppercase;
}

.catalog-heading h2 {
    margin: 0;
    font-family: 'Prata', serif;
    font-size: clamp(30px, 4vw, 42px);
    font-weight: 400;
    line-height: 1.2;
}

.catalog-count {
    color: #8b8076;
    font-size: 12px;
    white-space: nowrap;
}

.menu-search {
    width: min(100%, 420px);
    margin: 28px 0 6px;
}

.menu-search :deep(.v-field) {
    background: #fffaf4;
    border-radius: 0;
    font-family: 'Manrope', sans-serif;
}

.menu-search :deep(.v-label) {
    color: #756b62;
}

.product-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 22px;
    margin-top: 28px;
}

.product-card {
    display: grid;
    grid-template-columns: minmax(190px, 42%) minmax(0, 1fr);
    min-height: 240px;
    overflow: hidden;
    border: 1px solid #e7ded3;
    background: #fffaf4;
    transition: transform .25s, box-shadow .25s;
}

.product-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 45px rgba(51, 38, 27, .09);
}

.empty-state {
    display: grid;
    justify-items: center;
    min-height: 280px;
    margin-top: 28px;
    padding: 48px 24px;
    border: 1px solid #e7ded3;
    background: #fffaf4;
    text-align: center;
}

.empty-state-mark {
    color: #df5f45;
    font-family: 'Prata', serif;
    font-size: 42px;
}

.empty-state h3 {
    margin: 12px 0 0;
    font-family: 'Prata', serif;
    font-size: 25px;
    font-weight: 400;
}

.empty-state p {
    margin: 8px 0 0;
    color: #81766c;
    font-size: 13px;
}

.menu-pagination {
    display: flex;
    justify-content: center;
    margin-top: 52px;
}

.menu-pagination :deep(.v-pagination__item .v-btn) {
    font-family: 'Manrope', sans-serif;
    font-weight: 700;
}

@media (max-width: 1000px) {
    .product-grid {
        grid-template-columns: 1fr;
    }

    .product-card {
        grid-template-columns: minmax(210px, 38%) minmax(0, 1fr);
    }
}

@media (max-width: 560px) {
    .category-list {
        gap: 8px;
        margin-bottom: 44px;
    }

    .category-tab {
        padding: 11px 15px;
    }

    .catalog-heading {
        align-items: start;
        flex-direction: column;
        gap: 8px;
    }

    .menu-search {
        width: 100%;
    }

    .product-card {
        grid-template-columns: 1fr;
    }

    .menu-pagination {
        margin-top: 38px;
    }
}
</style>
