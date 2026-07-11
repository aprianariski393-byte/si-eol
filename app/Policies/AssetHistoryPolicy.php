<?php

namespace App\Policies;

use App\Models\User;
use App\Models\AssetHistory;
use Illuminate\Auth\Access\Response;

class AssetHistoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('View Any Asset History');
    }

    public function view(User $user, AssetHistory $model): bool
    {
        return $user->can('View Asset History');
    }

    public function create(User $user): bool
    {
        return $user->can('Create Asset History');
    }

    public function update(User $user, AssetHistory $model): bool
    {
        return $user->can('Update Asset History');
    }

    public function delete(User $user, AssetHistory $model): bool
    {
        return $user->can('Delete Asset History');
    }

    public function restore(User $user, AssetHistory $model): bool
    {
        return $user->can('Restore Asset History');
    }

    public function forceDelete(User $user, AssetHistory $model): bool
    {
        return $user->can('Force Delete Asset History');
    }
}