import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Client\ProfileController::index
* @see app/Http/Controllers/Client/ProfileController.php:26
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
* @see app/Http/Controllers/Client/ProfileController.php:26
* @route '/profile'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Client\ProfileController::index
* @see app/Http/Controllers/Client/ProfileController.php:26
* @route '/profile'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Client\ProfileController::index
* @see app/Http/Controllers/Client/ProfileController.php:26
* @route '/profile'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Client\ProfileController::update
* @see app/Http/Controllers/Client/ProfileController.php:43
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
* @see app/Http/Controllers/Client/ProfileController.php:43
* @route '/profile'
*/
update.url = (options?: RouteQueryOptions) => {
    return update.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Client\ProfileController::update
* @see app/Http/Controllers/Client/ProfileController.php:43
* @route '/profile'
*/
update.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: update.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Client\ProfileController::changeAvatar
* @see app/Http/Controllers/Client/ProfileController.php:56
* @route '/profile/change-avatar'
*/
export const changeAvatar = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: changeAvatar.url(options),
    method: 'post',
})

changeAvatar.definition = {
    methods: ["post"],
    url: '/profile/change-avatar',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Client\ProfileController::changeAvatar
* @see app/Http/Controllers/Client/ProfileController.php:56
* @route '/profile/change-avatar'
*/
changeAvatar.url = (options?: RouteQueryOptions) => {
    return changeAvatar.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Client\ProfileController::changeAvatar
* @see app/Http/Controllers/Client/ProfileController.php:56
* @route '/profile/change-avatar'
*/
changeAvatar.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: changeAvatar.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Client\ProfileController::destroyAvatar
* @see app/Http/Controllers/Client/ProfileController.php:74
* @route '/profile/delete-avatar'
*/
export const destroyAvatar = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroyAvatar.url(options),
    method: 'delete',
})

destroyAvatar.definition = {
    methods: ["delete"],
    url: '/profile/delete-avatar',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Client\ProfileController::destroyAvatar
* @see app/Http/Controllers/Client/ProfileController.php:74
* @route '/profile/delete-avatar'
*/
destroyAvatar.url = (options?: RouteQueryOptions) => {
    return destroyAvatar.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Client\ProfileController::destroyAvatar
* @see app/Http/Controllers/Client/ProfileController.php:74
* @route '/profile/delete-avatar'
*/
destroyAvatar.delete = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroyAvatar.url(options),
    method: 'delete',
})

const ProfileController = { index, update, changeAvatar, destroyAvatar }

export default ProfileController