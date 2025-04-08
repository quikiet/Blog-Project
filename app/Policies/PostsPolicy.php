<?php

namespace App\Policies;

use App\Models\User;
use App\Models\posts;
use Illuminate\Auth\Access\Response;

class PostsPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        if ($user && $user->role === 'admin') {
            return true;
        }
        if ($user && $user->role === 'author') {
            return true;
        }
        // Nếu là guest (user == null) hoặc user thường -> chỉ thấy published
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, posts $post): bool
    {
        if ($user && $user->role === 'admin') {
            return true;
        }

        if ($user && $user->role === 'author' && $user->id === $post->user_id) {
            return true;
        }

        if (in_array($post->status, ['published', 'archived'])) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return in_array($user->role, ['admin', 'author']) ? Response::allow() : Response::deny('You do not own this post.');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, posts $post): Response
    {
        return $user->role === 'admin' || $user->id === $post->user_id ? Response::allow() : Response::deny('You do not own this post.');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, posts $post): Response
    {
        return $user->role === 'admin' || $user->id === $post->user_id ? Response::allow() : Response::deny('You do not own this post.');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, posts $posts): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, posts $posts): bool
    {
        return false;
    }
}
