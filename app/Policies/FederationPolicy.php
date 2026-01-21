<?php

namespace App\Policies;

use App\Models\Federation;
use App\Models\User;

class FederationPolicy
{
    // Разрешаем только Админам (у кого нет club_id)

    public function viewAny(User $user): bool
    {
        return $user->club_id === null;
    }

    public function view(User $user, Federation $federation): bool
    {
        return $user->club_id === null;
    }

    public function create(User $user): bool
    {
        return $user->club_id === null;
    }

    public function update(User $user, Federation $federation): bool
    {
        return $user->club_id === null;
    }

    public function delete(User $user, Federation $federation): bool
    {
        return $user->club_id === null;
    }
}
