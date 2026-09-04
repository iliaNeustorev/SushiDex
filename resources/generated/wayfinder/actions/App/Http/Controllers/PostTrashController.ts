import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\PostTrashController::index
* @see app/Http/Controllers/PostTrashController.php:13
* @route '/admin/post-trash'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/post-trash',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PostTrashController::index
* @see app/Http/Controllers/PostTrashController.php:13
* @route '/admin/post-trash'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PostTrashController::index
* @see app/Http/Controllers/PostTrashController.php:13
* @route '/admin/post-trash'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PostTrashController::index
* @see app/Http/Controllers/PostTrashController.php:13
* @route '/admin/post-trash'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\PostTrashController::update
* @see app/Http/Controllers/PostTrashController.php:23
* @route '/admin/post-trash/{post_trash}'
*/
export const update = (args: { post_trash: string | number } | [post_trash: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/admin/post-trash/{post_trash}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\PostTrashController::update
* @see app/Http/Controllers/PostTrashController.php:23
* @route '/admin/post-trash/{post_trash}'
*/
update.url = (args: { post_trash: string | number } | [post_trash: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { post_trash: args }
    }

    if (Array.isArray(args)) {
        args = {
            post_trash: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        post_trash: args.post_trash,
    }

    return update.definition.url
            .replace('{post_trash}', parsedArgs.post_trash.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PostTrashController::update
* @see app/Http/Controllers/PostTrashController.php:23
* @route '/admin/post-trash/{post_trash}'
*/
update.put = (args: { post_trash: string | number } | [post_trash: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\PostTrashController::update
* @see app/Http/Controllers/PostTrashController.php:23
* @route '/admin/post-trash/{post_trash}'
*/
update.patch = (args: { post_trash: string | number } | [post_trash: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\PostTrashController::destroy
* @see app/Http/Controllers/PostTrashController.php:35
* @route '/admin/post-trash/{post_trash}'
*/
export const destroy = (args: { post_trash: string | number } | [post_trash: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/post-trash/{post_trash}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\PostTrashController::destroy
* @see app/Http/Controllers/PostTrashController.php:35
* @route '/admin/post-trash/{post_trash}'
*/
destroy.url = (args: { post_trash: string | number } | [post_trash: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { post_trash: args }
    }

    if (Array.isArray(args)) {
        args = {
            post_trash: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        post_trash: args.post_trash,
    }

    return destroy.definition.url
            .replace('{post_trash}', parsedArgs.post_trash.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PostTrashController::destroy
* @see app/Http/Controllers/PostTrashController.php:35
* @route '/admin/post-trash/{post_trash}'
*/
destroy.delete = (args: { post_trash: string | number } | [post_trash: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

const PostTrashController = { index, update, destroy }

export default PostTrashController