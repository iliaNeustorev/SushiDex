import type {GuestCartPayload} from '~vue/types/cart';

const emptyCart = (): GuestCartPayload => ({
    items: [],
    total_price: 0,
});

const storageHelper = {
    getCart(): GuestCartPayload {
        if (typeof window === 'undefined') {
            return emptyCart();
        }

        const value = localStorage.getItem('cart');

        if (!value) {
            return emptyCart();
        }

        return JSON.parse(value) as GuestCartPayload;
    },

    setCart(cart: GuestCartPayload): void {
        localStorage.setItem('cart', JSON.stringify(cart));
    },

    removeCart(): void {
        localStorage.removeItem('cart');
    },
};

export default storageHelper;
