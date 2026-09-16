import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Client\ProfileController::index
* @see app/Http/Controllers/Client/ProfileController.php:18
* @route '/profile'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/profile',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Client\ProfileController::index
* @see app/Http/Controllers/Client/ProfileController.php:18
* @route '/profile'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Client\ProfileController::index
* @see app/Http/Controllers/Client/ProfileController.php:18
* @route '/profile'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Client\ProfileController::index
* @see app/Http/Controllers/Client/ProfileController.php:18
* @route '/profile'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Client\ProfileController::update
* @see app/Http/Controllers/Client/ProfileController.php:34
* @route '/profile'
*/
export const update = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: update.url(options),
    method: 'post',
})

update.definition = {
    methods: ["post"],
    url: '/profile',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Client\ProfileController::update
* @see app/Http/Controllers/Client/ProfileController.php:34
* @route '/profile'
*/
update.url = (options?: RouteQueryOptions) => {
    return update.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Client\ProfileController::update
* @see app/Http/Controllers/Client/ProfileController.php:34
* @route '/profile'
*/
update.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: update.url(options),
    method: 'post',
})

const ProfileController = { index, update }

export default ProfileController