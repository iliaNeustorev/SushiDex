export type CartSaveReqDTO = {
product_id: number;
count: number;
};
export type CategoriesSaveReqDTO = {
url: string;
title: string;
type: Type | null;
parent_id: number | null;
};
export type CategoryCrudResource = {
id: number;
url: string;
title: string;
created_at: string;
type: Type | null;
parent_id: number | null;
};
export type CategoryPublicResource = {
url: string;
title: string;
type: Type | null;
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
export type OrderSaveReqDTO = {
user_id: number;
total_price: string;
status: Status;
};
export type PostCrudResource = {
id: number;
url: string;
title: string;
content: string;
created_at: string;
status: Status;
category: CategoryCrudResource | null;
tags: Array<TagCrudResource>;
};
export type PostPublicResource = {
id: number;
url: string;
title: string | null;
content: string | null;
created_at: string;
category: CategoryPublicResource | null;
user: UserPublicResource | null;
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
category_id: number | null;
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
};
export type ProductsSaveReqDTO = {
title: string;
description: string | null;
content: string | null;
price: string;
old_price: string | null;
category_id: number;
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
description: string;
created_at: string;
};
export type TagPublicResource = {
url: string;
title: string;
description: string | null;
};
export enum Type { PRODUCT = 1, BLOG = 2 };
export type UserAuthResource = {
id: number;
first_name: string;
last_name: string | null;
email: string | null;
created_at: string;
roles: Array<RolePublicResource>;
};
export type UserPublicResource = {
id: number;
first_name: string;
};
