<?php

namespace App\Policies;

use App\Models\User;
use App\Models\AssetAttachment;
use Illuminate\Auth\Access\Response;

class AssetAttachmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('View Any Asset Attachment');
    }

    public function view(User $user, AssetAttachment $model): bool
    {
        return $user->can('View Asset Attachment');
    }

    public function create(User $user): bool
    {
        return $user->can('Create Asset Attachment');
    }

    public function update(User $user, AssetAttachment $model): bool
    {
        return $user->can('Update Asset Attachment');
    }

    public function delete(User $user, AssetAttachment $model): bool
    {
        return $user->can('Delete Asset Attachment');
    }

    public function restore(User $user, AssetAttachment $model): bool
    {
        return $user->can('Restore Asset Attachment');
    }

    public function forceDelete(User $user, AssetAttachment $model): bool
    {
        return $user->can('Force Delete Asset Attachment');
    }
}