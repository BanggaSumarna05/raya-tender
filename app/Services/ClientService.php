<?php

namespace App\Services;

use App\Models\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClientService
{
    public function __construct(
        private readonly ActivityLogService $activityLogService
    ) {}

    public function create(array $data): Client
    {
        return DB::transaction(function () use ($data) {
            $data['code'] = $this->generateCode();

            $client = Client::create($data);

            $this->activityLogService->log(
                'CREATE_CLIENT',
                'Client',
                $client->id,
                Client::class,
                "Membuat klien: {$client->name}",
                ['code' => $client->code]
            );

            return $client;
        });
    }

    public function update(Client $client, array $data): Client
    {
        return DB::transaction(function () use ($client, $data) {
            $client->update($data);

            $this->activityLogService->log(
                'UPDATE_CLIENT',
                'Client',
                $client->id,
                Client::class,
                "Memperbarui klien: {$client->name}"
            );

            return $client->fresh();
        });
    }

    public function delete(Client $client): bool
    {
        return DB::transaction(function () use ($client) {
            $this->activityLogService->log(
                'DELETE_CLIENT',
                'Client',
                $client->id,
                Client::class,
                "Menghapus klien: {$client->name}"
            );

            return $client->delete();
        });
    }

    private function generateCode(): string
    {
        $lastCode = Client::withTrashed()
            ->where('code', 'like', 'CLT-%')
            ->orderByDesc('code')
            ->value('code');

        if ($lastCode) {
            $lastNumber = (int) substr($lastCode, 4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return 'CLT-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
