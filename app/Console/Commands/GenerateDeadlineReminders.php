<?php

namespace App\Console\Commands;

use App\Services\ReminderService;
use Illuminate\Console\Command;

class GenerateDeadlineReminders extends Command
{
    protected $signature   = 'reminders:generate {--dry-run : Preview reminders without saving}';
    protected $description = 'Generate deadline reminders for tenders and proposals (H-7, H-3, H-1, Hari-H, Overdue)';

    public function handle(ReminderService $reminderService): int
    {
        $this->info('Generating deadline reminders...');

        if ($this->option('dry-run')) {
            $this->warn('DRY RUN — no reminders will be saved.');
        }

        if (!$this->option('dry-run')) {
            $tenderCount   = $reminderService->generateTenderReminders();
            $proposalCount = $reminderService->generateProposalReminders();
        } else {
            $tenderCount   = 0;
            $proposalCount = 0;
        }

        $this->table(
            ['Type', 'Reminders Created'],
            [
                ['Tender',   $tenderCount],
                ['Proposal', $proposalCount],
                ['Total',    $tenderCount + $proposalCount],
            ]
        );

        $this->info('Done.');

        return Command::SUCCESS;
    }
}
