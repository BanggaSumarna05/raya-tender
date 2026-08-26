<?php

namespace App\Services;

use App\Models\Proposal;
use App\Models\ProposalVersion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProposalService
{
    public function __construct(
        private readonly ActivityLogService $activityLogService
    ) {}

    public function create(array $data): Proposal
    {
        return DB::transaction(function () use ($data) {
            $data['created_by']      = Auth::id();
            $data['updated_by']      = Auth::id();
            $data['code']            = $this->generateCode();
            $data['current_version'] = 1;

            $proposal = Proposal::create($data);

            ProposalVersion::create([
                'proposal_id'     => $proposal->id,
                'version_number'  => 1,
                'revision_number' => 0,
                'change_notes'    => 'Versi awal',
                'title'           => $proposal->title,
                'description'     => $proposal->description,
                'bid_value'       => $proposal->bid_value,
                'deadline'        => $proposal->deadline,
                'valid_until'     => $proposal->valid_until,
                'status'          => $proposal->status->value,
                'created_by'      => Auth::id(),
                'created_at'      => now(),
            ]);

            $this->activityLogService->log(
                'CREATE_PROPOSAL',
                'Proposal',
                $proposal->id,
                Proposal::class,
                "Membuat proposal: {$proposal->title}",
                [
                    'code'          => $proposal->code,
                    'tender_id'     => $proposal->tender_id,
                    'pic_id'        => $proposal->pic_id,
                    'backup_pic_id' => $proposal->backup_pic_id,
                ]
            );

            return $proposal;
        });
    }

    public function update(Proposal $proposal, array $data, bool $newVersion = false): Proposal
    {
        return DB::transaction(function () use ($proposal, $data, $newVersion) {
            // Snapshot before state
            $before = ActivityLogService::proposalSnapshot($proposal);

            $data['updated_by'] = Auth::id();

            if ($newVersion) {
                $versionNumber  = $proposal->current_version + 1;
                $revisionNumber = $proposal->versions()->max('revision_number') + 1;
                $data['current_version'] = $versionNumber;

                $snapshotTitle      = $data['title']       ?? $proposal->title;
                $snapshotDescription = $data['description'] ?? $proposal->description;
                $snapshotBidValue   = $data['bid_value']   ?? $proposal->bid_value;
                $snapshotDeadline   = $data['deadline']    ?? $proposal->deadline;
                $snapshotValidUntil = $data['valid_until'] ?? $proposal->valid_until;
                $snapshotStatus     = $data['status']      ?? $proposal->status->value;

                ProposalVersion::create([
                    'proposal_id'     => $proposal->id,
                    'version_number'  => $versionNumber,
                    'revision_number' => $revisionNumber,
                    'change_notes'    => $data['version_notes'] ?? null,
                    'title'           => $snapshotTitle,
                    'description'     => $snapshotDescription,
                    'bid_value'       => $snapshotBidValue,
                    'deadline'        => $snapshotDeadline,
                    'valid_until'     => $snapshotValidUntil,
                    'status'          => is_object($snapshotStatus) ? $snapshotStatus->value : $snapshotStatus,
                    'created_by'      => Auth::id(),
                    'created_at'      => now(),
                ]);

                $this->activityLogService->log(
                    'CREATE_PROPOSAL_VERSION',
                    'Proposal',
                    $proposal->id,
                    Proposal::class,
                    "Membuat revisi V{$versionNumber} proposal: {$proposal->title}",
                    ['version' => $versionNumber, 'revision' => $revisionNumber, 'code' => $proposal->code]
                );
            }

            $proposal->update($data);
            $after = ActivityLogService::proposalSnapshot($proposal->fresh());

            $this->activityLogService->logWithDiff(
                'UPDATE_PROPOSAL',
                'Proposal',
                $proposal,
                $before,
                $after,
                "Memperbarui proposal: {$proposal->title}",
                ['code' => $proposal->code]
            );

            return $proposal->fresh();
        });
    }

    /**
     * Create a new revision snapshot.
     */
    public function createRevision(Proposal $proposal, string $revisionNote): Proposal
    {
        return DB::transaction(function () use ($proposal, $revisionNote) {
            $nextVersion  = $proposal->current_version + 1;
            $nextRevision = ($proposal->versions()->max('revision_number') ?? 0) + 1;

            ProposalVersion::create([
                'proposal_id'     => $proposal->id,
                'version_number'  => $nextVersion,
                'revision_number' => $nextRevision,
                'change_notes'    => $revisionNote,
                'title'           => $proposal->title,
                'description'     => $proposal->description,
                'bid_value'       => $proposal->bid_value,
                'deadline'        => $proposal->deadline,
                'valid_until'     => $proposal->valid_until,
                'status'          => $proposal->status->value,
                'created_by'      => Auth::id(),
                'created_at'      => now(),
            ]);

            $proposal->update([
                'current_version' => $nextVersion,
                'updated_by'      => Auth::id(),
            ]);

            $this->activityLogService->log(
                'CREATE_PROPOSAL_VERSION',
                'Proposal',
                $proposal->id,
                Proposal::class,
                "Membuat revisi V{$nextVersion} untuk proposal: {$proposal->title}",
                [
                    'version'       => $nextVersion,
                    'revision'      => $nextRevision,
                    'revision_note' => $revisionNote,
                    'code'          => $proposal->code,
                ]
            );

            return $proposal->fresh();
        });
    }

    public function delete(Proposal $proposal): bool
    {
        return DB::transaction(function () use ($proposal) {
            $this->activityLogService->log(
                'DELETE_PROPOSAL',
                'Proposal',
                $proposal->id,
                Proposal::class,
                "Menghapus proposal: {$proposal->title}",
                ['code' => $proposal->code, 'status' => $proposal->status->value]
            );

            return $proposal->delete();
        });
    }

    /**
     * Duplicate a proposal — creates a brand new proposal (different code).
     * NOT a revision. Does NOT copy bid_value (financial) or versions/history.
     *
     * Copied  : tender_id, title, pic_id, backup_pic_id, description, notes
     * Reset   : code (new), status → draft, bid_value → null, deadline → null,
     *           current_version → 1, submitted_at → null, created_by/updated_by
     */
    public function duplicate(Proposal $proposal): Proposal
    {
        return DB::transaction(function () use ($proposal) {
            $newProposal = Proposal::create([
                'tender_id'      => $proposal->tender_id,
                'code'           => $this->generateCode(),
                'title'          => $proposal->title . ' (Duplikat)',
                'pic_id'         => $proposal->pic_id,
                'backup_pic_id'  => $proposal->backup_pic_id,
                'description'    => $proposal->description,
                'notes'          => $proposal->notes,
                'bid_value'      => null,      // financial data NOT copied
                'status'         => 'draft',
                'current_version'=> 1,
                'deadline'       => null,
                'valid_until'    => null,
                'submitted_at'   => null,
                'created_by'     => Auth::id(),
                'updated_by'     => Auth::id(),
            ]);

            // Create initial version snapshot for the new proposal
            ProposalVersion::create([
                'proposal_id'     => $newProposal->id,
                'version_number'  => 1,
                'revision_number' => 0,
                'change_notes'    => "Diduplikat dari proposal {$proposal->code}",
                'title'           => $newProposal->title,
                'description'     => $newProposal->description,
                'bid_value'       => null,
                'deadline'        => null,
                'valid_until'     => null,
                'status'          => 'draft',
                'created_by'      => Auth::id(),
                'created_at'      => now(),
            ]);

            $this->activityLogService->log(
                'DUPLICATE_PROPOSAL',
                'Proposal',
                $newProposal->id,
                Proposal::class,
                "Menduplikat proposal {$proposal->code} → {$newProposal->code}",
                ['source_code' => $proposal->code, 'new_code' => $newProposal->code]
            );

            return $newProposal;
        });
    }

    private function generateCode(): string
    {
        $year     = now()->year;
        $prefix   = "PR-{$year}-";
        $lastCode = Proposal::withTrashed()
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
