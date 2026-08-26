<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tender_id')->constrained('tenders')->restrictOnDelete();
            $table->string('code', 50)->unique();
            $table->string('title', 255);
            $table->foreignId('pic_id')->constrained('users')->restrictOnDelete();
            $table->text('description')->nullable();
            $table->decimal('bid_value', 18, 2)->nullable();
            $table->enum('status', [
                'draft', 'internal_review', 'final', 'submitted', 'revision', 'won', 'lost', 'cancelled'
            ])->default('draft');
            $table->unsignedSmallInteger('current_version')->default(1);
            $table->date('deadline')->nullable();
            $table->date('valid_until')->nullable();
            $table->datetime('submitted_at')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('tender_id');
            $table->index('pic_id');
            $table->index('deadline');
            $table->index('code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proposals');
    }
};
