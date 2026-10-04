<?php

namespace App\Policies;

use App\Enums\Property\PropertyStatus;
use App\Enums\User\UserRole;
use App\Models\Property;
use App\Models\User;

class PropertyPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Property $property): bool
    {
        if ($property->status === PropertyStatus::Published) {
            return true;
        }

        return $user !== null && $user->id === $property->agent_id;
    }

    public function create(User $user): bool
    {
        return $user->role === UserRole::Agent;
    }

    public function update(User $user, Property $property): bool
    {
        return $user->id === $property->agent_id;
    }

    public function delete(User $user, Property $property): bool
    {
        return $user->id === $property->agent_id;
    }

    public function transitionStatus(User $user, Property $property): bool
    {
        return $user->id === $property->agent_id;
    }

    public function feature(User $user, Property $property): bool
    {
        return $user->id === $property->agent_id && $property->status === PropertyStatus::Published;
    }
}
