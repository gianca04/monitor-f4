<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Compliance;
use Illuminate\Auth\Access\HandlesAuthorization;

class CompliancePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Compliance');
    }

    public function view(AuthUser $authUser, Compliance $compliance): bool
    {
        return $authUser->can('View:Compliance');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Compliance');
    }

    public function update(AuthUser $authUser, Compliance $compliance): bool
    {
        return $authUser->can('Update:Compliance');
    }

    public function delete(AuthUser $authUser, Compliance $compliance): bool
    {
        return $authUser->can('Delete:Compliance');
    }

    public function restore(AuthUser $authUser, Compliance $compliance): bool
    {
        return $authUser->can('Restore:Compliance');
    }

    public function forceDelete(AuthUser $authUser, Compliance $compliance): bool
    {
        return $authUser->can('ForceDelete:Compliance');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Compliance');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Compliance');
    }

    public function replicate(AuthUser $authUser, Compliance $compliance): bool
    {
        return $authUser->can('Replicate:Compliance');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Compliance');
    }

}