<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\QuoteWarehouse;
use Illuminate\Auth\Access\HandlesAuthorization;

class QuoteWarehousePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:QuoteWarehouse');
    }

    public function view(AuthUser $authUser, QuoteWarehouse $quoteWarehouse): bool
    {
        return $authUser->can('View:QuoteWarehouse');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:QuoteWarehouse');
    }

    public function update(AuthUser $authUser, QuoteWarehouse $quoteWarehouse): bool
    {
        return $authUser->can('Update:QuoteWarehouse');
    }

    public function delete(AuthUser $authUser, QuoteWarehouse $quoteWarehouse): bool
    {
        return $authUser->can('Delete:QuoteWarehouse');
    }

    public function restore(AuthUser $authUser, QuoteWarehouse $quoteWarehouse): bool
    {
        return $authUser->can('Restore:QuoteWarehouse');
    }

    public function forceDelete(AuthUser $authUser, QuoteWarehouse $quoteWarehouse): bool
    {
        return $authUser->can('ForceDelete:QuoteWarehouse');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:QuoteWarehouse');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:QuoteWarehouse');
    }

    public function replicate(AuthUser $authUser, QuoteWarehouse $quoteWarehouse): bool
    {
        return $authUser->can('Replicate:QuoteWarehouse');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:QuoteWarehouse');
    }

}