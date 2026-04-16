<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('work_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('call_id')->constrained()->cascadeOnDelete();
            $table->foreignId('resolution_type_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('work_started_at')->nullable();
            $table->timestamp('work_completed_at')->nullable();
            $table->timestamps();

            $table->index('created_at');              // date filtering
            $table->index('resolution_type_id');      // grouping/filter
            $table->index(['created_at', 'resolution_type_id']); // compound

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_tasks');
    }
};
