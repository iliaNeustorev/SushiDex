<?php

namespace App\Policies;

use App\Enums\Posts\Status;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class PostPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return Gate::check('author', $user);
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAnyAdmin(User $user): bool
    {
        return Gate::check('moderator', $user);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Post $post): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return Gate::forUser($user)->allows('author');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Post $post): bool
    {
        if ($user->id === $post->user_id && ! Gate::check('moderator', $user)) {
            return ! ($post->status === Status::PUBLISHED);
        }

        return Gate::check('moderator', $user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Post $post): bool
    {
        return $user->id === $post->user_id || Gate::check('moderator', $user);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Post $post): bool
    {
        return Gate::forUser($user)->allows('dev');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Post $post): bool
    {
        return Gate::forUser($user)->allows('dev');
    }
}
