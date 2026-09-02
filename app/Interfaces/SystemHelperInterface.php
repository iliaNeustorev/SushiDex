<?php

namespace App\Interfaces;

use App\Models\User;

interface SystemHelperInterface
{
    public function getRolesUserFromCache(User $user): array;

    public function saveRolesUserInCache(User $user): array;
}
