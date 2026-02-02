<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\WorkReport;
use Illuminate\Auth\Access\HandlesAuthorization;

class WorkReportPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:WorkReport');
    }

    public function view(AuthUser $authUser, WorkReport $workReport): bool
    {
        return $authUser->can('View:WorkReport');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:WorkReport');
    }

    public function update(AuthUser $authUser, WorkReport $workReport): bool
    {
        return $authUser->can('Update:WorkReport');
    }

    public function delete(AuthUser $authUser, WorkReport $workReport): bool
    {
        return $authUser->can('Delete:WorkReport');
    }

    public function restore(AuthUser $authUser, WorkReport $workReport): bool
    {
        return $authUser->can('Restore:WorkReport');
    }

    public function forceDelete(AuthUser $authUser, WorkReport $workReport): bool
    {
        return $authUser->can('ForceDelete:WorkReport');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:WorkReport');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:WorkReport');
    }

    public function replicate(AuthUser $authUser, WorkReport $workReport): bool
    {
        return $authUser->can('Replicate:WorkReport');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:WorkReport');
    }

}