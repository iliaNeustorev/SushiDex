import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\Trash\ProductTrashController::index
* @see app/Http/Controllers/Admin/Trash/ProductTrashController.php:24
* @route '/admin/product-trash'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/product-trash',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\Trash\ProductTrashController::index
* @see app/Http/Controllers/Admin/Trash/ProductTrashController.php:24
* @route '/admin/product-trash'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\Trash\ProductTrashController::index
* @see app/Http/Controllers/Admin/Trash/ProductTrashController.php:24
* @route '/admin/product-trash'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\Trash\ProductTrashController::index
* @see app/Http/Controllers/Admin/Trash/ProductTrashController.php:24
* @route '/admin/product-trash'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\Trash\ProductTrashController::update
* @see app/Http/Controllers/Admin/Trash/ProductTrashController.php:47
* @route '/admin/product-trash/{product_trash}'
*/
export const update = (args: { product_trash: string | number } | [product_trash: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/admin/product-trash/{product_trash}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\Admin\Trash\ProductTrashController::update
* @see app/Http/Controllers/Admin/Trash/ProductTrashController.php:47
* @route '/admin/product-trash/{product_trash}'
*/
update.url = (args: { product_trash: string | number } | [product_trash: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { product_trash: args }
    }

    if (Array.isArray(args)) {
        args = {
            product_trash: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        product_trash: args.product_trash,
    }

    return update.definition.url
            .replace('{product_trash}', parsedArgs.product_trash.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\Trash\ProductTrashController::update
* @see app/Http/Controllers/Admin/Trash/ProductTrashController.php:47
* @route '/admin/product-trash/{product_trash}'
*/
update.put = (args: { product_trash: string | number } | [product_trash: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Admin\Trash\ProductTrashController::update
* @see app/Http/Controllers/Admin/Trash/ProductTrashController.php:47
* @route '/admin/product-trash/{product_trash}'
*/
update.patch = (args: { product_trash: string | number } | [product_trash: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Admin\Trash\ProductTrashController::destroy
* @see app/Http/Controllers/Admin/Trash/ProductTrashController.php:61
* @route '/admin/product-trash/{product_trash}'
*/
export const destroy = (args: { product_trash: string | number } | [product_trash: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/product-trash/{product_trash}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Admin\Trash\ProductTrashController::destroy
* @see app/Http/Controllers/Admin/Trash/ProductTrashController.php:61
* @route '/admin/product-trash/{product_trash}'
*/
destroy.url = (args: { product_trash: string | number } | [product_trash: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { product_trash: args }
    }

    if (Array.isArray(args)) {
        args = {
            product_trash: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        product_trash: args.product_trash,
    }

    return destroy.definition.url
            .replace('{product_trash}', parsedArgs.product_trash.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\Trash\ProductTrashController::destroy
* @see app/Http/Controllers/Admin/Trash/ProductTrashController.php:61
* @route '/admin/product-trash/{product_trash}'
*/
destroy.delete = (args: { product_trash: string | number } | [product_trash: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

const ProductTrashController = { index, update, destroy }

export default ProductTrashController