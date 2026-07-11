<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Department;
use Illuminate\Auth\Access\Response;

class DepartmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('View Any Department');
    }

    public function view(User $user, Department $model): bool
    {
        return $user->can('View Department');
    }

    public function create(User $user): bool
    {
        return $user->can('Create Department');
    }

    public function update(User $user, Department $model): bool
    {
        return $user->can('Update Department');
    }

    public function delete(User $user, Department $model): bool
    {
        return $user->can('Delete Department');
    }

    public function restore(User $user, Department $model): bool
    {
        return $user->can('Restore Department');
    }

    public function forceDelete(User $user, Department $model): bool
    {
        return $user->can('Force Delete Department');
    }
}