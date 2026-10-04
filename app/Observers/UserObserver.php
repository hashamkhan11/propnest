<?php

namespace App\Observers;

use App\Enums\User\UserRole;
use App\Models\AgentProfile;
use App\Models\User;

class UserObserver
{
    public function created(User $user): void
    {
        if ($user->role === UserRole::Agent) {
            AgentProfile::create(['user_id' => $user->id]);
        }
    }
}
