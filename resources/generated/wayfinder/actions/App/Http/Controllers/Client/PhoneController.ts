import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Client\PhoneController::sendCode
* @see app/Http/Controllers/Client/PhoneController.php:28
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
* @see app/Http/Controllers/Client/PhoneController.php:28
* @route '/profile/phone/send-code'
*/
sendCode.url = (options?: RouteQueryOptions) => {
    return sendCode.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Client\PhoneController::sendCode
* @see app/Http/Controllers/Client/PhoneController.php:28
* @route '/profile/phone/send-code'
*/
sendCode.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: sendCode.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Client\PhoneController::confirmCode
* @see app/Http/Controllers/Client/PhoneController.php:48
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
* @see app/Http/Controllers/Client/PhoneController.php:48
* @route '/profile/phone/confirm-code'
*/
confirmCode.url = (options?: RouteQueryOptions) => {
    return confirmCode.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Client\PhoneController::confirmCode
* @see app/Http/Controllers/Client/PhoneController.php:48
* @route '/profile/phone/confirm-code'
*/
confirmCode.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: confirmCode.url(options),
    method: 'post',
})

const PhoneController = { sendCode, confirmCode }

export default PhoneController