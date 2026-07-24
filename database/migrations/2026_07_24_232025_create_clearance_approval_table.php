<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clearance_approval', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clearance_id')
                ->constrained('clearance')
                ->cascadeOnDelete();
            $table->unsignedTinyInteger('step_order');
            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->enum('status', [
                'pending',
                'approved',
                'revision',
                'rejected'
            ])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clearance_approval');
    }
};