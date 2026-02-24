<?php

namespace App\Policies;

use App\Models\Calculation;
use App\Models\User;

class CalculationPolicy
{
    public function view(User $user, Calculation $calculation): bool
    {
        return $user->id === $calculation->user_id;
    }

    public function delete(User $user, Calculation $calculation): bool
    {
        return $user->id === $calculation->user_id;
    }
}
