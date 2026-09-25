import type {
    CartPublicResource,
    ImagePublicResource,
} from '~types/generated';

export interface GuestCartItem extends CartPublicResource {
    previewImage: ImagePublicResource | null;
}

export type CartStoreItem = CartPublicResource | GuestCartItem;

export interface CartDisplayItem {
    id: number;
    title: string;
    price: string;
    count: number;
    previewImage: ImagePublicResource | null;
}

export interface CartPayload {
    items: CartPublicResource[];
    total_price: number;
}

export interface GuestCartPayload {
    items: GuestCartItem[];
    total_price: number;
}
