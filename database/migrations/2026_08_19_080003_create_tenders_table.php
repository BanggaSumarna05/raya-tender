<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->restrictOnDelete();
            $table->foreignId('category_id')->constrained('tender_categories')->restrictOnDelete();
            $table->string('code', 50)->unique();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->string('source', 100)->nullable();          // Government, Private, BUMN, etc.
            $table->string('source_reference', 255)->nullable();
            $table->foreignId('pic_id')->constrained('users')->restrictOnDelete();
            $table->string('location', 255)->nullable();
            $table->decimal('estimated_value', 18, 2)->nullable();
            $table->date('received_date')->nullable();
            $table->datetime('submission_deadline')->nullable();
            $table->date('project_start_date')->nullable();
            $table->date('project_end_date')->nullable();
            $table->enum('status', [
                'new', 'qualification', 'proposal', 'submitted', 'won', 'lost', 'no_bid', 'cancelled'
            ])->default('new');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->text('requirements')->nullable();
            $table->text('notes')->nullable();
            $table->text('no_bid_reason')->nullable();
            $table->text('lost_reason')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('priority');
            $table->index('submission_deadline');
            $table->index('client_id');
            $table->index('category_id');
            $table->index('pic_id');
            $table->index('created_by');
            $table->index('code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenders');
    }
};
