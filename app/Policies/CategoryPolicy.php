<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Category;
use Illuminate\Auth\Access\Response;

class CategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('View Any Category');
    }

    public function view(User $user, Category $model): bool
    {
        return $user->can('View Category');
    }

    public function create(User $user): bool
    {
        return $user->can('Create Category');
    }

    public function update(User $user, Category $model): bool
    {
        return $user->can('Update Category');
    }

    public function delete(User $user, Category $model): bool
    {
        return $user->can('Delete Category');
    }

    public function restore(User $user, Category $model): bool
    {
        return $user->can('Restore Category');
    }

    public function forceDelete(User $user, Category $model): bool
    {
        return $user->can('Force Delete Category');
    }
}