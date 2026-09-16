import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Client\PhoneController::store
* @see app/Http/Controllers/Client/PhoneController.php:27
* @route '/profile/phone'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/profile/phone',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Client\PhoneController::store
* @see app/Http/Controllers/Client/PhoneController.php:27
* @route '/profile/phone'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Client\PhoneController::store
* @see app/Http/Controllers/Client/PhoneController.php:27
* @route '/profile/phone'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Client\PhoneController::sendCode
* @see app/Http/Controllers/Client/PhoneController.php:35
* @route '/profile/phone/send-code'
*/
export const sendCode = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: sendCode.url(options),
    method: 'post',
})

sendCode.definition = {
    methods: ["post"],
    url: '/profile/phone/send-code',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Client\PhoneController::sendCode
* @see app/Http/Controllers/Client/PhoneController.php:35
* @route '/profile/phone/send-code'
*/
sendCode.url = (options?: RouteQueryOptions) => {
    return sendCode.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Client\PhoneController::sendCode
* @see app/Http/Controllers/Client/PhoneController.php:35
* @route '/profile/phone/send-code'
*/
sendCode.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: sendCode.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Client\PhoneController::confirmCode
* @see app/Http/Controllers/Client/PhoneController.php:55
* @route '/profile/phone/confirm-code'
*/
export const confirmCode = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: confirmCode.url(options),
    method: 'post',
})

confirmCode.definition = {
    methods: ["post"],
    url: '/profile/phone/confirm-code',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Client\PhoneController::confirmCode
* @see app/Http/Controllers/Client/PhoneController.php:55
* @route '/profile/phone/confirm-code'
*/
confirmCode.url = (options?: RouteQueryOptions) => {
    return confirmCode.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Client\PhoneController::confirmCode
* @see app/Http/Controllers/Client/PhoneController.php:55
* @route '/profile/phone/confirm-code'
*/
confirmCode.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: confirmCode.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Client\PhoneController::destroy
* @see app/Http/Controllers/Client/PhoneController.php:85
* @route '/profile/phone/{pendingPhone}'
*/
export const destroy = (args: { pendingPhone: number | { id: number } } | [pendingPhone: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/profile/phone/{pendingPhone}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Client\PhoneController::destroy
* @see app/Http/Controllers/Client/PhoneController.php:85
* @route '/profile/phone/{pendingPhone}'
*/
destroy.url = (args: { pendingPhone: number | { id: number } } | [pendingPhone: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { pendingPhone: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { pendingPhone: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            pendingPhone: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        pendingPhone: typeof args.pendingPhone === 'object'
        ? args.pendingPhone.id
        : args.pendingPhone,
    }

    return destroy.definition.url
            .replace('{pendingPhone}', parsedArgs.pendingPhone.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Client\PhoneController::destroy
* @see app/Http/Controllers/Client/PhoneController.php:85
* @route '/profile/phone/{pendingPhone}'
*/
destroy.delete = (args: { pendingPhone: number | { id: number } } | [pendingPhone: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

const PhoneController = { store, sendCode, confirmCode, destroy }

export default PhoneController