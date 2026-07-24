<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clearance_aset', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clearance_id')
                ->constrained('clearance')
                ->cascadeOnDelete();
            $table->foreignId('aset_id')
                ->constrained('aset')
                ->cascadeOnDelete();
            $table->enum('status_pengembalian', [
                'pending',
                'returned',
                'missing',
                'damaged'
            ])->default('pending');
            $table->text('catatan')->nullable();
            $table->string('bukti_file')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clearance_aset');
    }
};