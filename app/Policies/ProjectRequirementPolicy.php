<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ProjectRequirement;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectRequirementPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ProjectRequirement');
    }

    public function view(AuthUser $authUser, ProjectRequirement $projectRequirement): bool
    {
        return $authUser->can('View:ProjectRequirement');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ProjectRequirement');
    }

    public function update(AuthUser $authUser, ProjectRequirement $projectRequirement): bool
    {
        return $authUser->can('Update:ProjectRequirement');
    }

    public function delete(AuthUser $authUser, ProjectRequirement $projectRequirement): bool
    {
        return $authUser->can('Delete:ProjectRequirement');
    }

    public function restore(AuthUser $authUser, ProjectRequirement $projectRequirement): bool
    {
        return $authUser->can('Restore:ProjectRequirement');
    }

    public function forceDelete(AuthUser $authUser, ProjectRequirement $projectRequirement): bool
    {
        return $authUser->can('ForceDelete:ProjectRequirement');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ProjectRequirement');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ProjectRequirement');
    }

    public function replicate(AuthUser $authUser, ProjectRequirement $projectRequirement): bool
    {
        return $authUser->can('Replicate:ProjectRequirement');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ProjectRequirement');
    }

}