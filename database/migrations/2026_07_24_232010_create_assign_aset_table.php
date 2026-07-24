<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assign_aset', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')
                ->constrained('karyawan')
                ->cascadeOnDelete();
            $table->foreignId('aset_id')
                ->constrained('aset')
                ->cascadeOnDelete();
            $table->date('tanggal_assign');
            $table->text('keterangan')->nullable();
            $table->enum('status', [
                'aktif',
                'dikembalikan'
            ])->default('aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assign_aset');
    }
};