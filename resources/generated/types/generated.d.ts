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
export type OrderSaveReqDTO = {
type: TypePaid;
need_delivery: boolean;
total_price: string;
};
export type PhoneCrudResource = {
user: UserPublicResource | null;
phone: string;
verified_at: string;
};
export type PostCrudResource = {
id: number;
url: string;
title: string;
content: string;
created_at: string;
status: Status;
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
export type PostsQuery = {
filter?: PostsQueryFilters;
sort?: string;
page?: number;
batch?: number;
tagSearch?: string;
};
export type PostsQueryFilters = {
title?: string;
status?: Status;
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
export enum Status { DRAFT = 0, MODERATING = 5, PUBLISHED = 10, REJECTED = 15 };
export enum Status { NEW = 1, PAID = 2, PROCESSING = 3, COMPLETED = 4, CANCELLED = 5 };
export enum Status { IN_CART = 1, ORDERED = 2 };
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
