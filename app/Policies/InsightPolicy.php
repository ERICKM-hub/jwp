<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Insight;
use Illuminate\Auth\Access\HandlesAuthorization;

class InsightPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Insight');
    }

    public function view(AuthUser $authUser, Insight $insight): bool
    {
        return $authUser->can('View:Insight');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Insight');
    }

    public function update(AuthUser $authUser, Insight $insight): bool
    {
        return $authUser->can('Update:Insight');
    }

    public function delete(AuthUser $authUser, Insight $insight): bool
    {
        return $authUser->can('Delete:Insight');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Insight');
    }

    public function restore(AuthUser $authUser, Insight $insight): bool
    {
        return $authUser->can('Restore:Insight');
    }

    public function forceDelete(AuthUser $authUser, Insight $insight): bool
    {
        return $authUser->can('ForceDelete:Insight');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Insight');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Insight');
    }

    public function replicate(AuthUser $authUser, Insight $insight): bool
    {
        return $authUser->can('Replicate:Insight');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Insight');
    }

}