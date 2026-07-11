<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Status;
use Illuminate\Auth\Access\Response;

class StatusPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('View Any Status');
    }

    public function view(User $user, Status $model): bool
    {
        return $user->can('View Status');
    }

    public function create(User $user): bool
    {
        return $user->can('Create Status');
    }

    public function update(User $user, Status $model): bool
    {
        return $user->can('Update Status');
    }

    public function delete(User $user, Status $model): bool
    {
        return $user->can('Delete Status');
    }

    public function restore(User $user, Status $model): bool
    {
        return $user->can('Restore Status');
    }

    public function forceDelete(User $user, Status $model): bool
    {
        return $user->can('Force Delete Status');
    }
}