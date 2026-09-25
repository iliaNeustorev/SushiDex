import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\Trash\CategoryTrashController::index
* @see app/Http/Controllers/Admin/Trash/CategoryTrashController.php:24
* @route '/admin/category-trash'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/category-trash',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\Trash\CategoryTrashController::index
* @see app/Http/Controllers/Admin/Trash/CategoryTrashController.php:24
* @route '/admin/category-trash'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\Trash\CategoryTrashController::index
* @see app/Http/Controllers/Admin/Trash/CategoryTrashController.php:24
* @route '/admin/category-trash'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\Trash\CategoryTrashController::index
* @see app/Http/Controllers/Admin/Trash/CategoryTrashController.php:24
* @route '/admin/category-trash'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\Trash\CategoryTrashController::update
* @see app/Http/Controllers/Admin/Trash/CategoryTrashController.php:44
* @route '/admin/category-trash/{category_trash}'
*/
export const update = (args: { category_trash: string | number } | [category_trash: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/admin/category-trash/{category_trash}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\Admin\Trash\CategoryTrashController::update
* @see app/Http/Controllers/Admin/Trash/CategoryTrashController.php:44
* @route '/admin/category-trash/{category_trash}'
*/
update.url = (args: { category_trash: string | number } | [category_trash: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { category_trash: args }
    }

    if (Array.isArray(args)) {
        args = {
            category_trash: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        category_trash: args.category_trash,
    }

    return update.definition.url
            .replace('{category_trash}', parsedArgs.category_trash.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\Trash\CategoryTrashController::update
* @see app/Http/Controllers/Admin/Trash/CategoryTrashController.php:44
* @route '/admin/category-trash/{category_trash}'
*/
update.put = (args: { category_trash: string | number } | [category_trash: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Admin\Trash\CategoryTrashController::update
* @see app/Http/Controllers/Admin/Trash/CategoryTrashController.php:44
* @route '/admin/category-trash/{category_trash}'
*/
update.patch = (args: { category_trash: string | number } | [category_trash: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Admin\Trash\CategoryTrashController::destroy
* @see app/Http/Controllers/Admin/Trash/CategoryTrashController.php:58
* @route '/admin/category-trash/{category_trash}'
*/
export const destroy = (args: { category_trash: string | number } | [category_trash: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/category-trash/{category_trash}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Admin\Trash\CategoryTrashController::destroy
* @see app/Http/Controllers/Admin/Trash/CategoryTrashController.php:58
* @route '/admin/category-trash/{category_trash}'
*/
destroy.url = (args: { category_trash: string | number } | [category_trash: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { category_trash: args }
    }

    if (Array.isArray(args)) {
        args = {
            category_trash: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        category_trash: args.category_trash,
    }

    return destroy.definition.url
            .replace('{category_trash}', parsedArgs.category_trash.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\Trash\CategoryTrashController::destroy
* @see app/Http/Controllers/Admin/Trash/CategoryTrashController.php:58
* @route '/admin/category-trash/{category_trash}'
*/
destroy.delete = (args: { category_trash: string | number } | [category_trash: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

const CategoryTrashController = { index, update, destroy }

export default CategoryTrashController