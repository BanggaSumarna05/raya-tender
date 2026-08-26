<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;

/**
 * Migration: Add snapshot columns to proposal_versions table.
 *
 * The permissions (view_financial_data, create_proposal_revisions, change_tender_status)
 * are seeded via RolePermissionSeeder — not here — so this migration only handles schema.
 *
 * Adds to proposal_versions:
 *   - title            (snapshot of proposal title at version time)
 *   - description      (snapshot)
 *   - bid_value        (snapshot — financial, protected)
 *   - deadline         (snapshot)
 *   - valid_until      (snapshot)
 *   - status           (snapshot of proposal status)
 *   - revision_number  (sequential revision label, 0 = Original)
 *   - revision_note    (alias of change_notes, kept for UI clarity)
 *
 * change_notes already exists — kept for backward compatibility.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proposal_versions', function (Blueprint $table) {
            // Snapshot fields — added after existing columns
            $table->string('title', 255)->nullable()->after('change_notes');
            $table->text('description')->nullable()->after('title');
            $table->decimal('bid_value', 18, 2)->nullable()->after('description');
            $table->date('deadline')->nullable()->after('bid_value');
            $table->date('valid_until')->nullable()->after('deadline');
            $table->string('status', 50)->nullable()->after('valid_until');
            $table->smallInteger('revision_number')->default(0)->after('version_number');
        });

        // Backfill existing version 1 records with their parent proposal's current data
        // so old records are not left with empty snapshots.
        DB::statement("
            UPDATE proposal_versions pv
            JOIN proposals p ON p.id = pv.proposal_id
            SET
                pv.title         = p.title,
                pv.description   = p.description,
                pv.bid_value     = p.bid_value,
                pv.deadline      = p.deadline,
                pv.valid_until   = p.valid_until,
                pv.status        = p.status,
                pv.revision_number = CASE WHEN pv.version_number = 1 THEN 0 ELSE pv.version_number - 1 END
            WHERE pv.title IS NULL
        ");
    }

    public function down(): void
    {
        Schema::table('proposal_versions', function (Blueprint $table) {
            $table->dropColumn([
                'title', 'description', 'bid_value', 'deadline',
                'valid_until', 'status', 'revision_number',
            ]);
        });
    }
};
