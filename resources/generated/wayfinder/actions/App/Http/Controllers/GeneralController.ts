import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\GeneralController::menu
* @see app/Http/Controllers/GeneralController.php:27
* @route '/menu'
*/
export const menu = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: menu.url(options),
    method: 'get',
})

menu.definition = {
    methods: ["get","head"],
    url: '/menu',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\GeneralController::menu
* @see app/Http/Controllers/GeneralController.php:27
* @route '/menu'
*/
menu.url = (options?: RouteQueryOptions) => {
    return menu.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\GeneralController::menu
* @see app/Http/Controllers/GeneralController.php:27
* @route '/menu'
*/
menu.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: menu.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\GeneralController::menu
* @see app/Http/Controllers/GeneralController.php:27
* @route '/menu'
*/
menu.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: menu.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\GeneralController::index
* @see app/Http/Controllers/GeneralController.php:20
* @route '/'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\GeneralController::index
* @see app/Http/Controllers/GeneralController.php:20
* @route '/'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\GeneralController::index
* @see app/Http/Controllers/GeneralController.php:20
* @route '/'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\GeneralController::index
* @see app/Http/Controllers/GeneralController.php:20
* @route '/'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

const GeneralController = { menu, index }

export default GeneralController