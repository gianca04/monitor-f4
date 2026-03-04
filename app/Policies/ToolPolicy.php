<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Tool;
use Illuminate\Auth\Access\HandlesAuthorization;

class ToolPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Tool');
    }

    public function view(AuthUser $authUser, Tool $tool): bool
    {
        return $authUser->can('View:Tool');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Tool');
    }

    public function update(AuthUser $authUser, Tool $tool): bool
    {
        return $authUser->can('Update:Tool');
    }

    public function delete(AuthUser $authUser, Tool $tool): bool
    {
        return $authUser->can('Delete:Tool');
    }

    public function restore(AuthUser $authUser, Tool $tool): bool
    {
        return $authUser->can('Restore:Tool');
    }

    public function forceDelete(AuthUser $authUser, Tool $tool): bool
    {
        return $authUser->can('ForceDelete:Tool');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Tool');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Tool');
    }

    public function replicate(AuthUser $authUser, Tool $tool): bool
    {
        return $authUser->can('Replicate:Tool');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Tool');
    }

}