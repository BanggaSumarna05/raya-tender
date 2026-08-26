<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Improve reminders table:
 *  - Add sent_at   : tracks when notification was delivered (null = not yet sent)
 *  - Add event_key : unique key per event to prevent duplicate sends
 *                    format: "{tender|proposal}_{id}_{event_type}"
 *                    e.g.  : "tender_1_h7", "tender_1_overdue"
 *
 * The event_key unique constraint prevents creating the same reminder twice,
 * which is the duplicate-prevention mechanism.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reminders', function (Blueprint $table) {
            $table->datetime('sent_at')->nullable()->after('status');
            $table->string('event_key', 150)->nullable()->after('sent_at')
                  ->comment('Unique key per reminder event, e.g. tender_1_h7');

            $table->index('sent_at');
            $table->index('event_key');
        });
    }

    public function down(): void
    {
        Schema::table('reminders', function (Blueprint $table) {
            $table->dropIndex(['sent_at']);
            $table->dropIndex(['event_key']);
            $table->dropColumn(['sent_at', 'event_key']);
        });
    }
};
