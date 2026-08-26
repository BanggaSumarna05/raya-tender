<?php

namespace App\Policies;

use App\Models\Tender;
use App\Models\User;

class TenderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_tenders');
    }

    public function view(User $user, Tender $tender): bool
    {
        return $user->can('view_tenders');
    }

    public function create(User $user): bool
    {
        return $user->can('create_tenders');
    }

    public function update(User $user, Tender $tender): bool
    {
        return $user->can('update_tenders');
    }

    public function delete(User $user, Tender $tender): bool
    {
        return $user->can('delete_tenders');
    }

    public function export(User $user): bool
    {
        return $user->can('export_tenders');
    }
}
