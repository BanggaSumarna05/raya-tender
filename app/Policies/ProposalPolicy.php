<?php

namespace App\Policies;

use App\Models\Proposal;
use App\Models\User;

class ProposalPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_proposals');
    }

    public function view(User $user, Proposal $proposal): bool
    {
        return $user->can('view_proposals');
    }

    public function create(User $user): bool
    {
        return $user->can('create_proposals');
    }

    public function update(User $user, Proposal $proposal): bool
    {
        return $user->can('update_proposals');
    }

    public function delete(User $user, Proposal $proposal): bool
    {
        return $user->can('delete_proposals');
    }
}
