<?php

namespace App\Policies;

use App\Models\Club;
use App\Models\User;

class ClubPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->club_id === null;
    }

    public function view(User $user, Club $club): bool
    {
        return $user->club_id === null;
    }

    public function create(User $user): bool
    {
        return $user->club_id === null;
    }

    public function update(User $user, Club $club): bool
    {
        return $user->club_id === null;
    }

    public function delete(User $user, Club $club): bool
    {
        return $user->club_id === null;
    }
}
