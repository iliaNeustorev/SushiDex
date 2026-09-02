<?php

namespace App\Helpers;

use App\Interfaces\SystemHelperInterface;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class SystemHelper implements SystemHelperInterface
{
    public function getRolesUserFromCache(User $user): array
    {
        if (Cache::has("{$user->id}.roles")) {
            return Cache::get("{$user->id}.roles");
        } else {
            $roles = $this->saveRolesUserInCache($user);

            return $roles;
        }
    }

    public function saveRolesUserInCache(User $user): array
    {
        $roles = $user->roles->pluck('name')->pluck('value')->toArray();
        Cache::put("$user->id.roles", $roles);

        return $roles;
    }
}
