<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Pricelist;
use Illuminate\Auth\Access\HandlesAuthorization;

class PricelistPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Pricelist');
    }

    public function view(AuthUser $authUser, Pricelist $pricelist): bool
    {
        return $authUser->can('View:Pricelist');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Pricelist');
    }

    public function update(AuthUser $authUser, Pricelist $pricelist): bool
    {
        return $authUser->can('Update:Pricelist');
    }

    public function delete(AuthUser $authUser, Pricelist $pricelist): bool
    {
        return $authUser->can('Delete:Pricelist');
    }

    public function restore(AuthUser $authUser, Pricelist $pricelist): bool
    {
        return $authUser->can('Restore:Pricelist');
    }

    public function forceDelete(AuthUser $authUser, Pricelist $pricelist): bool
    {
        return $authUser->can('ForceDelete:Pricelist');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Pricelist');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Pricelist');
    }

    public function replicate(AuthUser $authUser, Pricelist $pricelist): bool
    {
        return $authUser->can('Replicate:Pricelist');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Pricelist');
    }

}