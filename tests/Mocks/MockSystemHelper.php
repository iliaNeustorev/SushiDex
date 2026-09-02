<?php

namespace Tests\Mocks;

use App\Enums\System\Roles;
use App\Interfaces\SystemHelperInterface;
use App\Models\User;

class MockSystemHelper implements SystemHelperInterface
{
    public function getRolesUserFromCache(User $user): array
    {
        return [Roles::DEVELOPER->value];
    }

    public function saveRolesUserInCache(User $user): array
    {
        return [Roles::DEVELOPER->value];
    }
}
