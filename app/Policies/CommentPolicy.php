<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    private function moderates(User $user): bool
    {
        return in_array($user->role, ['admin', 'moderator']);
    }

    public function viewAny(User $user): bool
    {
        return $this->moderates($user);
    }

    public function view(User $user, Comment $comment): bool
    {
        return $this->moderates($user);
    }

    // التعليقات تأتي من الزبائن فقط
    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Comment $comment): bool
    {
        return $this->moderates($user);
    }

    public function delete(User $user, Comment $comment): bool
    {
        return $this->moderates($user);
    }

    public function deleteAny(User $user): bool
    {
        return $this->moderates($user);
    }
}
