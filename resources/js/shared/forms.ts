import type {OrderSaveReqDTO, OrderUpdateReqDTO, PostsSaveReqDTO, ProductsSaveReqDTO} from "~types/generated";

export type PostForm =
    Omit<PostsSaveReqDTO, 'category_id'> & {
    category_id: PostsSaveReqDTO['category_id'] | null
}

export type ProductForm =
    Omit<ProductsSaveReqDTO, 'category_id'> & {
    category_id: ProductsSaveReqDTO['category_id'] | null
}

export type CreateOrderForm =
    Omit<OrderSaveReqDTO, 'type'> & {
    type: OrderSaveReqDTO['type'] | 0
}

export type UpdateAdminOrderForm =
    Omit<OrderUpdateReqDTO, 'status'> & {
    status: OrderUpdateReqDTO['status'] | null
}
