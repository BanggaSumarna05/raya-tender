<?php

namespace App\Services;

use App\Enums\TenderStatus;
use App\Models\Tender;
use App\Models\TenderStatusHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TenderService
{
    public function __construct(
        private readonly ActivityLogService $activityLogService
    ) {}

    public function create(array $data): Tender
    {
        return DB::transaction(function () use ($data) {
            $data['created_by'] = Auth::id();
            $data['updated_by'] = Auth::id();
            $data['code']       = $this->generateCode();

            if (empty($data['status'])) {
                $data['status'] = 'draft';
            }

            $tender = Tender::create($data);

            TenderStatusHistory::create([
                'tender_id'   => $tender->id,
                'from_status' => null,
                'to_status'   => $tender->status->value,
                'notes'       => 'Tender dibuat',
                'changed_by'  => Auth::id(),
                'created_at'  => now(),
            ]);

            $this->activityLogService->log(
                'CREATE_TENDER',
                'Tender',
                $tender->id,
                Tender::class,
                "Membuat tender: {$tender->title}",
                [
                    'code'        => $tender->code,
                    'status'      => $tender->status->value,
                    'pic_id'      => $tender->pic_id,
                    'backup_pic_id' => $tender->backup_pic_id,
                ]
            );

            return $tender;
        });
    }

    public function update(Tender $tender, array $data): Tender
    {
        return DB::transaction(function () use ($tender, $data) {
            // Snapshot before state for diff logging
            $before = ActivityLogService::tenderSnapshot($tender);

            $oldStatus = $tender->status->value;
            $data['updated_by'] = Auth::id();

            if (isset($data['status']) && $data['status'] !== $oldStatus) {
                $this->validateTransition($tender, $data['status']);
            }

            $tender->update($data);
            $tender->refresh();

            $newStatus = $tender->status->value;

            if ($oldStatus !== $newStatus) {
                TenderStatusHistory::create([
                    'tender_id'   => $tender->id,
                    'from_status' => $oldStatus,
                    'to_status'   => $newStatus,
                    'notes'       => $data['status_notes'] ?? null,
                    'changed_by'  => Auth::id(),
                    'created_at'  => now(),
                ]);
            }

            // After snapshot and diff log
            $after = ActivityLogService::tenderSnapshot($tender);

            $this->activityLogService->logWithDiff(
                'UPDATE_TENDER',
                'Tender',
                $tender,
                $before,
                $after,
                "Memperbarui tender: {$tender->title}",
                ['code' => $tender->code]
            );

            return $tender;
        });
    }

    public function delete(Tender $tender): bool
    {
        return DB::transaction(function () use ($tender) {
            $this->activityLogService->log(
                'DELETE_TENDER',
                'Tender',
                $tender->id,
                Tender::class,
                "Menghapus tender: {$tender->title}",
                ['code' => $tender->code, 'status' => $tender->status->value]
            );

            return $tender->delete();
        });
    }

    /**
     * Change tender status with transition validation.
     *
     * @throws \InvalidArgumentException if transition is not allowed
     */
    public function changeStatus(Tender $tender, string $newStatus, ?string $notes = null): Tender
    {
        return DB::transaction(function () use ($tender, $newStatus, $notes) {
            $oldStatus = $tender->status->value;

            $this->validateTransition($tender, $newStatus);

            $updateData = ['status' => $newStatus, 'updated_by' => Auth::id()];

            if ($newStatus === 'submitted') {
                $updateData['submitted_at'] = now();
            }

            $tender->update($updateData);

            TenderStatusHistory::create([
                'tender_id'   => $tender->id,
                'from_status' => $oldStatus,
                'to_status'   => $newStatus,
                'notes'       => $notes,
                'changed_by'  => Auth::id(),
                'created_at'  => now(),
            ]);

            $this->activityLogService->log(
                'CHANGE_TENDER_STATUS',
                'Tender',
                $tender->id,
                Tender::class,
                "Mengubah status tender dari {$oldStatus} ke {$newStatus}",
                [
                    'code'       => $tender->code,
                    'changes'    => [
                        'status' => ['before' => $oldStatus, 'after' => $newStatus],
                    ],
                    'notes'      => $notes,
                ]
            );

            return $tender->fresh();
        });
    }

    /**
     * Duplicate a tender — creates a new tender copying non-sensitive fields.
     *
     * Copied  : client_id, category_id, title, description, source, source_reference,
     *           pic_id, backup_pic_id, location, priority, requirements
     * Reset   : code (auto-generated), status → draft, deadline, project dates,
     *           estimated_value, created_by, updated_by, created_at, updated_at
     * Excluded: proposals, documents, status histories, activity logs
     */
    public function duplicate(Tender $tender): Tender
    {
        return DB::transaction(function () use ($tender) {
            $newTender = Tender::create([
                'client_id'        => $tender->client_id,
                'category_id'      => $tender->category_id,
                'code'             => $this->generateCode(),
                'title'            => $tender->title . ' (Duplikat)',
                'description'      => $tender->description,
                'source'           => $tender->source,
                'source_reference' => $tender->source_reference,
                'pic_id'           => $tender->pic_id,
                'backup_pic_id'    => $tender->backup_pic_id,
                'location'         => $tender->location,
                'priority'         => $tender->priority->value,
                'requirements'     => $tender->requirements,
                'notes'            => $tender->notes,
                'status'           => 'draft',            // always reset to draft
                'estimated_value'  => null,               // financial data NOT copied
                'received_date'    => null,               // dates reset
                'submission_deadline' => null,
                'project_start_date'  => null,
                'project_end_date'    => null,
                'created_by'       => Auth::id(),
                'updated_by'       => Auth::id(),
            ]);

            TenderStatusHistory::create([
                'tender_id'   => $newTender->id,
                'from_status' => null,
                'to_status'   => 'draft',
                'notes'       => "Diduplikat dari tender {$tender->code}",
                'changed_by'  => Auth::id(),
                'created_at'  => now(),
            ]);

            $this->activityLogService->log(
                'DUPLICATE_TENDER',
                'Tender',
                $newTender->id,
                Tender::class,
                "Menduplikasi tender {$tender->code} → {$newTender->code}",
                ['source_code' => $tender->code, 'new_code' => $newTender->code]
            );

            return $newTender;
        });
    }

    /**
     * Validate that the transition from current status to new status is allowed.
     *
     * @throws \InvalidArgumentException
     */
    private function validateTransition(Tender $tender, string $newStatus): void
    {
        $currentStatus = $tender->status;

        if (!$currentStatus->canTransitionTo($newStatus)) {
            $allowed = TenderStatus::allowedTransitions()[$currentStatus->value] ?? [];
            $allowedLabels = empty($allowed)
                ? 'tidak ada (status final)'
                : implode(', ', $allowed);

            throw new \InvalidArgumentException(
                "Transisi status tidak diizinkan: {$currentStatus->value} → {$newStatus}. " .
                "Status yang diizinkan dari {$currentStatus->value}: {$allowedLabels}."
            );
        }
    }

    private function generateCode(): string
    {
        $year     = now()->year;
        $prefix   = "TR-{$year}-";
        $lastCode = Tender::withTrashed()
            ->where('code', 'like', "{$prefix}%")
            ->orderByDesc('code')
            ->value('code');

        if ($lastCode) {
            $lastNumber = (int) substr($lastCode, strlen($prefix));
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
