<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Location;
use Illuminate\Auth\Access\Response;

class LocationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('View Any Location');
    }

    public function view(User $user, Location $model): bool
    {
        return $user->can('View Location');
    }

    public function create(User $user): bool
    {
        return $user->can('Create Location');
    }

    public function update(User $user, Location $model): bool
    {
        return $user->can('Update Location');
    }

    public function delete(User $user, Location $model): bool
    {
        return $user->can('Delete Location');
    }

    public function restore(User $user, Location $model): bool
    {
        return $user->can('Restore Location');
    }

    public function forceDelete(User $user, Location $model): bool
    {
        return $user->can('Force Delete Location');
    }
}