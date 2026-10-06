import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\OrderController::updateSettings
* @see app/Http/Controllers/Admin/OrderController.php:74
* @route '/orders/{order}/update-settings'
*/
export const updateSettings = (args: { order: number | { id: number } } | [order: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateSettings.url(args, options),
    method: 'patch',
})

updateSettings.definition = {
    methods: ["patch"],
    url: '/orders/{order}/update-settings',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\Admin\OrderController::updateSettings
* @see app/Http/Controllers/Admin/OrderController.php:74
* @route '/orders/{order}/update-settings'
*/
updateSettings.url = (args: { order: number | { id: number } } | [order: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { order: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { order: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            order: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        order: typeof args.order === 'object'
        ? args.order.id
        : args.order,
    }

    return updateSettings.definition.url
            .replace('{order}', parsedArgs.order.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\OrderController::updateSettings
* @see app/Http/Controllers/Admin/OrderController.php:74
* @route '/orders/{order}/update-settings'
*/
updateSettings.patch = (args: { order: number | { id: number } } | [order: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateSettings.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Admin\OrderController::index
* @see app/Http/Controllers/Admin/OrderController.php:29
* @route '/admin/orders'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/orders',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\OrderController::index
* @see app/Http/Controllers/Admin/OrderController.php:29
* @route '/admin/orders'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\OrderController::index
* @see app/Http/Controllers/Admin/OrderController.php:29
* @route '/admin/orders'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\OrderController::index
* @see app/Http/Controllers/Admin/OrderController.php:29
* @route '/admin/orders'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\OrderController::update
* @see app/Http/Controllers/Admin/OrderController.php:37
* @route '/admin/orders/{order}'
*/
export const update = (args: { order: number | { id: number } } | [order: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/admin/orders/{order}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\Admin\OrderController::update
* @see app/Http/Controllers/Admin/OrderController.php:37
* @route '/admin/orders/{order}'
*/
update.url = (args: { order: number | { id: number } } | [order: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { order: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { order: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            order: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        order: typeof args.order === 'object'
        ? args.order.id
        : args.order,
    }

    return update.definition.url
            .replace('{order}', parsedArgs.order.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\OrderController::update
* @see app/Http/Controllers/Admin/OrderController.php:37
* @route '/admin/orders/{order}'
*/
update.put = (args: { order: number | { id: number } } | [order: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Admin\OrderController::update
* @see app/Http/Controllers/Admin/OrderController.php:37
* @route '/admin/orders/{order}'
*/
update.patch = (args: { order: number | { id: number } } | [order: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Admin\OrderController::actual
* @see app/Http/Controllers/Admin/OrderController.php:45
* @route '/admin/orders/actual'
*/
export const actual = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: actual.url(options),
    method: 'get',
})

actual.definition = {
    methods: ["get","head"],
    url: '/admin/orders/actual',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\OrderController::actual
* @see app/Http/Controllers/Admin/OrderController.php:45
* @route '/admin/orders/actual'
*/
actual.url = (options?: RouteQueryOptions) => {
    return actual.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\OrderController::actual
* @see app/Http/Controllers/Admin/OrderController.php:45
* @route '/admin/orders/actual'
*/
actual.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: actual.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\OrderController::actual
* @see app/Http/Controllers/Admin/OrderController.php:45
* @route '/admin/orders/actual'
*/
actual.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: actual.url(options),
    method: 'head',
})

const OrderController = { updateSettings, index, update, actual }

export default OrderController