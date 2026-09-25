export type AddPhoneReqDTO = {
phone: string;
};
export type CartPublicDetailsResource = {
id: number;
title: string;
price: string;
description: string | null;
content: string | null;
category: CategoryPublicResource;
previewImage: ImagePublicResource | null;
old_price: string | null;
count: number;
};
export type CartPublicResource = {
id: number;
count: number;
price: string;
title: string;
};
export type CartSaveReqDTO = {
product_id: number;
count: number;
};
export type CategoriesQuery = {
filter?: CategoriesQueryFilters;
sort?: string;
page?: number;
batch?: number;
};
export type CategoriesQueryFilters = {
title?: string;
url?: string;
type?: Type;
date_from?: string;
date_to?: string;
};
export type CategoriesSaveReqDTO = {
url: string;
title: string;
type: Type;
parent_id: number | null;
};
export type CategoriesTrashFiltersQuery = {
title?: string;
date_from?: string;
date_to?: string;
};
export type CategoriesTrashQuery = {
filter?: CategoriesTrashFiltersQuery;
sort?: string;
page?: number;
batch?: number;
};
export type CategoryCrudResource = {
id: number;
url: string;
title: string;
created_at: string;
type: Type;
parent_id: number | null;
};
export type CategoryPublicResource = {
url: string;
title: string;
type: Type;
parent_id: number | null;
};
export type ChangeAvatarReqDTO = {
item: string;
image: any;
};
export type ChangePasswordReqDTO = {
current_password: string;
password: string;
password_confirmation: string;
};
export type ConfirmCodeReqDTO = {
code: string;
id: number;
};
export type GeneralPagination = {
page: number;
total: number;
lastPage: number;
perPage: number;
data: Array<any>;
};
export type ImageCrudResource = {
id: number;
path: string;
created_at: string;
};
export type ImagePublicResource = {
id: number;
url: string;
};
export type ImagesUploadReqDTO = {
item: string;
id: number;
images: File[];
};
export type LoginReqDTO = {
email: string;
password: string;
remember: boolean;
};
export type OrderPublicResource = {
status_text: string;
type_paid_text: string;
id: number;
total_price: string;
status: OrderStatus;
type_paid: TypePaid;
need_delivery: boolean;
created_at: string;
items_count: number;
};
export type OrderSaveReqDTO = {
type: TypePaid;
need_delivery: boolean;
};
export enum OrderStatus { NEW = 1, PAID = 2, PROCESSING = 3, COMPLETED = 4, CANCELLED = 5 };
export type PendingPhoneProfileResource = {
id: number;
phone: string;
created_at: string;
};
export type PhoneCrudResource = {
phone: string;
verified_at: string;
};
export type PhoneProfileResource = {
phone: string;
verified_at: string;
};
export type PostCrudResource = {
id: number;
url: string;
title: string;
content: string;
created_at: string;
status: PostStatus;
category: CategoryCrudResource;
tags: Array<TagCrudResource>;
};
export type PostPublicResource = {
id: number;
url: string;
title: string | null;
content: string | null;
created_at: string;
category: CategoryPublicResource;
user: UserPublicResource;
tags: Array<TagPublicResource>;
};
export enum PostStatus { DRAFT = 0, MODERATING = 5, PUBLISHED = 10, REJECTED = 15 };
export type PostTrashResource = {
id: number;
url: string;
title: string;
created_at: string;
status: PostStatus;
category: CategoryCrudResource;
};
export type PostsQuery = {
filter?: PostsQueryFilters;
sort?: string;
page?: number;
batch?: number;
tagSearch?: string;
};
export type PostsQueryFilters = {
title?: string;
status?: PostStatus;
date_from?: string;
date_to?: string;
tags?: string;
};
export type PostsSaveReqDTO = {
url: string;
title: string;
content: string;
category_id: number;
tags: Array<any> | null;
};
export type PostsTrashQuery = {
filter?: PostsTrashQueryFilters;
sort?: string;
page?: number;
batch?: number;
};
export type PostsTrashQueryFilters = {
title?: string;
date_from?: string;
date_to?: string;
};
export type ProductCrudResource = {
id: number;
title: string;
description: string | null;
content: string | null;
price: string;
old_price: string | null;
category: CategoryCrudResource;
created_at: string;
updated_at: string;
count_paid: number | null;
active: boolean;
};
export type ProductPublicResource = {
id: number;
title: string;
description: string | null;
content: string | null;
price: string;
old_price: string | null;
count_paid: number | null;
images: Array<ImagePublicResource>;
};
export enum ProductStatus { IN_CART = 1, ORDERED = 2 };
export type ProductTrashResource = {
id: number;
title: string;
price: string;
category: CategoryCrudResource;
created_at: string;
};
export type ProductsClientQuery = {
filter?: ProductsClientQueryFilters;
url?: string;
page?: number;
};
export type ProductsClientQueryFilters = {
title?: string;
};
export type ProductsQuery = {
filter?: ProductsQueryFilters;
sort?: string;
page?: number;
batch?: number;
};
export type ProductsQueryFilters = {
title?: string;
category_id?: number;
date_from?: string;
date_to?: string;
};
export type ProductsSaveReqDTO = {
title: string;
description: string | null;
content: string | null;
price: string;
old_price: string | null;
category_id: number;
active: boolean;
};
export type ProductsTrashFiltersQuery = {
title?: string;
date_from?: string;
date_to?: string;
};
export type ProductsTrashQuery = {
filter?: ProductsTrashFiltersQuery;
sort?: string;
page?: number;
batch?: number;
};
export type ProfileSaveReqDTO = {
first_name: string;
middle_name: string | null;
last_name: string | null;
address: string | null;
};
export type RegisterReqDTO = {
first_name: string;
email: string;
password: string;
password_confirmation: string;
phone: string | null;
};
export type ResetPasswordReqDTO = {
token: string;
email: string | null;
password: string;
password_confirmation: string;
};
export type RoleCrudResource = {
id: number;
name: Roles;
description: string;
};
export type RolePublicResource = {
name: Roles;
description: string;
};
export enum Roles { USER = 'user', AUTHOR = 'author', ADMIN = 'admin', DEVELOPER = 'dev' };
export type SendCodeReqDTO = {
id: number;
};
export type TagCrudResource = {
id: number;
url: string;
title: string;
description: string | null;
created_at: string;
};
export type TagPublicResource = {
url: string;
title: string;
description: string | null;
};
export type TagsQuery = {
filter?: TagsQueryFilters;
sort?: string;
page?: number;
batch?: number;
};
export type TagsQueryFilters = {
url?: string;
title?: string;
};
export type TagsSaveReqDTO = {
url: string;
title: string;
description: string | null;
};
export enum Type { PRODUCT = 1, BLOG = 2 };
export enum TypePaid { CARD_ONLINE = 1, CARD_COURIER = 2, CASH_COURIER = 3, IN_PICKUP_LOCATION = 4 };
export type UserAuthResource = {
id: number;
first_name: string;
last_name: string | null;
email: string | null;
created_at: string;
roles: Array<RolePublicResource>;
address: string | null;
image: ImagePublicResource | null;
};
export type UserChangeBlockDTO = {
block: boolean;
};
export type UserChangeRolesRequestDTO = {
roleIds: Array<any>;
};
export type UserCrudResource = {
id: number;
first_name: string;
last_name: string | null;
middle_name: string | null;
email: string | null;
created_at: string;
roles: Array<RoleCrudResource>;
address: string | null;
block: boolean;
phone: PhoneCrudResource | null;
};
export type UserProfileResource = {
id: number;
first_name: string;
last_name: string | null;
middle_name: string | null;
email: string | null;
address: string | null;
phone: PhoneProfileResource | null;
pendingPhones: Array<PendingPhoneProfileResource>;
image: ImagePublicResource | null;
};
export type UserPublicResource = {
id: number;
first_name: string;
};
export type UsersQuery = {
filter?: UsersQueryFilters;
sort?: string;
page?: number;
batch?: number;
};
export type UsersQueryFilters = {
name?: string;
email?: string;
phone?: string;
block?: boolean;
address?: string;
date_from?: string;
date_to?: string;
};
