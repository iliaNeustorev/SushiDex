import {defineStore} from 'pinia';
import type {ProductPublicResource} from '~types/generated.ts';
import type {
    CartPayload,
    CartStoreItem,
    GuestCartItem,
    GuestCartPayload,
} from '~vue/types/cart';
import axios from "axios";
import CartRoutes from "~routes/CartController.ts";
import storageHelper from "~vue/utils/storage.ts";

export const useCartStore = defineStore('cart', {
    state: () => ({
        items: [] as CartStoreItem[],
        totalAmount: 0 as number,
        updatingProductIds: [] as number[],
        authenticated: false,
    }),
    getters: {
        empty(state) {
            return state.items.length === 0
        },
        allCount(state) {
            return state.items.reduce(
                (total, item) => total + item.count,
                0,
            );
        },
        productCount: state => (id: number): number => {
            return state.items.find(item => item.id === id)?.count ?? 0;
        },
        isProductUpdating: state => (id: number): boolean => {
            return state.updatingProductIds.includes(id);
        },
    },
    actions: {
        setAuthenticated(value: boolean): void {
            this.authenticated = value;
        },
        setCart(value: CartPayload | GuestCartPayload): void {
            this.items = value.items;
            this.totalAmount = value.total_price
        },
        async updateProductCount(
            id: number,
            count: number,
            productDetails?: ProductPublicResource,
        ): Promise<void> {
            if (!this.authenticated) {
                this.updateGuestProductCount(id, count, productDetails);

                return;
            }

            const product = this.items.find(item => item.id === id);

            if (this.isProductUpdating(id)) {
                return;
            }
            const previousCount = product?.count ?? 0;
            this.updatingProductIds.push(id);

            try {
                const response = await axios.put<{ cart: CartPayload }>(
                    CartRoutes.update().url,
                    {
                        product_id: id,
                        count,
                    },
                );

                this.setCart(response.data.cart);
            } catch (error) {
                if (product && previousCount != 0) {
                    product.count = previousCount;
                }
                throw error;
            } finally {
                this.updatingProductIds = this.updatingProductIds.filter(
                    productId => productId !== id,
                );
            }
        },
        updateGuestProductCount(
            id: number,
            count: number,
            productDetails?: ProductPublicResource,
        ): void {
            const product = this.items.find(item => item.id === id);

            if (count === 0) {
                this.items = this.items.filter(item => item.id !== id);
            } else if (product) {
                product.count = count;
            } else if (productDetails) {
                this.items.push({
                    id,
                    price: productDetails.price,
                    title: productDetails.title,
                    count,
                    previewImage: productDetails.images[0] ?? null,
                });
            }

            this.totalAmount = Number(this.items.reduce(
                (total, item) => total + Number(item.price) * item.count,
                0,
            ).toFixed(2));

            const items: GuestCartItem[] = this.items.map(item => ({
                id: item.id,
                title: item.title,
                price: item.price,
                count: item.count,
                previewImage: 'previewImage' in item
                    ? item.previewImage
                    : null,
            }));

            storageHelper.setCart({
                items,
                total_price: this.totalAmount,
            });
        },
        clearGuestCart(): void {
            this.items = [];
            this.totalAmount = 0;
            storageHelper.removeCart();
        }
    },
});
