<?php

namespace App\Policies;

use App\Models\User;
use App\Models\SoftwareLicenseDetail;
use Illuminate\Auth\Access\Response;

class SoftwareLicenseDetailPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('View Any Software License Detail');
    }

    public function view(User $user, SoftwareLicenseDetail $model): bool
    {
        return $user->can('View Software License Detail');
    }

    public function create(User $user): bool
    {
        return $user->can('Create Software License Detail');
    }

    public function update(User $user, SoftwareLicenseDetail $model): bool
    {
        return $user->can('Update Software License Detail');
    }

    public function delete(User $user, SoftwareLicenseDetail $model): bool
    {
        return $user->can('Delete Software License Detail');
    }

    public function restore(User $user, SoftwareLicenseDetail $model): bool
    {
        return $user->can('Restore Software License Detail');
    }

    public function forceDelete(User $user, SoftwareLicenseDetail $model): bool
    {
        return $user->can('Force Delete Software License Detail');
    }
}