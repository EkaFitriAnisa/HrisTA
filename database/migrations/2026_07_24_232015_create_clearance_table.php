<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clearance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')
                ->constrained('karyawan')
                ->cascadeOnDelete();
            $table->unsignedInteger('depart_id')->nullable();
            $table->enum('jenis', [
                'resign',
                'mutasi_internal'
            ]);
            $table->date('tanggal_efektif');
            $table->unsignedInteger('depart_tujuan_id')->nullable();
            $table->text('alasan')->nullable();
            $table->enum('status', [
                'pending',
                'approved',
                'revision',
                'process'
            ])->default('pending');
            $table->timestamps();
            $table->foreign('depart_id')
                ->references('id')
                ->on('depart')
                ->nullOnDelete();
            $table->foreign('depart_tujuan_id')
                ->references('id')
                ->on('depart')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clearance');
    }
};