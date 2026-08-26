<?php

namespace App\Policies;

use App\Models\TenderCategory;
use App\Models\User;

class TenderCategoryPolicy
{
    public function viewAny(User $user): bool { return $user->can('view_categories'); }
    public function view(User $user, TenderCategory $category): bool { return $user->can('view_categories'); }
    public function create(User $user): bool { return $user->can('create_categories'); }
    public function update(User $user, TenderCategory $category): bool { return $user->can('update_categories'); }
    public function delete(User $user, TenderCategory $category): bool { return $user->can('delete_categories'); }
}
