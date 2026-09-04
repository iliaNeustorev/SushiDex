import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Auth\VerificationController::verify
* @see app/Http/Controllers/Auth/VerificationController.php:19
* @route '/email/verify/{id}/{hash}'
*/
export const verify = (args: { id: string | number, hash: string | number } | [id: string | number, hash: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: verify.url(args, options),
    method: 'get',
})

verify.definition = {
    methods: ["get","head"],
    url: '/email/verify/{id}/{hash}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Auth\VerificationController::verify
* @see app/Http/Controllers/Auth/VerificationController.php:19
* @route '/email/verify/{id}/{hash}'
*/
verify.url = (args: { id: string | number, hash: string | number } | [id: string | number, hash: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            id: args[0],
            hash: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        id: args.id,
        hash: args.hash,
    }

    return verify.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace('{hash}', parsedArgs.hash.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Auth\VerificationController::verify
* @see app/Http/Controllers/Auth/VerificationController.php:19
* @route '/email/verify/{id}/{hash}'
*/
verify.get = (args: { id: string | number, hash: string | number } | [id: string | number, hash: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: verify.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\VerificationController::verify
* @see app/Http/Controllers/Auth/VerificationController.php:19
* @route '/email/verify/{id}/{hash}'
*/
verify.head = (args: { id: string | number, hash: string | number } | [id: string | number, hash: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: verify.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Auth\VerificationController::repeatSendMail
* @see app/Http/Controllers/Auth/VerificationController.php:12
* @route '/email/verification-notification'
*/
export const repeatSendMail = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: repeatSendMail.url(options),
    method: 'post',
})

repeatSendMail.definition = {
    methods: ["post"],
    url: '/email/verification-notification',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Auth\VerificationController::repeatSendMail
* @see app/Http/Controllers/Auth/VerificationController.php:12
* @route '/email/verification-notification'
*/
repeatSendMail.url = (options?: RouteQueryOptions) => {
    return repeatSendMail.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Auth\VerificationController::repeatSendMail
* @see app/Http/Controllers/Auth/VerificationController.php:12
* @route '/email/verification-notification'
*/
repeatSendMail.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: repeatSendMail.url(options),
    method: 'post',
})

const VerificationController = { verify, repeatSendMail }

export default VerificationController