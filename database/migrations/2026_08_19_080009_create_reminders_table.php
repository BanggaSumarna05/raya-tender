<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tender_id')->nullable()->constrained('tenders')->cascadeOnDelete();
            $table->foreignId('proposal_id')->nullable()->constrained('proposals')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->datetime('reminder_at');
            $table->enum('type', ['deadline', 'follow_up', 'meeting', 'submission', 'custom'])->default('deadline');
            $table->enum('status', ['pending', 'completed', 'dismissed'])->default('pending');
            $table->timestamps();

            $table->index('user_id');
            $table->index('tender_id');
            $table->index('reminder_at');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reminders');
    }
};
