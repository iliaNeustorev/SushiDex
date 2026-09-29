import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\CartController::index
* @see app/Http/Controllers/CartController.php:23
* @route '/cart'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/cart',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CartController::index
* @see app/Http/Controllers/CartController.php:23
* @route '/cart'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CartController::index
* @see app/Http/Controllers/CartController.php:23
* @route '/cart'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CartController::index
* @see app/Http/Controllers/CartController.php:23
* @route '/cart'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\CartController::update
* @see app/Http/Controllers/CartController.php:46
* @route '/cart/update'
*/
export const update = (options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/cart/update',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\CartController::update
* @see app/Http/Controllers/CartController.php:46
* @route '/cart/update'
*/
update.url = (options?: RouteQueryOptions) => {
    return update.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CartController::update
* @see app/Http/Controllers/CartController.php:46
* @route '/cart/update'
*/
update.put = (options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\CartController::destroy
* @see app/Http/Controllers/CartController.php:62
* @route '/cart/delete'
*/
export const destroy = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/cart/delete',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\CartController::destroy
* @see app/Http/Controllers/CartController.php:62
* @route '/cart/delete'
*/
destroy.url = (options?: RouteQueryOptions) => {
    return destroy.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CartController::destroy
* @see app/Http/Controllers/CartController.php:62
* @route '/cart/delete'
*/
destroy.delete = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\CartController::syncWithTemp
* @see app/Http/Controllers/CartController.php:70
* @route '/cart/sync-temp-cart'
*/
export const syncWithTemp = (options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: syncWithTemp.url(options),
    method: 'put',
})

syncWithTemp.definition = {
    methods: ["put"],
    url: '/cart/sync-temp-cart',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\CartController::syncWithTemp
* @see app/Http/Controllers/CartController.php:70
* @route '/cart/sync-temp-cart'
*/
syncWithTemp.url = (options?: RouteQueryOptions) => {
    return syncWithTemp.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CartController::syncWithTemp
* @see app/Http/Controllers/CartController.php:70
* @route '/cart/sync-temp-cart'
*/
syncWithTemp.put = (options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: syncWithTemp.url(options),
    method: 'put',
})

const CartController = { index, update, destroy, syncWithTemp }

export default CartController