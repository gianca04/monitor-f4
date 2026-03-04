<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Requirement;
use Illuminate\Auth\Access\HandlesAuthorization;

class RequirementPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Requirement');
    }

    public function view(AuthUser $authUser, Requirement $requirement): bool
    {
        return $authUser->can('View:Requirement');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Requirement');
    }

    public function update(AuthUser $authUser, Requirement $requirement): bool
    {
        return $authUser->can('Update:Requirement');
    }

    public function delete(AuthUser $authUser, Requirement $requirement): bool
    {
        return $authUser->can('Delete:Requirement');
    }

    public function restore(AuthUser $authUser, Requirement $requirement): bool
    {
        return $authUser->can('Restore:Requirement');
    }

    public function forceDelete(AuthUser $authUser, Requirement $requirement): bool
    {
        return $authUser->can('ForceDelete:Requirement');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Requirement');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Requirement');
    }

    public function replicate(AuthUser $authUser, Requirement $requirement): bool
    {
        return $authUser->can('Replicate:Requirement');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Requirement');
    }

}