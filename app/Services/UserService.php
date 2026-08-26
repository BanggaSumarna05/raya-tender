<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function __construct(
        private readonly ActivityLogService $activityLogService
    ) {}

    public function create(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'username' => $data['username'] ?? null,
                'phone'    => $data['phone'] ?? null,
                'password' => Hash::make($data['password']),
                'status'   => $data['status'],
            ]);

            $user->assignRole($data['role']);

            $this->activityLogService->log(
                'CREATE_USER',
                'User',
                $user->id,
                User::class,
                "Membuat pengguna: {$user->name} ({$data['role']})"
            );

            return $user;
        });
    }

    public function update(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            $updateData = [
                'name'     => $data['name'],
                'email'    => $data['email'],
                'username' => $data['username'] ?? null,
                'phone'    => $data['phone'] ?? null,
                'status'   => $data['status'],
            ];

            if (!empty($data['password'])) {
                $updateData['password'] = Hash::make($data['password']);
            }

            $user->update($updateData);

            // Sync role
            $user->syncRoles([$data['role']]);

            $this->activityLogService->log(
                'UPDATE_USER',
                'User',
                $user->id,
                User::class,
                "Memperbarui pengguna: {$user->name}"
            );

            return $user->fresh();
        });
    }

    public function delete(User $user): bool
    {
        return DB::transaction(function () use ($user) {
            $this->activityLogService->log(
                'DELETE_USER',
                'User',
                $user->id,
                User::class,
                "Menghapus pengguna: {$user->name}"
            );

            return $user->delete();
        });
    }

    public function toggleStatus(User $user): User
    {
        $newStatus = $user->status->value === 'active' ? 'inactive' : 'active';
        $user->update(['status' => $newStatus]);

        $this->activityLogService->log(
            'UPDATE_USER',
            'User',
            $user->id,
            User::class,
            "Mengubah status pengguna: {$user->name} → {$newStatus}"
        );

        return $user->fresh();
    }

    public function resetPassword(User $user, string $password): bool
    {
        $result = $user->update(['password' => Hash::make($password)]);

        $this->activityLogService->log(
            'RESET_PASSWORD',
            'User',
            $user->id,
            User::class,
            "Reset password pengguna: {$user->name}"
        );

        return $result;
    }
}
