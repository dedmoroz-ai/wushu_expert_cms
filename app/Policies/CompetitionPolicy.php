<?php

namespace App\Policies;

use App\Models\Competition;
use App\Models\User;

class CompetitionPolicy
{
    // Админ (без клуба) может всё. Тренер (с клубом) - ничего.

    public function viewAny(User $user): bool
    {
        return $user->club_id === null;
    }

    public function view(User $user, Competition $competition): bool
    {
        return $user->club_id === null;
    }

    public function create(User $user): bool
    {
        return $user->club_id === null;
    }

    public function update(User $user, Competition $competition): bool
    {
        return $user->club_id === null;
    }

    public function delete(User $user, Competition $competition): bool
    {
        return $user->club_id === null;
    }
}
