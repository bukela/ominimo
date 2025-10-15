<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Post $post): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return (bool) $user;
    }

    public function update(User $user, Post $post): bool
    {
        return $user->id === $post->user_id || $user->isAdmin();
    }

    public function delete(User $user, Post $post): bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        // Moderator cannot delete others' posts
        if ($user->isModerator()) {
            return $user->id === $post->user_id;
        }
        return $user->id === $post->user_id;
    }

    public function moderate(User $user, Post $post): bool
    {
        return $user->isAdmin() || $user->isModerator();
    }
}
