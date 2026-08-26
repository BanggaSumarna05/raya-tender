<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add backup_pic_id to tenders and proposals tables.
 * Backup PIC is optional — receives reminders when PIC is unavailable.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenders', function (Blueprint $table) {
            $table->foreignId('backup_pic_id')
                  ->nullable()
                  ->after('pic_id')
                  ->constrained('users')
                  ->nullOnDelete();

            $table->index('backup_pic_id');
        });

        Schema::table('proposals', function (Blueprint $table) {
            $table->foreignId('backup_pic_id')
                  ->nullable()
                  ->after('pic_id')
                  ->constrained('users')
                  ->nullOnDelete();

            $table->index('backup_pic_id');
        });
    }

    public function down(): void
    {
        Schema::table('tenders', function (Blueprint $table) {
            $table->dropForeign(['backup_pic_id']);
            $table->dropIndex(['backup_pic_id']);
            $table->dropColumn('backup_pic_id');
        });

        Schema::table('proposals', function (Blueprint $table) {
            $table->dropForeign(['backup_pic_id']);
            $table->dropIndex(['backup_pic_id']);
            $table->dropColumn('backup_pic_id');
        });
    }
};
