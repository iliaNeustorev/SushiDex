<?php

namespace App\Providers;

use App\Enums\System\Roles;
use App\Interfaces\SystemHelperInterface;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $systemHelper = app(SystemHelperInterface::class);

        /**
         * Check role dev.
         */
        Gate::define('dev', function ($user) use ($systemHelper) {
            $roles = $systemHelper->getRolesUserFromCache($user);
            $needRoles = [Roles::DEVELOPER->value];

            return count(array_intersect($needRoles, $roles)) > 0;
        });

        /**
         * Check role moderator.
         */
        Gate::define('moderator', function ($user) use ($systemHelper) {
            $roles = $systemHelper->getRolesUserFromCache($user);
            $needRoles = [Roles::DEVELOPER->value, Roles::ADMIN->value];

            return count(array_intersect($needRoles, $roles)) > 0;
        });

        /**
         * Check role author.
         */
        Gate::define('author', function ($user) use ($systemHelper) {
            $roles = $systemHelper->getRolesUserFromCache($user);
            $needRoles = [Roles::AUTHOR->value];

            return count(array_intersect($needRoles, $roles)) > 0;
        });
    }
}
