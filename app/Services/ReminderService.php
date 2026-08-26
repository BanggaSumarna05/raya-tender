<?php

namespace App\Services;

use App\Models\Reminder;
use App\Models\Tender;
use App\Models\Proposal;
use App\Models\User;

/**
 * ReminderService — creates in-app reminders for deadline events.
 *
 * Duplicate prevention:
 *   Each reminder has an event_key (e.g. "tender_1_h7").
 *   Before inserting, we check if a reminder with the same event_key
 *   already exists for that user. If it does, skip.
 *
 * This service is intended to be called by a scheduled command (e.g. daily),
 * not inline during HTTP requests.
 *
 * Events generated:
 *   h7      → 7 days before deadline
 *   h3      → 3 days before deadline
 *   h1      → 1 day before deadline
 *   due     → deadline day (0 days remaining)
 *   overdue → past deadline
 *
 * Targets:
 *   - PIC of the tender/proposal
 *   - Backup PIC (if set)
 *
 * Tenders/proposals in Won/Lost/Completed/Cancelled state are skipped.
 */
class ReminderService
{
    private const FINAL_STATUSES = ['won', 'lost', 'completed', 'cancelled'];

    /** Reminder types keyed by event name */
    private const EVENTS = [
        'h7'      => ['days' => 7,  'label' => 'H-7',    'type' => 'deadline'],
        'h3'      => ['days' => 3,  'label' => 'H-3',    'type' => 'deadline'],
        'h1'      => ['days' => 1,  'label' => 'H-1',    'type' => 'deadline'],
        'due'     => ['days' => 0,  'label' => 'Hari H', 'type' => 'deadline'],
        'overdue' => ['days' => -1, 'label' => 'Overdue','type' => 'deadline'],
    ];

    /**
     * Generate deadline reminders for all active tenders.
     * Returns the number of reminders created.
     */
    public function generateTenderReminders(): int
    {
        $created = 0;

        $tenders = Tender::whereNotIn('status', self::FINAL_STATUSES)
            ->whereNotNull('submission_deadline')
            ->with(['pic', 'backupPic'])
            ->get();

        foreach ($tenders as $tender) {
            $days = $tender->days_until_deadline;

            if ($days === null) {
                continue;
            }

            foreach (self::EVENTS as $event => $config) {
                if ($event === 'overdue' && $days >= 0) {
                    continue;
                }

                if ($event !== 'overdue' && $days !== $config['days']) {
                    continue;
                }

                $targets = $this->getTargetUsers($tender->pic, $tender->backupPic);

                foreach ($targets as $user) {
                    $eventKey = "tender_{$tender->id}_{$event}_{$user->id}";

                    if ($this->reminderAlreadySent($eventKey)) {
                        continue;
                    }

                    Reminder::create([
                        'tender_id'   => $tender->id,
                        'proposal_id' => null,
                        'user_id'     => $user->id,
                        'title'       => "[{$config['label']}] Deadline: {$tender->title}",
                        'description' => "Tender {$tender->code} memiliki deadline pada "
                            . $tender->submission_deadline->format('d M Y')
                            . ". Status: {$tender->status->label()}.",
                        'reminder_at' => now(),
                        'type'        => 'deadline',
                        'status'      => 'pending',
                        'event_key'   => $eventKey,
                        'sent_at'     => now(),
                    ]);

                    $created++;
                }
            }
        }

        return $created;
    }

    /**
     * Generate deadline reminders for all active proposals.
     */
    public function generateProposalReminders(): int
    {
        $created = 0;

        $finalProposalStatuses = ['won', 'lost', 'cancelled'];

        $proposals = Proposal::whereNotIn('status', $finalProposalStatuses)
            ->whereNotNull('deadline')
            ->with(['pic', 'backupPic', 'tender'])
            ->get();

        foreach ($proposals as $proposal) {
            $days = $proposal->days_until_deadline;

            if ($days === null) {
                continue;
            }

            foreach (self::EVENTS as $event => $config) {
                if ($event === 'overdue' && $days >= 0) {
                    continue;
                }

                if ($event !== 'overdue' && $days !== $config['days']) {
                    continue;
                }

                $targets = $this->getTargetUsers($proposal->pic, $proposal->backupPic);

                foreach ($targets as $user) {
                    $eventKey = "proposal_{$proposal->id}_{$event}_{$user->id}";

                    if ($this->reminderAlreadySent($eventKey)) {
                        continue;
                    }

                    Reminder::create([
                        'tender_id'   => $proposal->tender_id,
                        'proposal_id' => $proposal->id,
                        'user_id'     => $user->id,
                        'title'       => "[{$config['label']}] Deadline Proposal: {$proposal->title}",
                        'description' => "Proposal {$proposal->code} ({$proposal->tender?->code}) memiliki deadline pada "
                            . $proposal->deadline->format('d M Y')
                            . ". Status: {$proposal->status->label()}.",
                        'reminder_at' => now(),
                        'type'        => 'deadline',
                        'status'      => 'pending',
                        'event_key'   => $eventKey,
                        'sent_at'     => now(),
                    ]);

                    $created++;
                }
            }
        }

        return $created;
    }

    /**
     * Get pending reminders for a user (for notification display).
     */
    public function getPendingForUser(int $userId, int $limit = 20): \Illuminate\Database\Eloquent\Collection
    {
        return Reminder::where('user_id', $userId)
            ->where('status', 'pending')
            ->with(['tender', 'proposal'])
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Mark a reminder as completed/dismissed.
     */
    public function markRead(Reminder $reminder): void
    {
        $reminder->update(['status' => 'completed']);
    }

    /**
     * Check if a reminder with this event_key has already been sent.
     */
    private function reminderAlreadySent(string $eventKey): bool
    {
        return Reminder::where('event_key', $eventKey)->exists();
    }

    /**
     * Collect unique non-null target users: PIC + Backup PIC.
     *
     * @return User[]
     */
    private function getTargetUsers(?User $pic, ?User $backupPic): array
    {
        $targets = [];

        if ($pic) {
            $targets[$pic->id] = $pic;
        }

        if ($backupPic && !isset($targets[$backupPic->id])) {
            $targets[$backupPic->id] = $backupPic;
        }

        return array_values($targets);
    }
}
