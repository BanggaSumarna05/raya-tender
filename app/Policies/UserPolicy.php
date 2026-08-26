<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_users');
    }

    public function view(User $user, User $model): bool
    {
        return $user->can('view_users');
    }

    public function create(User $user): bool
    {
        return $user->can('create_users');
    }

    public function update(User $user, User $model): bool
    {
        if (!$user->can('update_users')) {
            return false;
        }

        // Super Admin hanya boleh diupdate oleh Super Admin lain
        if ($model->hasRole('Super Admin') && !$user->hasRole('Super Admin')) {
            return false;
        }

        return true;
    }

    public function delete(User $user, User $model): bool
    {
        // Tidak boleh hapus diri sendiri
        if ($user->id === $model->id) {
            return false;
        }

        // Super Admin tidak bisa dihapus oleh siapapun
        if ($model->hasRole('Super Admin')) {
            return false;
        }

        return $user->can('delete_users');
    }

    public function toggleStatus(User $user, User $model): bool
    {
        // Tidak boleh nonaktifkan diri sendiri
        if ($user->id === $model->id) {
            return false;
        }

        // Super Admin tidak bisa dinonaktifkan oleh siapapun
        if ($model->hasRole('Super Admin')) {
            return false;
        }

        return $user->can('update_users');
    }
}
