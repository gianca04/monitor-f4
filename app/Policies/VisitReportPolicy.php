<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\VisitReport;
use Illuminate\Auth\Access\HandlesAuthorization;

class VisitReportPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:VisitReport');
    }

    public function view(AuthUser $authUser, VisitReport $visitReport): bool
    {
        return $authUser->can('View:VisitReport');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:VisitReport');
    }

    public function update(AuthUser $authUser, VisitReport $visitReport): bool
    {
        return $authUser->can('Update:VisitReport');
    }

    public function delete(AuthUser $authUser, VisitReport $visitReport): bool
    {
        return $authUser->can('Delete:VisitReport');
    }

    public function restore(AuthUser $authUser, VisitReport $visitReport): bool
    {
        return $authUser->can('Restore:VisitReport');
    }

    public function forceDelete(AuthUser $authUser, VisitReport $visitReport): bool
    {
        return $authUser->can('ForceDelete:VisitReport');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:VisitReport');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:VisitReport');
    }

    public function replicate(AuthUser $authUser, VisitReport $visitReport): bool
    {
        return $authUser->can('Replicate:VisitReport');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:VisitReport');
    }

}