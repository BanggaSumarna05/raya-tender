<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Expand TenderStatus enum to full workflow.
 *
 * Old values:  new | qualification | proposal | submitted | won | lost | no_bid | cancelled
 * New values:  draft | identified | qualification | preparation | submitted |
 *              evaluation | clarification | negotiation | won | lost | completed | cancelled
 *
 * Data migration mapping (safe, no data loss):
 *   new           → identified       (was "new/identified tender")
 *   qualification → qualification    (no change)
 *   proposal      → preparation      (proposal preparation stage)
 *   submitted     → submitted        (no change)
 *   won           → won              (no change)
 *   lost          → lost             (no change)
 *   no_bid        → cancelled        (no_bid is a form of cancellation; closest safe mapping)
 *   cancelled     → cancelled        (no change)
 *
 * Note: 'draft', 'evaluation', 'clarification', 'negotiation', 'completed' are new
 * stages with no old equivalent — they will only appear for new tenders going forward.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Change column from ENUM to VARCHAR so we can freely update values
        // MySQL ENUM changes require ALTER TABLE which can lock the table.
        // We use VARCHAR(50) to avoid enum constraint issues during migration.
        DB::statement("ALTER TABLE tenders MODIFY COLUMN status VARCHAR(50) NOT NULL DEFAULT 'draft'");
        DB::statement("ALTER TABLE tender_status_histories MODIFY COLUMN from_status VARCHAR(50) NULL");
        DB::statement("ALTER TABLE tender_status_histories MODIFY COLUMN to_status VARCHAR(50) NOT NULL");

        // Step 2: Data migration — map old values to new values
        DB::table('tenders')->where('status', 'new')->update(['status' => 'identified']);
        DB::table('tenders')->where('status', 'proposal')->update(['status' => 'preparation']);
        DB::table('tenders')->where('status', 'no_bid')->update(['status' => 'cancelled']);

        // Also update status history records for consistency
        DB::table('tender_status_histories')->where('from_status', 'new')->update(['from_status' => 'identified']);
        DB::table('tender_status_histories')->where('from_status', 'proposal')->update(['from_status' => 'preparation']);
        DB::table('tender_status_histories')->where('from_status', 'no_bid')->update(['from_status' => 'cancelled']);
        DB::table('tender_status_histories')->where('to_status', 'new')->update(['to_status' => 'identified']);
        DB::table('tender_status_histories')->where('to_status', 'proposal')->update(['to_status' => 'preparation']);
        DB::table('tender_status_histories')->where('to_status', 'no_bid')->update(['to_status' => 'cancelled']);

        // Step 3: Re-apply ENUM constraint with new values
        DB::statement("ALTER TABLE tenders MODIFY COLUMN status ENUM(
            'draft','identified','qualification','preparation',
            'submitted','evaluation','clarification','negotiation',
            'won','lost','completed','cancelled'
        ) NOT NULL DEFAULT 'draft'");
    }

    public function down(): void
    {
        // Reverse: convert VARCHAR back, remap values
        DB::statement("ALTER TABLE tenders MODIFY COLUMN status VARCHAR(50) NOT NULL DEFAULT 'new'");

        // Reverse data migration
        DB::table('tenders')->where('status', 'identified')->update(['status' => 'new']);
        DB::table('tenders')->where('status', 'preparation')->update(['status' => 'proposal']);
        DB::table('tenders')->where('status', 'evaluation')->update(['status' => 'submitted']);
        DB::table('tenders')->where('status', 'clarification')->update(['status' => 'submitted']);
        DB::table('tenders')->where('status', 'negotiation')->update(['status' => 'submitted']);
        DB::table('tenders')->where('status', 'completed')->update(['status' => 'won']);
        DB::table('tenders')->where('status', 'draft')->update(['status' => 'new']);

        // Restore ENUM
        DB::statement("ALTER TABLE tenders MODIFY COLUMN status ENUM(
            'new','qualification','proposal','submitted','won','lost','no_bid','cancelled'
        ) NOT NULL DEFAULT 'new'");
    }
};
