<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Auth\Access\Response;

class VendorPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('View Any Vendor');
    }

    public function view(User $user, Vendor $model): bool
    {
        return $user->can('View Vendor');
    }

    public function create(User $user): bool
    {
        return $user->can('Create Vendor');
    }

    public function update(User $user, Vendor $model): bool
    {
        return $user->can('Update Vendor');
    }

    public function delete(User $user, Vendor $model): bool
    {
        return $user->can('Delete Vendor');
    }

    public function restore(User $user, Vendor $model): bool
    {
        return $user->can('Restore Vendor');
    }

    public function forceDelete(User $user, Vendor $model): bool
    {
        return $user->can('Force Delete Vendor');
    }
}