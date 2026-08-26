<?php

namespace App\Http\Controllers;

use App\Http\Concerns\ValidatesPerPage;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    use ValidatesPerPage;
    public function __construct(
        private readonly UserService $userService
    ) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $sortField = in_array($request->sort, ['created_at','name','email','last_login_at'])
                     ? $request->sort : 'created_at';
        $sortDir   = $request->direction === 'asc' ? 'asc' : 'desc';

        $users = User::with('roles')
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('email', 'like', "%{$request->search}%"))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->role, fn($q) => $q->whereHas('roles', fn($r) => $r->where('name', $request->role)))
            ->orderBy($sortField, $sortDir)
            ->paginate($this->getPerPage($request, 15))
            ->withQueryString();

        $roles = Role::orderBy('name')->get(['id', 'name']);

        return view('users.index', compact('users', 'roles', 'sortField', 'sortDir'));
    }

    public function create()
    {
        $this->authorize('create', User::class);

        $roles = Role::orderBy('name')->get(['id', 'name']);

        return view('users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request)
    {
        try {
            $user = $this->userService->create($request->validated());

            return redirect()
                ->route('settings.users.index')
                ->with('success', "Pengguna {$user->name} berhasil dibuat.");
        } catch (\Throwable $e) {
            Log::error('Failed to create user', ['error' => $e->getMessage()]);

            return back()->withInput()->with('error', 'Terjadi kesalahan. Silakan coba kembali.');
        }
    }

    public function show(User $user)
    {
        $this->authorize('view', $user);

        $user->load('roles');

        $stats = [
            'total_tenders'   => $user->tenders()->count(),
            'active_tenders'  => $user->tenders()->whereIn('status', \App\Enums\TenderStatus::activeStatuses())->count(),
            'won_tenders'     => $user->tenders()->where('status', 'won')->count(),
            'lost_tenders'    => $user->tenders()->where('status', 'lost')->count(),
            'total_proposals' => $user->proposals()->count(),
        ];

        return view('users.show', compact('user', 'stats'));
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);

        $roles = Role::orderBy('name')->get(['id', 'name']);

        return view('users.edit', compact('user', 'roles'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        try {
            $this->userService->update($user, $request->validated());

            return redirect()
                ->route('settings.users.index')
                ->with('success', 'Pengguna berhasil diperbarui.');
        } catch (\Throwable $e) {
            Log::error('Failed to update user', ['user_id' => $user->id, 'error' => $e->getMessage()]);

            return back()->withInput()->with('error', 'Terjadi kesalahan. Silakan coba kembali.');
        }
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        try {
            $this->userService->delete($user);

            return redirect()->route('settings.users.index')->with('success', 'Pengguna berhasil dihapus.');
        } catch (\Throwable $e) {
            Log::error('Failed to delete user', ['user_id' => $user->id, 'error' => $e->getMessage()]);

            return back()->with('error', 'Terjadi kesalahan. Silakan coba kembali.');
        }
    }

    public function toggleStatus(User $user)
    {
        $this->authorize('toggleStatus', $user);

        try {
            $updated = $this->userService->toggleStatus($user);
            $label   = $updated->status->value === 'active' ? 'diaktifkan' : 'dinonaktifkan';

            return back()->with('success', "Pengguna berhasil {$label}.");
        } catch (\Throwable $e) {
            return back()->with('error', 'Terjadi kesalahan. Silakan coba kembali.');
        }
    }
}
