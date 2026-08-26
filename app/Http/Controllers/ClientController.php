<?php

namespace App\Http\Controllers;

use App\Http\Concerns\ValidatesPerPage;
use App\Http\Requests\Client\StoreClientRequest;
use App\Http\Requests\Client\UpdateClientRequest;
use App\Models\Client;
use App\Services\ClientService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ClientController extends Controller
{
    use ValidatesPerPage;
    public function __construct(
        private readonly ClientService $clientService
    ) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Client::class);

        $sortField = in_array($request->sort, ['created_at','name','city','industry','tenders_count'])
                     ? $request->sort : 'created_at';
        $sortDir   = $request->direction === 'asc' ? 'asc' : 'desc';

        $clients = Client::withCount('tenders')
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('code', 'like', "%{$request->search}%")
                ->orWhere('city', 'like', "%{$request->search}%"))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->industry, fn($q) => $q->where('industry', $request->industry))
            ->orderBy($sortField === 'tenders_count' ? 'tenders_count' : $sortField, $sortDir)
            ->paginate($this->getPerPage($request, 15))
            ->withQueryString();

        return view('clients.index', compact('clients', 'sortField', 'sortDir'));
    }

    public function create()
    {
        $this->authorize('create', Client::class);

        return view('clients.create');
    }

    public function store(StoreClientRequest $request)
    {
        try {
            $client = $this->clientService->create($request->validated());

            return redirect()
                ->route('clients.show', $client)
                ->with('success', "Klien {$client->name} berhasil dibuat.");
        } catch (\Throwable $e) {
            Log::error('Failed to create client', ['error' => $e->getMessage()]);

            return back()->withInput()->with('error', 'Terjadi kesalahan. Silakan coba kembali.');
        }
    }

    public function show(Client $client)
    {
        $this->authorize('view', $client);

        $client->load(['tenders' => fn($q) => $q->with(['category', 'pic'])->latest()->limit(10)]);

        $stats = [
            'total_tenders' => $client->tenders()->count(),
            'won'           => $client->tenders()->where('status', 'won')->count(),
            'lost'          => $client->tenders()->where('status', 'lost')->count(),
            'active'        => $client->tenders()->whereIn('status', \App\Enums\TenderStatus::activeStatuses())->count(),
            'total_value'   => $client->tenders()->sum('estimated_value'),
        ];

        $won  = $stats['won'];
        $lost = $stats['lost'];
        $stats['win_rate'] = ($won + $lost) > 0 ? round(($won / ($won + $lost)) * 100, 1) : 0;

        return view('clients.show', compact('client', 'stats'));
    }

    public function edit(Client $client)
    {
        $this->authorize('update', $client);

        return view('clients.edit', compact('client'));
    }

    public function update(UpdateClientRequest $request, Client $client)
    {
        try {
            $this->clientService->update($client, $request->validated());

            return redirect()
                ->route('clients.show', $client)
                ->with('success', 'Klien berhasil diperbarui.');
        } catch (\Throwable $e) {
            Log::error('Failed to update client', ['client_id' => $client->id, 'error' => $e->getMessage()]);

            return back()->withInput()->with('error', 'Terjadi kesalahan. Silakan coba kembali.');
        }
    }

    public function destroy(Client $client)
    {
        $this->authorize('delete', $client);

        try {
            if ($client->tenders()->exists()) {
                return back()->with('error', 'Klien tidak dapat dihapus karena masih memiliki tender.');
            }

            $this->clientService->delete($client);

            return redirect()->route('clients.index')->with('success', 'Klien berhasil dihapus.');
        } catch (\Throwable $e) {
            Log::error('Failed to delete client', ['client_id' => $client->id, 'error' => $e->getMessage()]);

            return back()->with('error', 'Terjadi kesalahan. Silakan coba kembali.');
        }
    }

    /**
     * Restore a soft-deleted client (Super Admin only).
     */
    public function restore(int $id)
    {
        $client = Client::withTrashed()->findOrFail($id);
        $client->restore();

        return back()->with('success', "Klien {$client->name} berhasil dipulihkan.");
    }

    /**
     * Permanently delete a soft-deleted client (Super Admin only).
     */
    public function forceDelete(int $id)
    {
        $client = Client::withTrashed()->findOrFail($id);
        $client->forceDelete();

        return back()->with('success', "Klien {$client->name} telah dihapus permanen.");
    }
}
