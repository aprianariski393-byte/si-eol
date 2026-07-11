<?php

namespace App\Policies;

use App\Models\User;
use App\Models\MaintenanceLog;
use Illuminate\Auth\Access\Response;

class MaintenanceLogPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('View Any Maintenance Log');
    }

    public function view(User $user, MaintenanceLog $model): bool
    {
        return $user->can('View Maintenance Log');
    }

    public function create(User $user): bool
    {
        return $user->can('Create Maintenance Log');
    }

    public function update(User $user, MaintenanceLog $model): bool
    {
        return $user->can('Update Maintenance Log');
    }

    public function delete(User $user, MaintenanceLog $model): bool
    {
        return $user->can('Delete Maintenance Log');
    }

    public function restore(User $user, MaintenanceLog $model): bool
    {
        return $user->can('Restore Maintenance Log');
    }

    public function forceDelete(User $user, MaintenanceLog $model): bool
    {
        return $user->can('Force Delete Maintenance Log');
    }
}