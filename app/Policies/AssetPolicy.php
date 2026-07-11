<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Asset;
use Illuminate\Auth\Access\Response;

class AssetPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('View Any Asset');
    }

    public function view(User $user, Asset $model): bool
    {
        return $user->can('View Asset');
    }

    public function create(User $user): bool
    {
        return $user->can('Create Asset');
    }

    public function update(User $user, Asset $model): bool
    {
        return $user->can('Update Asset');
    }

    public function delete(User $user, Asset $model): bool
    {
        return $user->can('Delete Asset');
    }

    public function restore(User $user, Asset $model): bool
    {
        return $user->can('Restore Asset');
    }

    public function forceDelete(User $user, Asset $model): bool
    {
        return $user->can('Force Delete Asset');
    }
}