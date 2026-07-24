<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('karyawan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('users_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->unsignedInteger('depart_id');
            $table->string('nama', 120);
            $table->string('jabatan', 120);
            $table->string('no_hp', 30);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->foreign('depart_id')
                ->references('id')
                ->on('depart')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('karyawan');
    }
};