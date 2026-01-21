<?php

namespace App\Policies;

use App\Models\AgeGroup;
use App\Models\User;

class AgeGroupPolicy
{
    // Разрешаем только Админам

    public function viewAny(User $user): bool
    {
        return $user->club_id === null;
    }

    public function view(User $user, AgeGroup $ageGroup): bool
    {
        return $user->club_id === null;
    }

    public function create(User $user): bool
    {
        return $user->club_id === null;
    }

    public function update(User $user, AgeGroup $ageGroup): bool
    {
        return $user->club_id === null;
    }

    public function delete(User $user, AgeGroup $ageGroup): bool
    {
        return $user->club_id === null;
    }
}
