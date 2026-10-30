<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        return Gate::forUser($user)->allows('moderator');
    }

    public function create(User $user): bool
    {
        return Gate::forUser($user)->allows('moderator');
    }

    public function update(User $user, Product $product): bool
    {
        return Gate::forUser($user)->allows('moderator');
    }

    public function delete(User $user, Product $product): bool
    {
        return Gate::forUser($user)->allows('moderator');
    }

    public function restore(User $user, Product $product): bool
    {
        return Gate::forUser($user)->allows('dev');
    }

    public function forceDelete(User $user, Product $product): bool
    {
        return Gate::forUser($user)->allows('dev');
    }

    public function active(User $user, Product $product): bool
    {
        return Gate::forUser($user)->allows('dev');
    }
}
