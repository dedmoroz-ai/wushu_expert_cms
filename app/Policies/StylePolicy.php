<?php

namespace App\Policies;

use App\Models\Style;
use App\Models\User;

class StylePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->club_id === null;
    }

    public function view(User $user, Style $style): bool
    {
        return $user->club_id === null;
    }

    public function create(User $user): bool
    {
        return $user->club_id === null;
    }

    public function update(User $user, Style $style): bool
    {
        return $user->club_id === null;
    }

    public function delete(User $user, Style $style): bool
    {
        return $user->club_id === null;
    }
}
